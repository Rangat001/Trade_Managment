<?php
/**
 * header.php — Shared <head> content and sticky topbar
 *
 * PHP variables consumed:
 *   $pageTitle       (string) — page heading shown in topbar and <title>
 *   $showEditProfile (bool, optional) — show "Edit Profile" link in dropdown
 *
 * Usage: require_once 'includes/header.php'; inside <head>...</head>
 */
$showEditProfile = $showEditProfile ?? false;
$faviconVersion = @filemtime(__DIR__ . '/../../asset/favicon.ico') ?: time();
$favicon192Version = @filemtime(__DIR__ . '/../../asset/dp_favicon_192.png') ?: time();
?>      
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dealer Panel') ?> - Dealer Panel</title>

    <link rel="icon" href="../asset/favicon.ico?v=<?= $faviconVersion ?>" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="192x192" href="../asset/dp_favicon_192.png?v=<?= $favicon192Version ?>">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:   '#4F46E5',
                        secondary: '#6366F1',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="../asset/css/dataTables.bootstrap4.min.css">

    <!-- App CSS (design tokens + DataTables overrides) -->
    <link rel="stylesheet" href="assets/css/app.css">

    <?php includePermissionJS(); ?>
</head>
<body class="bg-[var(--bg)] font-sans text-[var(--text)]">

<!-- ═══════════════════════════════════════════════════════════
     STICKY TOPBAR
     ═══════════════════════════════════════════════════════════ -->
<header class="sticky top-0 bg-white border-b border-[var(--border)] px-4 md:px-6 py-3 shadow-sm z-40 md:ml-64">
    <div class="flex items-center justify-between">

        <!-- Left: hamburger + page title -->
        <div class="flex items-center gap-3">
            <button id="menuToggle"
                    class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg text-[var(--subtext)] hover:bg-gray-100 transition-colors"
                    aria-label="Toggle menu">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <h1 class="text-xl font-semibold text-[var(--text)]">
                <?= htmlspecialchars($pageTitle ?? '') ?>
            </h1>
        </div>

        <!-- Right: profile dropdown -->
        <div class="relative">
            <button id="profileDropdown"
                    class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-100 transition-colors cursor-pointer"
                    aria-haspopup="true" aria-expanded="false">
                <div class="w-8 h-8 rounded-full bg-[var(--primary)] flex items-center justify-center text-white text-sm font-semibold">
                    <?= strtoupper(substr($_SESSION['rgt_logedin_user_name'] ?? 'D', 0, 1)) ?>
                </div>
                <span class="hidden sm:block text-sm font-medium text-[var(--text)]">
                    <?= htmlspecialchars($_SESSION['rgt_logedin_user_name'] ?? 'Dealer') ?>
                </span>
                <i class="fas fa-chevron-down text-[var(--subtext)] text-xs"></i>
            </button>

            <!-- Dropdown menu -->
            <div id="profileMenu"
                 class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-[var(--border)] py-1 z-50">

                <?php if ($showEditProfile): ?>
                <a href="#" onclick="openProfileModal(); return false;"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-[var(--text)] hover:bg-gray-50 transition-colors">
                    <i class="fas fa-user-edit w-4 text-[var(--subtext)]"></i>
                    <span>Edit Profile</span>
                </a>
                <div class="border-t border-[var(--border)] my-1"></div>
                <?php endif; ?>

                <a href="logout.php"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    <i class="fas fa-sign-out-alt w-4"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

    </div>
</header>
