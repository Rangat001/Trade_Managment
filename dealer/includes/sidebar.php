<?php
/**
 * sidebar.php — Desktop sidebar + mobile bottom tab bar
 *
 * PHP variables consumed:
 *   $activePage (string) — one of: dashboard, sales, products, companies, purchases, staff
 *
 * Usage: require_once 'includes/sidebar.php'; inside <body>
 */
$activePage = $activePage ?? '';

/**
 * Returns Tailwind classes for a nav link based on whether it is active.
 */
function navClass(string $page, string $active): string {
    if ($page === $active) {
        return 'flex items-center px-4 py-3 mb-1 text-white bg-gradient-to-r from-[#4F46E5] to-indigo-500 rounded-xl shadow-md font-medium';
    }
    return 'flex items-center px-4 py-3 mb-1 text-[var(--subtext)] hover:bg-gray-100 hover:text-[var(--text)] rounded-xl transition-all font-medium';
}

/**
 * Returns Tailwind classes for a bottom tab link.
 */
function tabClass(string $page, string $active): string {
    if ($page === $active) {
        return 'flex flex-col items-center gap-1 px-3 py-2 text-[var(--primary)] font-medium';
    }
    return 'flex flex-col items-center gap-1 px-3 py-2 text-[var(--subtext)] hover:text-[var(--primary)] transition-colors';
}
?>

<!-- ═══════════════════════════════════════════════════════════
     DESKTOP SIDEBAR
     ═══════════════════════════════════════════════════════════ -->
<aside id="sidebar"
       class="fixed left-0 top-0 w-64 h-screen bg-white border-r border-[var(--border)] z-50 transition-transform duration-300 flex flex-col">

    <!-- Logo -->
    <div class="px-6 py-5 border-b border-[var(--border)]">
        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#4F46E5] to-indigo-400 bg-clip-text text-transparent tracking-tight">
            DealerPro
        </h2>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-3 mt-2 overflow-y-auto">

        <a href="dashboard.php" class="<?= navClass('dashboard', $activePage) ?>">
            <i class="fas fa-home w-5 mr-3 text-center"></i>
            <span>Dashboard</span>
        </a>

        <a href="sales.php" class="<?= navClass('sales', $activePage) ?>">
            <i class="fas fa-cash-register w-5 mr-3 text-center"></i>
            <span>Sales</span>
        </a>

        <a href="products.php" class="<?= navClass('products', $activePage) ?>">
            <i class="fas fa-box w-5 mr-3 text-center"></i>
            <span>Products</span>
        </a>

        <a href="companies.php" class="<?= navClass('companies', $activePage) ?>">
            <i class="fas fa-building w-5 mr-3 text-center"></i>
            <span>Companies</span>
        </a>

        <a href="purchases.php" class="<?= navClass('purchases', $activePage) ?>">
            <i class="fas fa-shopping-cart w-5 mr-3 text-center"></i>
            <span>Purchases</span>
        </a>

        <a href="staff.php" class="<?= navClass('staff', $activePage) ?>">
            <i class="fas fa-users w-5 mr-3 text-center"></i>
            <span>Staff</span>
        </a>

        <?php /* Reports — hidden until feature is ready
        <a href="reports.php" class="<?= navClass('reports', $activePage) ?>">
            <i class="fas fa-chart-bar w-5 mr-3 text-center"></i>
            <span>Reports</span>
        </a>
        */ ?>

    </nav>

    <!-- Footer info -->
    <div class="px-6 py-4 border-t border-[var(--border)]">
        <p class="text-xs text-[var(--subtext)]">
            Logged in as <span class="font-semibold"><?= htmlspecialchars($_SESSION['rgt_logedin_user_role'] ?? '') ?></span>
        </p>
    </div>
</aside>

<!-- Backdrop (mobile overlay) -->
<div id="sidebarBackdrop"
     class="hidden fixed inset-0 bg-black/50 z-40 md:hidden"></div>

<!-- ═══════════════════════════════════════════════════════════
     MOBILE BOTTOM TAB BAR
     ═══════════════════════════════════════════════════════════ -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-[var(--border)] z-40 flex justify-around items-center px-2 py-1 safe-area-inset-bottom">

    <a href="dashboard.php" class="<?= tabClass('dashboard', $activePage) ?>">
        <i class="fas fa-home text-lg"></i>
        <span class="text-[10px]">Dashboard</span>
    </a>

    <a href="sales.php" class="<?= tabClass('sales', $activePage) ?>">
        <i class="fas fa-cash-register text-lg"></i>
        <span class="text-[10px]">Sales</span>
    </a>

    <a href="products.php" class="<?= tabClass('products', $activePage) ?>">
        <i class="fas fa-box text-lg"></i>
        <span class="text-[10px]">Products</span>
    </a>

    <a href="purchases.php" class="<?= tabClass('purchases', $activePage) ?>">
        <i class="fas fa-shopping-cart text-lg"></i>
        <span class="text-[10px]">Purchases</span>
    </a>

    <a href="staff.php" class="<?= tabClass('staff', $activePage) ?>">
        <i class="fas fa-users text-lg"></i>
        <span class="text-[10px]">Staff</span>
    </a>

</nav>
