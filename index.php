<?php
session_start();
if (isset($_SESSION['rgt_logedin_user_id']) && trim($_SESSION['rgt_logedin_user_id']) !== '') {
    header('Location: dealer/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DealerPro — Goods Trading Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#4F46E5', secondary: '#6366F1' },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        :root {
            --primary: #4F46E5;
            --primary-light: #EEF2FF;
        }
        .gradient-text {
            background: linear-gradient(135deg, #4F46E5, #6366F1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-bg {
            background: radial-gradient(ellipse 80% 60% at 50% -10%, #EEF2FF 0%, transparent 70%);
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(79,70,229,.1);
        }
        .feature-card { transition: transform .2s ease, box-shadow .2s ease; }
    </style>
</head>
<body class="bg-white font-sans text-gray-900 antialiased">

<!-- ── Navbar ──────────────────────────────────────────────── -->
<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-sm">DP</div>
            <span class="text-lg font-bold text-gray-900">DealerPro</span>
        </div>
        <!-- Nav links -->
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="#features" class="hover:text-primary transition-colors">Features</a>
            <a href="#security" class="hover:text-primary transition-colors">Security</a>
            <a href="#cta" class="hover:text-primary transition-colors">Get Started</a>
        </div>
        <!-- Auth buttons -->
        <div class="flex items-center gap-3">
            <a href="auth/sign-in.php"
               class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Sign In
            </a>
            <a href="auth/sign-up.php"
               class="px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-primary to-secondary rounded-xl shadow shadow-indigo-300 hover:shadow-md transition-all">
                Start Free
            </a>
        </div>
    </div>
</nav>

<!-- ── Hero ────────────────────────────────────────────────── -->
<section class="hero-bg pt-20 pb-16 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- Left: copy -->
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-primary text-xs font-semibold rounded-full mb-6">
                    <i class="fas fa-bolt"></i> Built for Indian Traders & Dealers
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-5">
                    Manage your goods<br>
                    <span class="gradient-text">trading business</span><br>
                    the smart way.
                </h1>
                <p class="text-lg text-gray-500 mb-8 leading-relaxed">
                    Stock, purchases, sales, payments, and profit — all in one place.
                    No accounting background needed.
                </p>
                <div class="flex flex-wrap gap-3 mb-8">
                    <a href="auth/sign-up.php"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-xl shadow-lg shadow-indigo-300 hover:shadow-xl transition-all">
                        <i class="fas fa-rocket text-sm"></i> Get Started Free
                    </a>
                    <a href="auth/sign-in.php"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-sign-in-alt text-sm"></i> Sign In
                    </a>
                </div>
                <p class="text-xs text-gray-400">No credit card required. Access anywhere.</p>
            </div>

            <!-- Right: feature pills -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl p-8">
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-5">Perfect for</p>
                <div class="flex flex-wrap gap-2 mb-6">
                    <?php
                    $types = ['Grocery Traders','Wholesale Distributors','Electronics Dealers','Construction Suppliers','FMCG Dealers','Hardware Shops'];
                    foreach ($types as $t) {
                        echo '<span class="px-3 py-1.5 bg-indigo-50 text-primary text-xs font-semibold rounded-full">' . $t . '</span>';
                    }
                    ?>
                </div>
                <div class="space-y-3">
                    <?php
                    $perks = [
                        ['fas fa-check-circle','text-green-500','Auto stock update on every sale & purchase'],
                        ['fas fa-check-circle','text-green-500','Company-wise payment ledger'],
                        ['fas fa-check-circle','text-green-500','Role-based staff access'],
                        ['fas fa-check-circle','text-green-500','Profit tracking per sale'],
                        ['fas fa-check-circle','text-green-500','WhatsApp bill sharing'],
                    ];
                    foreach ($perks as [$icon, $color, $text]) {
                        echo '<div class="flex items-center gap-3 text-sm text-gray-700">
                            <i class="' . $icon . ' ' . $color . '"></i>
                            <span>' . $text . '</span>
                        </div>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── Features ────────────────────────────────────────────── -->
<section id="features" class="py-20 px-6 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Everything you need</h2>
            <p class="text-gray-500 max-w-xl mx-auto">Simple flows that cut paperwork and keep every rupee and item tracked automatically.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $features = [
                ['fas fa-building','bg-indigo-100 text-indigo-600','Company Management','Add suppliers, track what you owe or receive, and keep separate ledgers per company.'],
                ['fas fa-cubes','bg-emerald-100 text-emerald-600','Stock & Products','Know stock anytime, track buy/sell prices, auto-update on every purchase or sale.'],
                ['fas fa-cart-arrow-down','bg-amber-100 text-amber-600','Purchase Tracking','Create purchase orders, see pending vs received, avoid missing or duplicate orders.'],
                ['fas fa-file-invoice-dollar','bg-sky-100 text-sky-600','Payment & Ledger','Automatic credits/debits, clear history, zero confusion in company payments.'],
                ['fas fa-cash-register','bg-rose-100 text-rose-600','Sales & Billing','Touch-friendly POS, auto-calc profit, WhatsApp bill sharing in one tap.'],
                ['fas fa-users','bg-purple-100 text-purple-600','Staff Management','Add staff with limited access. Each dealer sees only their own data.'],
            ];
            foreach ($features as [$icon, $iconClass, $title, $desc]) {
                echo '
                <div class="feature-card bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                    <div class="w-11 h-11 rounded-xl ' . $iconClass . ' flex items-center justify-center mb-4">
                        <i class="' . $icon . ' text-lg"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">' . $title . '</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">' . $desc . '</p>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- ── Security ────────────────────────────────────────────── -->
<section id="security" class="py-20 px-6 bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Secure & role-based</h2>
            <p class="text-gray-500">Admins control everything. Staff get limited access. Each dealer's data is isolated.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
            <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-4">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Admin</h3>
                <p class="text-sm text-gray-600">Full access: products, companies, purchases, sales, reports, and staff management.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <div class="w-11 h-11 rounded-xl bg-gray-200 text-gray-600 flex items-center justify-center mb-4">
                    <i class="fas fa-user text-lg"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Staff</h3>
                <p class="text-sm text-gray-600">Can record sales and purchases. Restricted from editing products, companies, and staff.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ─────────────────────────────────────────────────── -->
<section id="cta" class="py-20 px-6 bg-gradient-to-br from-primary to-secondary">
    <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Start in minutes</h2>
        <p class="text-indigo-200 mb-8 text-lg">No accounting jargon. Just the essentials to keep goods, money, and profit clear.</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="auth/sign-up.php"
               class="px-8 py-3.5 bg-white text-primary font-bold rounded-xl shadow-lg hover:shadow-xl transition-all">
                Create your account
            </a>
            <a href="auth/sign-in.php"
               class="px-8 py-3.5 bg-white/20 text-white font-semibold rounded-xl border border-white/30 hover:bg-white/30 transition-all">
                Sign in
            </a>
        </div>
    </div>
</section>

<!-- ── Footer ──────────────────────────────────────────────── -->
<footer class="py-8 px-6 bg-gray-900 text-center">
    <div class="flex items-center justify-center gap-2 mb-2">
        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-xs">DP</div>
        <span class="text-white font-semibold">DealerPro</span>
    </div>
    <p class="text-gray-500 text-sm">Goods Trading Management System</p>
</footer>

</body>
</html>
