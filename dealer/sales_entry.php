<?php
require_once 'includes/auth_check.php';
$pageTitle  = 'New Sale';
$activePage = 'sales';

// Fetch products with category, grouped
$products = [];
$stmt = $conn->prepare("
    SELECT id, product_name, selling_price, current_stock, category
    FROM products
    WHERE dealer_id = ?
    ORDER BY CASE WHEN (category IS NULL OR category = '') THEN 1 ELSE 0 END, category, product_name
");
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

// Group by category (empty/null → '__none__')
$grouped = [];
foreach ($products as $p) {
    $cat = (isset($p['category']) && trim($p['category']) !== '') ? trim($p['category']) : '__none__';
    $grouped[$cat][] = $p;
}
$categories = array_keys($grouped);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/header.php'; ?>
<style>
/* ── POS Touch Layout ─────────────────────────────────────── */
.pos-wrap {
    display: flex;
    height: calc(100vh - 57px); /* subtract topbar */
    overflow: hidden;
}
/* Left: product grid */
.pos-left {
    flex: 1 1 0%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--bg);
}
/* Right: bill panel */
.pos-right {
    width: 340px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    background: #fff;
    border-left: 1px solid var(--border);
}
@media (max-width: 767px) {
    .pos-wrap { flex-direction: column; height: auto; }
    .pos-right { width: 100%; border-left: none; border-top: 1px solid var(--border); }
}

/* Product card */
.prod-card {
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 14px 10px 12px;
    cursor: pointer;
    transition: border-color .15s, box-shadow .15s, transform .1s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    text-align: center;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    position: relative;
    overflow: hidden;
}
.prod-card:hover  { border-color: var(--primary); box-shadow: 0 4px 16px rgba(79,70,229,.12); }
.prod-card:active { transform: scale(.96); }
.prod-card.out-of-stock { opacity: .5; cursor: not-allowed; }
.prod-card .prod-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: var(--primary-light);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    color: var(--primary);
}
.prod-card .prod-name {
    font-size: .78rem;
    font-weight: 600;
    color: var(--text);
    line-height: 1.3;
    max-height: 2.6em;
    overflow: hidden;
}
.prod-card .prod-price {
    font-size: .85rem;
    font-weight: 700;
    color: var(--primary);
}
.prod-card .prod-stock {
    font-size: .68rem;
    color: var(--subtext);
}
/* Cart item row */
.cart-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-bottom: 1px solid var(--border);
    animation: slideIn .15s ease;
}
@keyframes slideIn {
    from { opacity:0; transform: translateX(10px); }
    to   { opacity:1; transform: translateX(0); }
}
.cart-row .cart-name {
    flex: 1;
    font-size: .8rem;
    font-weight: 600;
    color: var(--text);
    line-height: 1.3;
}
.cart-row .cart-price {
    font-size: .75rem;
    color: var(--subtext);
}
.qty-btn {
    width: 28px; height: 28px;
    border-radius: 8px;
    border: 1.5px solid var(--border);
    background: #fff;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: .9rem;
    font-weight: 700;
    color: var(--text);
    transition: all .12s;
    flex-shrink: 0;
}
.qty-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.qty-display {
    min-width: 28px;
    text-align: center;
    font-size: .85rem;
    font-weight: 700;
    color: var(--text);
}
.cart-total {
    font-size: .82rem;
    font-weight: 700;
    color: var(--text);
    min-width: 52px;
    text-align: right;
}
/* Search bar */
.pos-search {
    width: 100%;
    padding: .55rem 1rem .55rem 2.5rem;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: .875rem;
    color: var(--text);
    background: #fff;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.pos-search:focus { border-color: var(--primary); box-shadow: 0 0 0 3px #EEF2FF; }
/* Pay chip */
.pay-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .3rem .75rem;
    border-radius: 9999px;
    border: 1.5px solid var(--border);
    font-size: .75rem; font-weight: 600;
    cursor: pointer; transition: all .12s;
    background: #fff; color: var(--subtext);
    user-select: none;
}
.pay-chip.active { border-color: var(--primary); background: var(--primary-light); color: var(--primary); }

/* Category view */
.cat-view-card {
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 16px;
    padding: 20px 12px 16px;
    cursor: pointer;
    transition: border-color .15s, box-shadow .15s, transform .1s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    text-align: center;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}
.cat-view-card:hover  { border-color: var(--primary); box-shadow: 0 6px 20px rgba(79,70,229,.14); }
.cat-view-card:active { transform: scale(.95); }
.cat-view-card .cat-icon {
    width: 56px; height: 56px;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
}
.cat-view-card .cat-label {
    font-size: .82rem; font-weight: 700;
    color: var(--text); line-height: 1.3;
}
.cat-view-card .cat-count-pill {
    font-size: .68rem; font-weight: 600;
    color: var(--subtext);
    background: #F3F4F6;
    padding: 2px 8px; border-radius: 999px;
}
</style>
</head>
<body class="bg-[var(--bg)]">
<?php require_once 'includes/sidebar.php'; ?>

<!-- ═══ POS WRAPPER (full-height, no scroll on outer) ═══════ -->
<div class="md:ml-64">

<?php if (!empty($_SESSION['sale_error'])): ?>
<div class="mx-4 mt-3 p-3 rounded-xl border border-red-300 bg-red-50 text-red-800 flex items-start gap-3 text-sm">
    <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 flex-shrink-0"></i>
    <div><strong>Error:</strong> <?= htmlspecialchars($_SESSION['sale_error']) ?></div>
    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600">&times;</button>
</div>
<?php unset($_SESSION['sale_error']); endif; ?>

<div class="pos-wrap">

    <!-- ══════════════════════════════════════════════════════
         LEFT — Product Catalogue
         ══════════════════════════════════════════════════════ -->
    <div class="pos-left">

        <!-- Top bar -->
        <div class="flex items-center gap-3 px-4 py-3 bg-white border-b border-[var(--border)] flex-shrink-0">
            <!-- Left: back-to-sales OR back-to-categories -->
            <button id="topBarBack" onclick="goBackToCategories()"
                    class="w-9 h-9 flex items-center justify-center rounded-xl border border-[var(--border)] text-[var(--subtext)] hover:bg-gray-50 transition-colors flex-shrink-0 hidden">
                <i class="fas fa-arrow-left text-sm"></i>
            </button>
            <a id="topBarBackSales" href="sales.php"
               class="w-9 h-9 flex items-center justify-center rounded-xl border border-[var(--border)] text-[var(--subtext)] hover:bg-gray-50 transition-colors flex-shrink-0">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>

            <!-- Title / breadcrumb -->
            <div class="flex-1 min-w-0">
                <div id="viewTitle" class="text-sm font-semibold text-[var(--text)] truncate">
                    All Categories
                </div>
            </div>

            <!-- Search (only in product view) -->
            <div id="searchWrap" class="relative hidden" style="width:180px">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[var(--subtext)] text-xs pointer-events-none"></i>
                <input type="text" id="productSearch" placeholder="Search…" class="pos-search" oninput="filterProducts(this.value)">
            </div>

            <span id="topBarCount" class="text-xs text-[var(--subtext)] whitespace-nowrap hidden sm:block">
                <?= count($categories) ?> categories
            </span>
        </div>

        <!-- Scrollable content area -->
        <div class="flex-1 overflow-y-auto p-4" id="scrollArea">

            <!-- ══ CATEGORY VIEW (default) ══════════════════════ -->
            <div id="categoryView">
                <?php if (empty($products)): ?>
                <div class="py-16 text-center text-[var(--subtext)]">
                    <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                    <p class="font-medium">No products found</p>
                    <p class="text-xs mt-1">Add products first from the Products page</p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3">
                    <?php
                    $catPalette = [
                        ['bg-indigo-100 text-indigo-600','fa fa-tag'],
                        ['bg-emerald-100 text-emerald-600','fa fa-tag'],
                        ['bg-amber-100 text-amber-600','fa fa-tag'],
                        ['bg-rose-100 text-rose-600','fa fa-tag'],
                        ['bg-sky-100 text-sky-600','fa fa-tag'],
                        ['bg-purple-100 text-purple-600','fa fa-tag'],
                        ['bg-orange-100 text-orange-600','fa fa-tag'],
                        ['bg-teal-100 text-teal-600','fa fa-tag'],
                    ];
                    $ci = 0;
                    foreach ($grouped as $cat => $catProducts):
                        $catLabel  = ($cat === '__none__') ? 'No Category' : $cat;
                        $palette   = $catPalette[$ci % count($catPalette)];
                        $iconClass = 'fas fa-tag';
                        $colorCls  = $palette[0];
                        $ci++;
                    ?>
                    <div class="cat-view-card"
                         onclick="openCategory('<?= htmlspecialchars($catLabel, ENT_QUOTES) ?>', <?= count($catProducts) ?>)">
                        <div class="cat-icon <?= $colorCls ?>">
                            <i class="<?= $iconClass ?>" ></i>
                        </div>
                        <div class="cat-label"><?= htmlspecialchars($catLabel) ?></div>
                        <div class="cat-count-pill"><?= count($catProducts) ?> items</div>
                    </div>
                    <?php $ci++; endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══ PRODUCT VIEW (shown after category tap) ══════ -->
            <div id="productView" class="hidden">
                <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3">
                    <!-- Populated by JS -->
                </div>
                <div id="noResults" class="hidden py-16 text-center text-[var(--subtext)]">
                    <i class="fas fa-search text-3xl mb-3 text-gray-300"></i>
                    <p class="font-medium text-sm">No products match your search</p>
                </div>
            </div>

        </div>

    </div>
    <!-- /LEFT -->

    <!-- ══════════════════════════════════════════════════════
         RIGHT — Bill / Cart Panel
         ══════════════════════════════════════════════════════ -->
    <div class="pos-right">

        <!-- Bill header -->
        <div class="px-4 py-3 border-b border-[var(--border)] flex-shrink-0">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-receipt text-[var(--primary)]"></i>
                    <span class="font-bold text-[var(--text)] text-sm">Current Bill</span>
                    <span id="cartBadge" class="hidden text-[10px] font-bold bg-[var(--primary)] text-white px-1.5 py-0.5 rounded-full">0</span>
                </div>
                <button type="button" onclick="clearCart()"
                        class="text-xs text-red-400 hover:text-red-600 transition-colors flex items-center gap-1">
                    <i class="fas fa-trash-alt text-xs"></i> Clear
                </button>
            </div>

            <!-- Sale meta: date + billing type in one row -->
            <div class="grid grid-cols-2 gap-2 mb-2">
                <div>
                    <label class="block text-[10px] font-semibold text-[var(--subtext)] uppercase mb-1">Date</label>
                    <input type="date" id="saleDateDisplay" class="w-full text-xs border border-[var(--border)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary)]">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-[var(--subtext)] uppercase mb-1">Billing</label>
                    <select id="billingTypeDisplay" class="w-full text-xs border border-[var(--border)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary)]">
                        <option value="GST">GST</option>
                        <option value="NON-GST">NON-GST</option>
                    </select>
                </div>
            </div>

            <!-- Payment mode chips -->
            <div class="flex gap-1.5 flex-wrap mb-2">
                <button type="button" class="pay-chip active" data-mode="CASH" onclick="selectPayMode('CASH')">
                    <i class="fas fa-money-bill-wave"></i> CASH
                </button>
                <button type="button" class="pay-chip" data-mode="UPI" onclick="selectPayMode('UPI')">
                    <i class="fas fa-mobile-alt"></i> UPI
                </button>
                <button type="button" class="pay-chip" data-mode="CARD" onclick="selectPayMode('CARD')">
                    <i class="fas fa-credit-card"></i> CARD
                </button>
            </div>

            <!-- Mobile (optional) -->
            <div class="relative">
                <i class="fas fa-phone absolute left-2.5 top-1/2 -translate-y-1/2 text-[var(--subtext)] text-[10px] pointer-events-none"></i>
                <input type="text" inputmode="numeric" pattern="[0-9]*" id="mobileDisplay" placeholder="Customer mobile (optional)"
                       class="w-full text-xs border border-[var(--border)] rounded-lg pl-7 pr-3 py-1.5 focus:outline-none focus:border-[var(--primary)]">
            </div>
        </div>

        <!-- Cart items (scrollable) -->
        <div class="flex-1 overflow-y-auto" id="cartList">
            <!-- Empty cart state — always stays in DOM, toggled via style.display -->
            <div id="cartEmpty" style="display:flex" class="flex-col items-center justify-center h-full py-10 text-[var(--subtext)]">
                <i class="fas fa-shopping-cart text-3xl mb-3 text-gray-200"></i>
                <p class="text-sm font-medium text-gray-400">Cart is empty</p>
                <p class="text-xs mt-1 text-gray-300">Tap a product to add it</p>
            </div>
        </div>

        <!-- Bill totals + actions -->
        <div class="flex-shrink-0 border-t border-[var(--border)] bg-white">

            <!-- Totals -->
            <div class="px-4 py-3 space-y-1.5">
                <div class="flex justify-between text-xs text-[var(--subtext)]">
                    <span>Items</span>
                    <span id="summItems" class="font-semibold text-[var(--text)]">0</span>
                </div>
                <div class="flex justify-between text-xs text-[var(--subtext)]">
                    <span>Total Qty</span>
                    <span id="summQty" class="font-semibold text-[var(--text)]">0</span>
                </div>
                <div class="flex justify-between text-xs text-[var(--subtext)]">
                    <span>Subtotal</span>
                    <span id="summSubtotal" class="font-semibold text-[var(--text)]">₹0.00</span>
                </div>
                <div class="flex justify-between items-center text-xs text-[var(--subtext)]">
                    <span>Discount (₹)</span>
                    <input type="text" inputmode="numeric" pattern="[0-9]*"  id="discountInput" min="0" step="0.01" value="0"
                           oninput="recalcTotals()"
                           class="w-24 text-right text-xs border border-[var(--border)] rounded-lg px-2 py-1 focus:outline-none focus:border-[var(--primary)] font-semibold">
                </div>
                <div class="flex justify-between items-center pt-1 border-t border-dashed border-[var(--border)]">
                    <span class="text-sm font-bold text-[var(--text)] uppercase tracking-wide">Total</span>
                    <span id="summTotal" class="text-xl font-extrabold text-[var(--primary)]">₹0.00</span>
                </div>
            </div>

            <!-- Delivery status -->
            <div class="px-4 pb-2">
                <select id="deliveryStatusDisplay" class="w-full text-xs border border-[var(--border)] rounded-lg px-2 py-1.5 focus:outline-none focus:border-[var(--primary)]">
                    <option value="ON-HAND">On-Hand (Delivered)</option>
                    <option value="PENDING">Pending Delivery</option>
                    <option value="DELIVERED">Delivered</option>
                </select>
            </div>

            <!-- Action buttons -->
            <div class="px-4 pb-4 space-y-2">
                <button type="button" onclick="submitSale('save')"
                        class="w-full flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold rounded-xl shadow shadow-green-300 transition-all text-sm">
                    <i class="fas fa-check-circle"></i> Save Sale
                </button>
                <button type="button" onclick="submitSale('save_print')"
                        class="w-full flex items-center justify-center gap-2 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow shadow-blue-300 transition-all text-sm">
                    <i class="fas fa-print"></i> Save &amp; Print
                </button>
            </div>
        </div>

    </div>
    <!-- /RIGHT -->

</div><!-- /pos-wrap -->
</div><!-- /md:ml-64 -->

<!-- Hidden form — submitted programmatically -->
<form action="process_sale.php" method="POST" id="salesForm" style="display:none">
    <input type="hidden" name="sale_date"        id="f_sale_date">
    <input type="hidden" name="billing_type"     id="f_billing_type">
    <input type="hidden" name="payment_mode"     id="f_payment_mode">
    <input type="hidden" name="delivery_status"  id="f_delivery_status">
    <input type="hidden" name="mobile"           id="f_mobile">
    <input type="hidden" name="discount_amount"  id="f_discount">
    <input type="hidden" name="final_amount"     id="f_final_amount">
    <!-- product rows injected by JS -->
    <div id="formProductRows"></div>
</form>

<?php require_once 'includes/footer.php'; ?>

<script>
/* ================================================================
   TOUCH POS — Sales Entry
   Category-first navigation + Save&Print stays on page
   ================================================================ */

// All products from PHP — keyed by category
var allGrouped = <?php
    $js_grouped = [];
    foreach ($grouped as $cat => $catProducts) {
        $catLabel = ($cat === '__none__') ? 'No Category' : $cat;
        foreach ($catProducts as $p) {
            $js_grouped[$catLabel][] = [
                'id'    => $p['id'],
                'name'  => $p['product_name'],
                'price' => (float)$p['selling_price'],
                'stock' => (int)$p['current_stock'],
            ];
        }
    }
    echo json_encode($js_grouped, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>;

// cart: { productId: { id, name, price, qty } }
var cart = {};
var currentCategory = null; // which category is open in product view

/* ── Color palette for product icons ──────────────────────── */
var iconColors = [
    'bg-indigo-100 text-indigo-600','bg-emerald-100 text-emerald-600',
    'bg-amber-100 text-amber-600','bg-rose-100 text-rose-600',
    'bg-sky-100 text-sky-600','bg-purple-100 text-purple-600',
    'bg-orange-100 text-orange-600','bg-teal-100 text-teal-600'
];
function iconColor(str) { return iconColors[str.charCodeAt(0) % iconColors.length]; }

/* ── Init ──────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('saleDateDisplay').value = new Date().toISOString().split('T')[0];
});

/* ── Open a category → show its products ──────────────────── */
function openCategory(catLabel, count) {
    currentCategory = catLabel;

    // Switch views
    document.getElementById('categoryView').classList.add('hidden');
    document.getElementById('productView').classList.remove('hidden');

    // Update top bar
    document.getElementById('topBarBackSales').classList.add('hidden');
    document.getElementById('topBarBack').classList.remove('hidden');
    document.getElementById('searchWrap').classList.remove('hidden');
    document.getElementById('viewTitle').textContent = catLabel;
    document.getElementById('topBarCount').textContent = count + ' products';
    document.getElementById('productSearch').value = '';

    // Render products for this category
    renderProductGrid(allGrouped[catLabel] || []);
}

/* ── Go back to category selection ────────────────────────── */
function goBackToCategories() {
    currentCategory = null;

    document.getElementById('productView').classList.add('hidden');
    document.getElementById('categoryView').classList.remove('hidden');

    document.getElementById('topBarBack').classList.add('hidden');
    document.getElementById('topBarBackSales').classList.remove('hidden');
    document.getElementById('searchWrap').classList.add('hidden');
    document.getElementById('viewTitle').textContent = 'All Categories';
    document.getElementById('topBarCount').textContent = Object.keys(allGrouped).length + ' categories';

    // Clear search
    document.getElementById('productSearch').value = '';
    document.getElementById('noResults').classList.add('hidden');

    // Scroll back to top
    document.getElementById('scrollArea').scrollTop = 0;
}

/* ── Render product cards into #productGrid ────────────────── */
function renderProductGrid(prods) {
    var grid = document.getElementById('productGrid');
    grid.innerHTML = '';
    document.getElementById('noResults').classList.add('hidden');

    if (!prods || prods.length === 0) {
        grid.innerHTML = '<div class="col-span-full py-16 text-center text-gray-400"><i class="fas fa-box-open text-4xl mb-3 block opacity-40"></i><p class="text-sm">No products in this category</p></div>';
        return;
    }

    prods.forEach(function (p) {
        var outOfStock = p.stock <= 0;
        var initial    = p.name.charAt(0).toUpperCase();
        var color      = iconColor(initial);
        var inCartQty  = cart[p.id] ? cart[p.id].qty : 0;

        var div = document.createElement('div');
        div.className  = 'prod-card' + (outOfStock ? ' out-of-stock' : '');
        div.dataset.id    = p.id;
        div.dataset.name  = p.name;
        div.dataset.price = p.price;
        div.dataset.stock = p.stock;
        if (!outOfStock) div.onclick = function () { addToCart(div); };
        div.title = p.name;

        div.innerHTML =
            '<div class="prod-icon ' + color + '">' + _esc(initial) + '</div>'
          + '<div class="prod-name">' + _esc(p.name) + '</div>'
          + '<div class="prod-price">₹' + p.price.toFixed(2) + '</div>'
          + '<div class="prod-stock">' + (outOfStock ? '<span class="text-red-500">Out of stock</span>' : 'Stock: ' + p.stock) + '</div>'
          + (outOfStock ? '' : '<div id="badge-' + p.id + '" style="' + (inCartQty > 0 ? 'display:flex' : 'display:none') + '" class="absolute top-2 right-2 w-5 h-5 rounded-full bg-[var(--primary)] text-white text-[10px] font-bold flex items-center justify-center">' + (inCartQty > 0 ? inCartQty : '') + '</div>');

        grid.appendChild(div);
    });
}

/* ── Search within current product view ────────────────────── */
function filterProducts(q) {
    q = q.toLowerCase().trim();
    if (!currentCategory) return; // not in product view

    var prods = allGrouped[currentCategory] || [];
    var filtered = q ? prods.filter(function (p) { return p.name.toLowerCase().includes(q); }) : prods;

    renderProductGrid(filtered);
    document.getElementById('noResults').classList.toggle('hidden', filtered.length > 0);
}

/* ── Payment mode chips ────────────────────────────────────── */
function selectPayMode(mode) {
    document.querySelectorAll('.pay-chip').forEach(function (c) {
        c.classList.toggle('active', c.dataset.mode === mode);
    });
}

/* ── Add product to cart ───────────────────────────────────── */
function addToCart(cardEl) {
    var id    = cardEl.dataset.id;
    var name  = cardEl.dataset.name;
    var price = parseFloat(cardEl.dataset.price);

    if (cart[id]) { cart[id].qty++; }
    else { cart[id] = { id: id, name: name, price: price, qty: 1 }; }

    renderCart();
    cardEl.style.transform = 'scale(0.94)';
    setTimeout(function () { cardEl.style.transform = ''; }, 120);
}

/* ── Badge on product card ─────────────────────────────────── */
function updateBadge(id) {
    var badge = document.getElementById('badge-' + id);
    if (!badge) return;
    var qty = cart[id] ? cart[id].qty : 0;
    badge.textContent   = qty > 0 ? qty : '';
    badge.style.display = qty > 0 ? 'flex' : 'none';
}

/* ── Render cart ───────────────────────────────────────────── */
function renderCart() {
    var ids   = Object.keys(cart);
    var list  = document.getElementById('cartList');
    var empty = document.getElementById('cartEmpty');

    empty.style.display = ids.length === 0 ? 'flex' : 'none';
    list.querySelectorAll('.cart-row').forEach(function (r) { r.remove(); });

    ids.forEach(function (id) {
        var item = cart[id];
        var row  = document.createElement('div');
        row.className = 'cart-row';
        row.id = 'cart-row-' + id;
        row.innerHTML =
            '<div class="flex-1 min-w-0">'
          +   '<div class="cart-name truncate">' + _esc(item.name) + '</div>'
          +   '<div class="cart-price">₹' + item.price.toFixed(2) + ' each</div>'
          + '</div>'
          + '<button type="button" class="qty-btn" onclick="changeQty(\'' + id + '\',-1)">−</button>'
          + '<span class="qty-display">' + item.qty + '</span>'
          + '<button type="button" class="qty-btn" onclick="changeQty(\'' + id + '\',1)">+</button>'
          + '<div class="cart-total">₹' + (item.price * item.qty).toFixed(2) + '</div>'
          + '<button type="button" onclick="removeFromCart(\'' + id + '\')"'
          +   ' class="w-7 h-7 flex items-center justify-center rounded-lg text-red-300 hover:text-red-500 hover:bg-red-50 transition-colors ml-1">'
          +   '<i class="fas fa-times text-xs"></i></button>';
        list.appendChild(row);
        updateBadge(id);
    });

    recalcTotals();
}

/* ── Change qty ────────────────────────────────────────────── */
function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) { removeFromCart(id); return; }
    var row = document.getElementById('cart-row-' + id);
    if (row) {
        row.querySelector('.qty-display').textContent = cart[id].qty;
        row.querySelector('.cart-total').textContent  = '₹' + (cart[id].price * cart[id].qty).toFixed(2);
    }
    updateBadge(id);
    recalcTotals();
}

/* ── Remove from cart ──────────────────────────────────────── */
function removeFromCart(id) {
    delete cart[id];
    updateBadge(id);
    renderCart();
}

/* ── Clear cart ────────────────────────────────────────────── */
function clearCart() {
    Object.keys(cart).forEach(function (id) { cart[id].qty = 0; updateBadge(id); });
    cart = {};
    renderCart();
}

/* ── Recalc totals ─────────────────────────────────────────── */
function recalcTotals() {
    var ids = Object.keys(cart);
    var totalItems = ids.length, totalQty = 0, subtotal = 0;
    ids.forEach(function (id) { totalQty += cart[id].qty; subtotal += cart[id].price * cart[id].qty; });
    var discount   = parseFloat(document.getElementById('discountInput').value) || 0;
    var grandTotal = Math.max(subtotal - discount, 0);
    document.getElementById('summItems').textContent    = totalItems;
    document.getElementById('summQty').textContent      = totalQty;
    document.getElementById('summSubtotal').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('summTotal').textContent    = '₹' + grandTotal.toFixed(2);
    var hb = document.getElementById('cartBadge');
    if (totalItems > 0) { hb.textContent = totalItems; hb.classList.remove('hidden'); }
    else { hb.classList.add('hidden'); }
}

/* ── Build hidden form fields ──────────────────────────────── */
function buildForm() {
    var ids = Object.keys(cart);
    var saleDate = document.getElementById('saleDateDisplay').value;
    if (!saleDate) { showToast('Please select a sale date.', 'error'); return false; }

    document.getElementById('f_sale_date').value       = saleDate;
    document.getElementById('f_billing_type').value    = document.getElementById('billingTypeDisplay').value;
    document.getElementById('f_delivery_status').value = document.getElementById('deliveryStatusDisplay').value;
    document.getElementById('f_mobile').value          = document.getElementById('mobileDisplay').value;

    var chip = document.querySelector('.pay-chip.active');
    document.getElementById('f_payment_mode').value = chip ? chip.dataset.mode : 'CASH';

    var discount  = parseFloat(document.getElementById('discountInput').value) || 0;
    var subtotal  = 0;
    ids.forEach(function (id) { subtotal += cart[id].price * cart[id].qty; });
    var grandTotal = Math.max(subtotal - discount, 0);
    document.getElementById('f_discount').value     = discount.toFixed(2);
    document.getElementById('f_final_amount').value = grandTotal.toFixed(2);

    var container = document.getElementById('formProductRows');
    container.innerHTML = '';
    ids.forEach(function (id, i) {
        var n = i + 1, item = cart[id];
        container.innerHTML +=
            '<input type="hidden" name="products[' + n + '][product_id]"   value="' + item.id + '">'
          + '<input type="hidden" name="products[' + n + '][quantity]"      value="' + item.qty + '">'
          + '<input type="hidden" name="products[' + n + '][selling_price]" value="' + item.price.toFixed(2) + '">';
    });
    return true;
}

/* ── Reset bill (keep page, clear cart) ────────────────────── */
function resetBill() {
    clearCart();
    document.getElementById('discountInput').value     = '0';
    document.getElementById('mobileDisplay').value     = '';
    document.getElementById('billingTypeDisplay').value = 'GST';
    document.getElementById('deliveryStatusDisplay').value = 'ON-HAND';
    document.getElementById('saleDateDisplay').value   = new Date().toISOString().split('T')[0];
    selectPayMode('CASH');
    goBackToCategories();
    showToast('Bill saved & printed! Ready for next sale.', 'success');
}

/* ── Submit sale ───────────────────────────────────────────── */
function submitSale(action) {
    if (Object.keys(cart).length === 0) {
        showToast('Add at least one product to the cart.', 'error');
        return;
    }
    if (!buildForm()) return;

    var form = document.getElementById('salesForm');

    if (action === 'save_print') {
        // Remove old action input if any
        var old = form.querySelector('input[name="action"]');
        if (old) old.remove();
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'action'; inp.value = 'save_print';
        form.appendChild(inp);

        form.target = '_blank';
        form.submit();
        form.target = '_self';

        // Stay on page — reset bill after a short delay for form to submit
        setTimeout(resetBill, 600);
    } else {
        // Save only → regular submit in same tab (process_sale.php redirects as needed)
        form.submit();
    }
}

/* ── HTML escape ───────────────────────────────────────────── */
function _esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
</body>
</html>
