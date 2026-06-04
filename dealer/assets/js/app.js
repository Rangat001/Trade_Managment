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
   5. DATATABLES AUTO-INIT
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
  if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
    jQuery('.datanew').DataTable({
      pageLength: 25,
      language: {
        search:      'Search:',
        lengthMenu:  'Show _MENU_ entries',
        info:        'Showing _START_ to _END_ of _TOTAL_ entries',
        paginate: {
          previous: '&lsaquo;',
          next:     '&rsaquo;'
        }
      }
    });
  }
});
