/**
 * app.js — DealerPro POS Shared JavaScript
 *
 * This file is loaded by dealer/includes/footer.php on every page.
 *
 * SECTIONS (to be appended in subsequent tasks):
 *  ✅ 1. Sidebar Toggle          — implemented here (task 2.1)
 *  🔜 2. Toast Notifications     — showToast(message, type)
 *  🔜 3. Confirm Modal           — showConfirm(title, message) → Promise
 *  🔜 4. Profile Dropdown Toggle — #profileDropdown / #profileMenu
 *  🔜 5. DataTables Auto-Init    — $('.datanew').DataTable(...)
 *
 * Requirements addressed: 2.3, 2.4, 2.6
 */

/* ==========================================================================
   1. SIDEBAR TOGGLE
   ========================================================================== */

/**
 * handleSidebarResize — called on load and on every window resize.
 *
 * ≥ 768px (md breakpoint): sidebar is always visible, backdrop always hidden.
 * < 768px: sidebar starts off-screen (overlay mode).
 */
function handleSidebarResize() {
  const sidebar  = document.getElementById('sidebar');
  const backdrop = document.getElementById('sidebarBackdrop');

  if (!sidebar) return;

  if (window.innerWidth >= 768) {
    // Desktop: pin sidebar open, ensure backdrop is hidden
    sidebar.classList.remove('-translate-x-full');
    if (backdrop) backdrop.classList.add('hidden');
  } else {
    // Mobile: slide sidebar off-screen (overlay closed by default)
    sidebar.classList.add('-translate-x-full');
  }
}

// Wire up click handlers after the DOM is ready
document.addEventListener('DOMContentLoaded', function () {
  var menuToggle      = document.getElementById('menuToggle');
  var sidebar         = document.getElementById('sidebar');
  var sidebarBackdrop = document.getElementById('sidebarBackdrop');

  // Hamburger button → open sidebar overlay + show backdrop
  if (menuToggle) {
    menuToggle.addEventListener('click', function () {
      if (sidebar)         sidebar.classList.remove('-translate-x-full');
      if (sidebarBackdrop) sidebarBackdrop.classList.remove('hidden');
    });
  }

  // Backdrop tap → close sidebar overlay + hide backdrop
  if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener('click', function () {
      if (sidebar)         sidebar.classList.add('-translate-x-full');
      sidebarBackdrop.classList.add('hidden');
    });
  }
});

// Respond to viewport changes (e.g. rotating device, resizing browser)
window.addEventListener('resize', handleSidebarResize);

// Run immediately so the sidebar starts in the correct state on page load
handleSidebarResize();

/* ==========================================================================
   2. TOAST NOTIFICATIONS
   ========================================================================== */

var _toastQueue = [];
var MAX_TOASTS  = 3;

/**
 * showToast(message, type)
 * Renders a non-blocking toast at the bottom-right of the screen.
 * type: 'success' | 'error' | 'warning' | 'info'
 * Auto-dismisses after 3 seconds. Max 3 visible at once.
 */
function showToast(message, type) {
  type = type || 'info';

  var colorMap = {
    success: 'bg-green-500',
    error:   'bg-red-500',
    warning: 'bg-amber-500',
    info:    'bg-blue-500'
  };
  var bgClass = colorMap[type] || colorMap.info;

  // Enforce max-3: dismiss oldest if already at limit
  if (_toastQueue.length >= MAX_TOASTS) {
    _dismissToast(_toastQueue[0]);
  }

  var container = document.getElementById('toastContainer');
  if (!container) return;

  var toast = document.createElement('div');
  toast.className = bgClass + ' text-white text-sm font-medium px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 min-w-[220px] max-w-xs transition-all duration-300 opacity-0 translate-y-2';
  toast.innerHTML = '<span class="flex-1">' + _escapeHtml(message) + '</span>'
    + '<button onclick="this.parentElement.remove()" class="ml-2 text-white/80 hover:text-white text-lg leading-none">&times;</button>';

  container.appendChild(toast);
  _toastQueue.push(toast);

  // Animate in
  requestAnimationFrame(function () {
    requestAnimationFrame(function () {
      toast.classList.remove('opacity-0', 'translate-y-2');
    });
  });

  // Auto-dismiss after 3s
  setTimeout(function () {
    _dismissToast(toast);
  }, 3000);
}

function _dismissToast(toast) {
  if (!toast) return;
  var idx = _toastQueue.indexOf(toast);
  if (idx !== -1) _toastQueue.splice(idx, 1);
  if (!toast.parentElement) return;
  toast.classList.add('opacity-0', 'translate-y-2');
  setTimeout(function () {
    if (toast.parentElement) toast.parentElement.removeChild(toast);
  }, 300);
}

function _escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

// Expose globally
window.showToast = showToast;

/* ==========================================================================
   3. CONFIRM MODAL
   ========================================================================== */

/**
 * showConfirm(title, message)
 * Shows a custom confirm modal. Returns a Promise that resolves to
 * true (Confirm clicked) or false (Cancel clicked).
 */
function showConfirm(title, message) {
  return new Promise(function (resolve) {
    var modal      = document.getElementById('confirmModal');
    var titleEl    = document.getElementById('confirmTitle');
    var messageEl  = document.getElementById('confirmMessage');
    var confirmBtn = document.getElementById('confirmOkBtn');
    var cancelBtn  = document.getElementById('confirmCancelBtn');

    if (!modal) { resolve(false); return; }

    if (titleEl)   titleEl.textContent   = title   || 'Confirm';
    if (messageEl) messageEl.textContent = message || 'Are you sure?';

    modal.classList.remove('hidden');

    function cleanup() {
      modal.classList.add('hidden');
      if (confirmBtn) confirmBtn.removeEventListener('click', onConfirm);
      if (cancelBtn)  cancelBtn.removeEventListener('click', onCancel);
    }

    function onConfirm() { cleanup(); resolve(true);  }
    function onCancel()  { cleanup(); resolve(false); }

    if (confirmBtn) confirmBtn.addEventListener('click', onConfirm);
    if (cancelBtn)  cancelBtn.addEventListener('click', onCancel);
  });
}

window.showConfirm = showConfirm;

/* ==========================================================================
   4. PROFILE DROPDOWN TOGGLE
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
  var profileDropdown = document.getElementById('profileDropdown');
  var profileMenu     = document.getElementById('profileMenu');

  if (profileDropdown && profileMenu) {
    profileDropdown.addEventListener('click', function (e) {
      e.stopPropagation();
      profileMenu.classList.toggle('hidden');
    });

    document.addEventListener('click', function () {
      profileMenu.classList.add('hidden');
    });
  }
});

/* ==========================================================================
   5. DATATABLES — shared options & AJAX table initialisation
   ========================================================================== */

/** Shared DataTables language block */
var _dtLang = {
  search:     'Search:',
  lengthMenu: 'Show _MENU_ entries',
  info:       'Showing _START_ to _END_ of _TOTAL_ entries',
  infoEmpty:  'No entries to show',
  paginate:   { previous: '&lsaquo;', next: '&rsaquo;' },
  loadingRecords: 'Loading…',
  processing: '<span class="text-indigo-600 text-sm">Loading…</span>',
  emptyTable: 'No data available'
};

/**
 * _dtRender — show a cell-count loading skeleton while the AJAX call fires.
 * Inserts one row with animated pulse bars.
 */
function _dtLoadingSkeleton(tbodySelector, cols) {
  var bar  = '<div class="h-3 bg-gray-200 rounded animate-pulse"></div>';
  var cell = '<td class="py-3 px-4">' + bar + '</td>';
  var row  = '<tr>' + cell.repeat(cols) + '</tr>';
  var rows = '';
  for (var i = 0; i < 5; i++) rows += row;
  var el = document.querySelector(tbodySelector);
  if (el) el.innerHTML = rows;
}

/* ── Sales table ──────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  var $salesTable = jQuery('#salesTable');
  if (!$salesTable.length) return;

  $salesTable.DataTable({
    ajax: { url: 'ajax/get_sales.php', dataSrc: 'data' },
    pageLength: 10,
    language: _dtLang,
    order: [],
    columns: [
      { data: 'sr' },
      { data: 'bill_no' },
      { data: 'sale_date' },
      { data: 'billing_type' },
      { data: 'item_count', render: function (d) { return d + ' item(s)'; } },
      { data: 'total_qty' },
      { data: 'bill_amount', render: function (d) { return '₹' + d; } },
      { data: 'total_amount', render: function (d) { return '₹' + d; } },
      {
        data: null,
        orderable: false,
        render: function (data, type, row) {
          return '<div class="flex items-center gap-1">'
            + '<button onclick="window.location.href=\'sale_details.php?id=' + row.sale_id + '\'" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors"><i class="fas fa-eye"></i></button>'
            + '<button onclick="shareBillToWhatsapp(' + row.sale_id + ',\'' + row.mobile_no + '\')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition-colors" title="Share bill on WhatsApp"><i class="fab fa-whatsapp"></i></button>'
            + '<button onclick="deleteSale(' + row.sale_id + ')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition-colors"><i class="fas fa-trash"></i></button>'
            + '</div>';
        }
      }
    ]
  });
});

/* ── Purchases table ──────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  var $tbl = jQuery('#purchasesTable');
  if (!$tbl.length) return;

  $tbl.DataTable({
    ajax: { url: 'ajax/get_purchases.php', dataSrc: 'data' },
    pageLength: 10,
    language: _dtLang,
    order: [],
    columns: [
      { data: 'order_date' },
      { data: 'product_name' },
      { data: 'quantity' },
      { data: 'base_price', render: function (d) { return '₹' + d; } },
      { data: 'total_price', render: function (d) { return '₹' + d; } },
      { data: 'paid_amount', render: function (d) { return '<span class="text-green-600 font-medium">₹' + d + '</span>'; } },
      {
        data: null,
        orderable: false,
        render: function (data, type, row) {
          var badge = '';
          if (row.status === 'RECEIVED') {
            badge = '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">RECEIVED</span>';
          } else if (row.status === 'REQUESTED') {
            badge = '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">REQUESTED</span>'
              + '<button onclick="markAsReceived(' + row.order_id + ')" class="ml-2 px-2 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded transition-colors">Mark as Received</button>';
          } else if (row.status === 'CANCELLED') {
            badge = '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">CANCELLED</span>';
          } else {
            badge = '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">' + row.status + '</span>';
          }
          return badge;
        }
      }
    ]
  });
});

/* ── Products table ───────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  var $tbl = jQuery('#productsTable');
  if (!$tbl.length) return;

  $tbl.DataTable({
    ajax: { url: 'ajax/get_products.php', dataSrc: 'data' },
    pageLength: 10,
    language: _dtLang,
    columns: [
      { data: 'id' },
      { data: 'product_name' },
      {
        data: 'category',
        render: function (d) {
          return d
            ? '<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">' + d + '</span>'
            : '<span class="text-gray-400 text-xs">—</span>';
        }
      },
      { data: 'company_name' },
      { data: 'assurance' },
      { data: 'validity' },
      { data: 'base_price', render: function (d) { return '₹' + d; } },
      { data: 'selling_price', render: function (d) { return '₹' + d; } },
      { data: 'current_stock' },
      { data: 'hsn' },
      { data: 'gst' },
      { data: 'barcode' },
      {
        data: null,
        orderable: false,
        render: function (data, type, row) {
          var r = row._raw;
          return '<div class="flex items-center gap-2">'
            + '<button onclick=\'openEditProductModal(' + JSON.stringify(r).replace(/'/g, "\\'") + ')\' class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"><i class="fas fa-edit"></i> Edit</button>'
            + '<button onclick="window.location.href=\'price_history.php?id=' + r.productId + '\'" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition-colors"><i class="fas fa-chart-line"></i> Price History</button>'
            + '</div>';
        }
      }
    ]
  });
});

/* ── Companies table ──────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  var $tbl = jQuery('#companiesTable');
  if (!$tbl.length) return;

  $tbl.DataTable({
    ajax: { url: 'ajax/get_companies.php', dataSrc: 'data' },
    pageLength: 10,
    language: _dtLang,
    columns: [
      { data: 'id' },
      { data: 'dealer_id' },
      { data: 'company_name' },
      { data: 'contact_person' },
      { data: 'phone' },
      { data: 'email' },
      { data: 'balance_html' },
      {
        data: null,
        orderable: false,
        render: function (data, type, row) {
          var r = row._raw;
          return '<div class="flex items-center gap-2">'
            + '<button onclick="openEditProductModal(' + r.id + ',\'' + r.company_name.replace(/'/g, "\\'") + '\',\'' + r.contact_person.replace(/'/g, "\\'") + '\',\'' + r.phone.replace(/'/g, "\\'") + '\',\'' + r.email.replace(/'/g, "\\'") + '\')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"><i class="fas fa-edit"></i> Edit</button>'
            + '</div>';
        }
      }
    ]
  });
});

/* ── Staff table ──────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  var $tbl = jQuery('#staffTable');
  if (!$tbl.length) return;

  $tbl.DataTable({
    ajax: { url: 'ajax/get_staff.php', dataSrc: 'data' },
    pageLength: 10,
    language: _dtLang,
    columns: [
      { data: 'name' },
      { data: 'email' },
      {
        data: 'role_cls',
        render: function (d, type, row) {
          return '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full ' + d + '">' + row.role + '</span>';
        }
      },
      {
        data: null,
        orderable: false,
        render: function (data, type, row) {
          return '<div class="flex items-center gap-2">'
            + '<button data-id="' + row.id + '" data-name="' + row.name + '" data-email="' + row.email + '" data-role="' + row.role + '" onclick="openEditStaffModal(this)" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"><i class="fas fa-edit"></i> Edit</button>'
            + '<button onclick="disableStaff(' + row.id + ')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition-colors"><i class="fas fa-ban"></i> Disable</button>'
            + '</div>';
        }
      }
    ]
  });
});


/* ==========================================================================
   6. DASHBOARD — Async stat cards & mini-tables
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
  if (!document.getElementById('dashboardStats')) return;

  fetch('ajax/get_dashboard.php')
    .then(function (r) { return r.json(); })
    .then(function (data) {
      // Stat cards
      document.getElementById('dashExpense').textContent     = '₹ ' + data.stats.expense;
      document.getElementById('dashRevenue').textContent     = '₹ ' + data.stats.revenue;
      document.getElementById('dashTotalSales').textContent  = data.stats.total_sales;
      document.getElementById('dashTotalProfit').textContent = '₹ ' + data.stats.total_profit;

      // Least stock
      var tbody1 = document.getElementById('dashLeastStockBody');
      if (tbody1) {
        tbody1.innerHTML = data.least_stock.length === 0
          ? '<tr><td colspan="4" class="py-8 text-center text-sm text-[var(--subtext)]">No products found</td></tr>'
          : data.least_stock.map(function (p) {
              var cls = p.current_stock <= 5 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700';
              return '<tr class="hover:bg-gray-50 transition-colors">'
                + '<td class="py-3 px-4 text-sm font-medium text-[var(--text)]">' + p.product_name + '</td>'
                + '<td class="py-3 px-4 text-sm text-[var(--subtext)]">' + p.company_name + '</td>'
                + '<td class="py-3 px-4"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold ' + cls + '">' + p.current_stock + '</span></td>'
                + '<td class="py-3 px-4 text-sm text-[var(--text)]">₹ ' + p.base_price + '</td>'
                + '</tr>';
            }).join('');
      }

      // Recent purchases
      var tbody2 = document.getElementById('dashRecentPurchasesBody');
      if (tbody2) {
        tbody2.innerHTML = data.recent_purchases.length === 0
          ? '<tr><td colspan="5" class="py-8 text-center text-sm text-[var(--subtext)]">No purchases found</td></tr>'
          : data.recent_purchases.map(function (p) {
              return '<tr class="hover:bg-gray-50 transition-colors">'
                + '<td class="py-3 px-4 text-sm font-medium text-[var(--text)]">' + p.company_name + '</td>'
                + '<td class="py-3 px-4 text-sm text-[var(--subtext)]">' + p.product_name + '</td>'
                + '<td class="py-3 px-4 text-sm text-[var(--text)]">' + p.quantity + '</td>'
                + '<td class="py-3 px-4 text-sm text-[var(--text)]">₹ ' + p.total_price + '</td>'
                + '<td class="py-3 px-4 text-sm text-[var(--subtext)] whitespace-nowrap">' + p.order_date + '</td>'
                + '</tr>';
            }).join('');
      }

      // Best sellers
      var tbody3 = document.getElementById('dashBestSellersBody');
      if (tbody3) {
        tbody3.innerHTML = data.best_sellers.length === 0
          ? '<tr><td colspan="4" class="py-8 text-center text-sm text-[var(--subtext)]">No sales data found</td></tr>'
          : data.best_sellers.map(function (p) {
              var rankColors = ['bg-yellow-100 text-yellow-700','bg-gray-100 text-gray-600','bg-orange-100 text-orange-700'];
              var rankColor  = rankColors[p.rank - 1] || 'bg-gray-50 text-gray-500';
              return '<tr class="hover:bg-gray-50 transition-colors">'
                + '<td class="py-3 px-4"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold ' + rankColor + '">' + p.rank + '</span></td>'
                + '<td class="py-3 px-4 text-sm font-medium text-[var(--text)]">' + p.company_name + '</td>'
                + '<td class="py-3 px-4 text-sm text-[var(--subtext)]">' + p.product_name + '</td>'
                + '<td class="py-3 px-4 text-sm font-semibold text-[var(--primary)]">' + p.total_qty_sold + '</td>'
                + '</tr>';
            }).join('');
      }

      // Recent sales
      var tbody4 = document.getElementById('dashRecentSalesBody');
      if (tbody4) {
        tbody4.innerHTML = data.recent_sales.length === 0
          ? '<tr><td colspan="4" class="py-8 text-center text-sm text-[var(--subtext)]">No recent sales found</td></tr>'
          : data.recent_sales.map(function (s) {
              return '<tr class="hover:bg-gray-50 transition-colors">'
                + '<td class="py-3 px-4"><span class="text-xs font-mono font-semibold text-[var(--primary)]">' + s.bill_no + '</span></td>'
                + '<td class="py-3 px-4 text-sm text-[var(--subtext)] whitespace-nowrap">' + s.sale_date + '</td>'
                + '<td class="py-3 px-4 text-sm text-[var(--text)]">' + s.product_name + '</td>'
                + '<td class="py-3 px-4 text-sm font-medium text-[var(--text)]">' + s.quantity + '</td>'
                + '</tr>';
            }).join('');
      }
    })
    .catch(function () {
      showToast('Failed to load dashboard data', 'error');
    });
});
