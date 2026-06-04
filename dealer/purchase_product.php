<?php
require_once 'includes/auth_check.php';

if (!isset($_SESSION['rgt_logedin_user_dealer_id'])) {
    die("Unauthorized");
}

$pageTitle  = 'Purchase Goods';
$activePage = 'purchases';

$dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];

/* Fetch companies with balance calculation */
$companies = [];

$sql = "
    SELECT
        c.id,
        c.company_name,
        (
            COALESCE((
                SELECT SUM(ct.amount)
                FROM company_transactions ct
                WHERE ct.company_id = c.id
                AND ct.dealer_id = ?
                AND ct.type = 'DEBIT'
            ), 0)

            -

            COALESCE((
                SELECT SUM(poi.total_price)
                FROM purchase_order_items poi
                JOIN purchase_orders po ON po.id = poi.order_id
                WHERE po.company_id = c.id
                AND po.dealer_id = ?
            ), 0)
        ) AS balance

    FROM companies c
    WHERE c.dealer_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $dealer_id, $dealer_id, $dealer_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $companies[] = $row;
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/header.php'; ?>
</head>
<body class="bg-[var(--bg)]">
<?php require_once 'includes/sidebar.php'; ?>

<div class="md:ml-64 pb-16 md:pb-0">
  <main class="p-6 md:p-8 max-w-4xl">

    <!-- Page Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-semibold text-[var(--text)]">Record Goods Received</h2>
      <p class="text-[var(--subtext)] mt-1 text-sm">Record goods received from company and payment details</p>
    </div>

    <!-- Purchase Form -->
    <form action="process_purchase.php" method="POST" id="purchaseForm">

      <!-- Section 1: Company Selection -->
      <div class="bg-white rounded-2xl border border-[var(--border)] shadow-sm p-6 mb-6">
        <h3 class="text-base font-bold text-[var(--text)] mb-4 flex items-center gap-3">
          <span class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
            <i class="fas fa-building text-[var(--primary)] text-sm"></i>
          </span>
          Select Company
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-[var(--text)] mb-1.5">Company Name *</label>
            <select name="company_id" id="companySelect" required
                    onchange="loadCompanyProducts();loadCompanyBalance()"
                    class="w-full px-4 py-2.5 border border-[var(--border)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">
              <option value="">-- Select Company --</option>
              <?php foreach ($companies as $c) { ?>
                <option value="<?= $c['id'] ?>" data-balance="<?= $c['balance'] ?>">
                  <?= htmlspecialchars($c['company_name']) ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-[var(--text)] mb-1.5">Current Balance with Company</label>
            <div id="companyBalancePreview"
                 class="px-4 py-2.5 bg-gray-50 border border-[var(--border)] rounded-xl text-sm font-medium text-[var(--subtext)]">
              Select a company to see balance
            </div>
          </div>
        </div>
      </div>

      <!-- Section 2: Purchase Details -->
      <div class="bg-white rounded-2xl border border-[var(--border)] shadow-sm p-6 mb-6">
        <h3 class="text-base font-bold text-[var(--text)] mb-4 flex items-center gap-3">
          <span class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
            <i class="fas fa-file-invoice text-[var(--primary)] text-sm"></i>
          </span>
          Purchase Details
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium text-[var(--text)] mb-1.5">Purchase Date *</label>
            <input type="date" name="purchase_date" required value="<?= date('Y-m-d') ?>"
                   class="w-full px-4 py-2.5 border border-[var(--border)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">
          </div>
          <div>
            <label class="block text-sm font-medium text-[var(--text)] mb-1.5">Bill / Reference</label>
            <input type="text" name="bill_number" placeholder="Bill Number"
                   class="w-full px-4 py-2.5 border border-[var(--border)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">
          </div>
          <div>
            <label class="block text-sm font-medium text-[var(--text)] mb-1.5">Purchase Status *</label>
            <select name="purchase_status" required
                    class="w-full px-4 py-2.5 border border-[var(--border)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">
              <option value="REQUESTED">Requested</option>
              <option value="RECEIVED" selected>Received</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Section 3: Goods Order -->
      <div class="bg-white rounded-2xl border border-[var(--border)] shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base font-bold text-[var(--text)] flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
              <i class="fas fa-boxes text-[var(--primary)] text-sm"></i>
            </span>
            Goods Order
          </h3>
          <button type="button" onclick="addProductRow()"
                  class="bg-[var(--primary)] hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <i class="fas fa-plus text-xs"></i> Add Product
          </button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50 border-b border-[var(--border)]">
                <th class="px-4 py-3 text-left font-semibold text-[var(--subtext)]">Product</th>
                <th class="px-4 py-3 text-right font-semibold text-[var(--subtext)]">Price (₹)</th>
                <th class="px-4 py-3 text-right font-semibold text-[var(--subtext)]">Qty</th>
                <th class="px-4 py-3 text-right font-semibold text-[var(--subtext)]">Total (₹)</th>
                <th class="px-4 py-3"></th>
              </tr>
            </thead>
            <tbody id="productsTableBody">
              <tr id="emptyRow">
                <td colspan="5" class="px-4 py-10 text-center text-[var(--subtext)] text-sm">
                  <i class="fas fa-box-open text-2xl mb-2 block opacity-30"></i>
                  Select a company then add products to begin
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Section 4: Payment -->
      <div class="bg-white rounded-2xl border border-[var(--border)] shadow-sm p-6 mb-6">
        <h3 class="text-base font-bold text-[var(--text)] mb-4 flex items-center gap-3">
          <span class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
            <i class="fas fa-wallet text-[var(--primary)] text-sm"></i>
          </span>
          Payment
        </h3>
        <label class="flex items-center gap-3 cursor-pointer select-none">
          <input type="checkbox" id="paymentToggle" onchange="togglePayment()" class="w-4 h-4 rounded">
          <span class="text-sm font-medium text-[var(--text)]">Making payment now?</span>
        </label>
        <div id="paymentFields" class="hidden mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-medium text-[var(--subtext)] mb-1.5">Amount Paid</label>
            <input type="number" name="amount_paid" id="amountPaid" value="0" min="0" step="0.01"
                   class="w-full px-4 py-2.5 border border-[var(--border)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">
          </div>
          <div>
            <label class="block text-xs font-medium text-[var(--subtext)] mb-1.5">Payment Mode</label>
            <select name="payment_mode"
                    class="w-full px-4 py-2.5 border border-[var(--border)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">
              <option>CASH</option><option>UPI</option><option>BANK</option><option>CHEQUE</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-[var(--subtext)] mb-1.5">Payment Date</label>
            <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>"
                   class="w-full px-4 py-2.5 border border-[var(--border)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <button type="submit"
              class="w-full py-3 rounded-xl text-white font-semibold text-sm bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 shadow-lg shadow-green-500/30 transition-all">
        <i class="fas fa-save mr-2"></i>Save Purchase
      </button>

    </form>

    <!-- Purchase Confirmation Modal -->
    <div id="purchaseConfirmModal" class="hidden fixed inset-0 z-[60]">
      <div class="absolute inset-0 bg-black/50"></div>
      <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-[var(--border)] overflow-hidden">
          <div class="px-6 py-4 border-b border-[var(--border)] bg-gradient-to-r from-[var(--primary)] to-indigo-500 text-white">
            <div class="flex items-center justify-between">
              <h3 class="text-base font-semibold flex items-center gap-2">
                <i class="fas fa-clipboard-check"></i> Confirm Purchase
              </h3>
              <button type="button" id="closeConfirmModal"
                      class="w-7 h-7 flex items-center justify-center rounded-lg text-white/80 hover:text-white hover:bg-white/20 transition-colors">
                <i class="fas fa-times text-sm"></i>
              </button>
            </div>
          </div>
          <div class="p-6 max-h-[70vh] overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-5">
              <div class="bg-gray-50 rounded-xl p-3 border border-[var(--border)]">
                <p class="text-xs text-[var(--subtext)] mb-0.5">Company</p>
                <p id="modalCompany" class="text-sm font-semibold text-[var(--text)]">-</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-3 border border-[var(--border)]">
                <p class="text-xs text-[var(--subtext)] mb-0.5">Purchase Date</p>
                <p id="modalDate" class="text-sm font-semibold text-[var(--text)]">-</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-3 border border-[var(--border)]">
                <p class="text-xs text-[var(--subtext)] mb-0.5">Bill / Reference</p>
                <p id="modalBill" class="text-sm font-semibold text-[var(--text)]">-</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-3 border border-[var(--border)]">
                <p class="text-xs text-[var(--subtext)] mb-0.5">Status</p>
                <p id="modalStatus" class="text-sm font-semibold text-[var(--text)]">-</p>
              </div>
            </div>
            <div class="border border-[var(--border)] rounded-xl overflow-hidden mb-5">
              <div class="px-4 py-3 bg-gray-50 border-b border-[var(--border)]">
                <h4 class="text-sm font-semibold text-[var(--text)]">Purchase Items</h4>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-sm">
                  <thead class="bg-gray-50 text-[var(--subtext)]">
                    <tr>
                      <th class="px-4 py-2 text-left font-medium">Product</th>
                      <th class="px-4 py-2 text-right font-medium">Qty</th>
                      <th class="px-4 py-2 text-right font-medium">Price</th>
                      <th class="px-4 py-2 text-right font-medium">Total</th>
                    </tr>
                  </thead>
                  <tbody id="modalItemsBody" class="divide-y divide-gray-100"></tbody>
                </table>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <p class="text-xs text-emerald-700 mb-0.5">Grand Total</p>
                <p id="modalGrandTotal" class="text-xl font-bold text-emerald-800">₹0.00</p>
              </div>
              <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                <p class="text-xs text-indigo-700 mb-0.5">Payment</p>
                <p id="modalPayment" class="text-sm font-semibold text-indigo-900">-</p>
              </div>
            </div>
          </div>
          <div class="px-6 py-4 border-t border-[var(--border)] bg-gray-50 flex justify-end gap-3">
            <button type="button" id="cancelConfirmModal"
                    class="px-4 py-2 rounded-xl border border-[var(--border)] text-[var(--text)] text-sm font-medium hover:bg-gray-100 transition-colors">
              Cancel
            </button>
            <button type="button" id="confirmSubmitPurchase"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-[var(--primary)] to-indigo-500 text-white text-sm font-semibold hover:opacity-95 transition-opacity">
              Confirm &amp; Save
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Theme Alert Modal -->
    <div id="themeAlertModal" class="hidden fixed inset-0 z-[65]">
      <div class="absolute inset-0 bg-black/45"></div>
      <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-[var(--border)] overflow-hidden">
          <div class="px-5 py-4 bg-red-50 border-b border-red-100 flex items-center gap-2 text-red-700">
            <i class="fas fa-exclamation-circle"></i>
            <h3 class="font-semibold text-sm">Action Required</h3>
          </div>
          <div class="p-5">
            <p id="themeAlertMessage" class="text-[var(--text)] text-sm"></p>
          </div>
          <div class="px-5 pb-5 flex justify-end">
            <button type="button" id="closeThemeAlert"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-[var(--primary)] to-indigo-500 text-white text-sm font-semibold">
              OK
            </button>
          </div>
        </div>
      </div>
    </div>

  </main>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
  var rowIndex = 0;
  var products = [];
  var companySelect = document.getElementById('companySelect');
  var purchaseForm  = document.getElementById('purchaseForm');
  var purchaseConfirmModal = document.getElementById('purchaseConfirmModal');
  var themeAlertModal      = document.getElementById('themeAlertModal');
  var modalItemsBody       = document.getElementById('modalItemsBody');
  var allowSubmit = false;

  function loadCompanyProducts() {
    var companyId = companySelect.value;
    fetch('get_products.php?company_id=' + companyId)
      .then(function(r) { return r.json(); })
      .then(function(d) { products = d; });
  }

  function loadCompanyBalance() {
    var opt = companySelect.selectedOptions[0];
    var bal = parseFloat(opt.dataset.balance || 0);
    var preview = document.getElementById('companyBalancePreview');
    if (bal < 0) {
      preview.innerHTML = '<span class="text-red-600 font-semibold">-₹' + Math.abs(bal).toFixed(2) + '</span>';
    } else if (bal > 0) {
      preview.innerHTML = '<span class="text-green-600 font-semibold">+₹' + bal.toFixed(2) + '</span>';
    } else {
      preview.innerHTML = '<span class="text-[var(--subtext)]">₹0.00 — Account settled</span>';
    }
  }

  function addProductRow() {
    var tbody = document.getElementById('productsTableBody');
    var emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    rowIndex++;
    var tr = document.createElement('tr');
    tr.className = 'border-b border-gray-100 hover:bg-gray-50 transition-colors';
    tr.innerHTML =
      '<td class="px-4 py-3">'
    +   '<select name="products[' + rowIndex + '][product_id]" onchange="setPrice(this)"'
    +           ' class="w-full px-3 py-2 border border-[var(--border)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">'
    +     '<option value="">-- Select Product --</option>'
    +     products.map(function(p) { return '<option value="' + p.id + '" data-price="' + p.base_price + '">' + p.product_name + '</option>'; }).join('')
    +   '</select>'
    + '</td>'
    + '<td class="px-4 py-3">'
    +   '<input name="products[' + rowIndex + '][base_price]" readonly'
    +          ' class="price w-full px-3 py-2 border border-[var(--border)] rounded-xl text-sm bg-gray-50 text-right">'
    + '</td>'
    + '<td class="px-4 py-3">'
    +   '<input name="products[' + rowIndex + '][quantity]" type="number" min="1" oninput="calcRow(this)"'
    +          ' class="qty w-full px-3 py-2 border border-[var(--border)] rounded-xl text-sm text-right focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">'
    + '</td>'
    + '<td class="px-4 py-3">'
    +   '<input class="total w-full px-3 py-2 border border-[var(--border)] rounded-xl text-sm bg-gray-50 text-right font-medium" readonly>'
    + '</td>'
    + '<td class="px-4 py-3 text-center">'
    +   '<button type="button" onclick="this.closest(\'tr\').remove()"'
    +           ' class="w-7 h-7 flex items-center justify-center rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors mx-auto">'
    +     '<i class="fas fa-times text-xs"></i>'
    +   '</button>'
    + '</td>';
    tbody.appendChild(tr);
  }

  function setPrice(sel) {
    var price = sel.selectedOptions[0].dataset.price || '';
    sel.closest('tr').querySelector('.price').value = price;
  }

  function calcRow(inp) {
    var tr = inp.closest('tr');
    var p  = parseFloat(tr.querySelector('.price').value) || 0;
    tr.querySelector('.total').value = (p * parseFloat(inp.value || 0)).toFixed(2);
  }

  function togglePayment() {
    document.getElementById('paymentFields').classList.toggle('hidden');
  }

  function buildPurchaseSummary() {
    var companyName    = companySelect.selectedOptions[0] ? companySelect.selectedOptions[0].textContent.trim() : 'Not selected';
    var purchaseDate   = purchaseForm.querySelector('input[name="purchase_date"]') ? purchaseForm.querySelector('input[name="purchase_date"]').value : '-';
    var billNumber     = purchaseForm.querySelector('input[name="bill_number"]') ? purchaseForm.querySelector('input[name="bill_number"]').value.trim() : '-';
    var purchaseStatus = purchaseForm.querySelector('select[name="purchase_status"]') ? purchaseForm.querySelector('select[name="purchase_status"]').value : '-';

    var rows = Array.from(document.querySelectorAll('#productsTableBody tr'));
    var items = [];
    var grandTotal = 0;

    rows.forEach(function(tr) {
      var productSel = tr.querySelector('select[name*="[product_id]"]');
      var priceInput = tr.querySelector('.price');
      var qtyInput   = tr.querySelector('.qty');
      if (!productSel || !qtyInput) return;
      var productName = productSel.selectedOptions[0] ? productSel.selectedOptions[0].textContent.trim() : '';
      var qty   = parseFloat(qtyInput.value || 0);
      var price = parseFloat(priceInput ? priceInput.value : 0) || 0;
      if (!productSel.value || qty <= 0) return;
      var lineTotal = qty * price;
      grandTotal += lineTotal;
      items.push({ productName: productName, qty: qty, price: price, lineTotal: lineTotal });
    });

    var paymentNow  = document.getElementById('paymentToggle').checked;
    var amountPaid  = parseFloat(document.getElementById('amountPaid') ? document.getElementById('amountPaid').value : 0) || 0;
    var paymentMode = purchaseForm.querySelector('select[name="payment_mode"]') ? purchaseForm.querySelector('select[name="payment_mode"]').value : '-';
    var paymentDate = purchaseForm.querySelector('input[name="payment_date"]') ? purchaseForm.querySelector('input[name="payment_date"]').value : '-';

    return {
      companyName: companyName, purchaseDate: purchaseDate, billNumber: billNumber, purchaseStatus: purchaseStatus,
      items: items, grandTotal: grandTotal,
      paymentNow: paymentNow, amountPaid: amountPaid, paymentMode: paymentMode, paymentDate: paymentDate,
      hasItems: items.length > 0
    };
  }

  function showThemeAlert(message) {
    document.getElementById('themeAlertMessage').textContent = message;
    themeAlertModal.classList.remove('hidden');
  }

  function closeThemeAlert() {
    themeAlertModal.classList.add('hidden');
  }

  function openConfirmModal(summaryData) {
    document.getElementById('modalCompany').textContent    = summaryData.companyName;
    document.getElementById('modalDate').textContent       = summaryData.purchaseDate;
    document.getElementById('modalBill').textContent       = summaryData.billNumber;
    document.getElementById('modalStatus').textContent     = summaryData.purchaseStatus;
    document.getElementById('modalGrandTotal').textContent = '₹' + summaryData.grandTotal.toFixed(2);
    document.getElementById('modalPayment').textContent    = summaryData.paymentNow
      ? 'Paid ₹' + summaryData.amountPaid.toFixed(2) + ' via ' + summaryData.paymentMode + ' on ' + summaryData.paymentDate
      : 'No payment made now';

    modalItemsBody.innerHTML = '';
    summaryData.items.forEach(function(item) {
      var tr = document.createElement('tr');
      tr.innerHTML =
        '<td class="px-4 py-2 text-[var(--text)]">' + item.productName + '</td>'
      + '<td class="px-4 py-2 text-right text-[var(--subtext)]">' + item.qty + '</td>'
      + '<td class="px-4 py-2 text-right text-[var(--subtext)]">₹' + item.price.toFixed(2) + '</td>'
      + '<td class="px-4 py-2 text-right font-semibold text-[var(--text)]">₹' + item.lineTotal.toFixed(2) + '</td>';
      modalItemsBody.appendChild(tr);
    });

    purchaseConfirmModal.classList.remove('hidden');
  }

  function closeConfirmModal() {
    purchaseConfirmModal.classList.add('hidden');
  }

  purchaseForm.addEventListener('submit', function(e) {
    if (allowSubmit) { allowSubmit = false; return; }
    e.preventDefault();
    var summaryData = buildPurchaseSummary();
    if (!summaryData.hasItems) {
      showThemeAlert('Please add at least one product with quantity before submitting.');
      return;
    }
    openConfirmModal(summaryData);
  });

  document.getElementById('confirmSubmitPurchase').addEventListener('click', function() {
    allowSubmit = true;
    closeConfirmModal();
    purchaseForm.requestSubmit();
  });

  document.getElementById('cancelConfirmModal').addEventListener('click', closeConfirmModal);
  document.getElementById('closeConfirmModal').addEventListener('click', closeConfirmModal);
  document.getElementById('closeThemeAlert').addEventListener('click', closeThemeAlert);

  purchaseConfirmModal.addEventListener('click', function(e) { if (e.target === purchaseConfirmModal) closeConfirmModal(); });
  themeAlertModal.addEventListener('click', function(e) { if (e.target === themeAlertModal) closeThemeAlert(); });
</script>

</body>
</html>
