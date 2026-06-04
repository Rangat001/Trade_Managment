<?php
session_start();
if (isset($_SESSION['rgt_logedin_user_id']) && trim($_SESSION['rgt_logedin_user_id']) !== '') {
    header('Location: ../dealer/dashboard.php');
    exit;
}
$error   = $_SESSION['rgt_error_message']   ?? '';
$success = $_SESSION['rgt_success_message'] ?? '';
unset($_SESSION['rgt_error_message'], $_SESSION['rgt_success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — DealerPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
</head>
<body class="bg-gray-50 font-sans min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">

    <!-- Logo -->
    <div class="text-center mb-8">
        <a href="../index.php" class="inline-flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-base shadow-lg shadow-indigo-300">DP</div>
            <span class="text-2xl font-bold text-gray-900">DealerPro</span>
        </a>
        <p class="text-gray-500 text-sm mt-2">Sign in to your account</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">

        <!-- Flash messages -->
        <?php if ($error): ?>
        <div class="mb-5 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="mb-5 flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
        <?php endif; ?>

        <form action="../includes/scripts/signmein.php" method="POST" class="space-y-5">

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="email" id="email" name="email" required
                           placeholder="you@example.com"
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="text-sm font-semibold text-gray-700">Password</label>
                    <a href="forgot.php" class="text-xs text-primary hover:underline font-medium">Forgot password?</a>
                </div>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="password" id="password" name="rgt_login_password" required
                           placeholder="Enter your password"
                           class="w-full pl-10 pr-11 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    <button type="button" id="togglePwd"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-eye text-sm" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-xl shadow-lg shadow-indigo-300 hover:shadow-xl transition-all text-sm">
                Sign In
            </button>

        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Don't have an account?
            <a href="sign-up.php" class="text-primary font-semibold hover:underline">Create one</a>
        </p>

    </div>

    <!-- Back to home -->
    <p class="text-center mt-6">
        <a href="../index.php" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> Back to home
        </a>
    </p>

</div>

<script>
    var pwd     = document.getElementById('password');
    var toggle  = document.getElementById('togglePwd');
    var eyeIcon = document.getElementById('eyeIcon');

    toggle.addEventListener('click', function () {
        var isText = pwd.type === 'text';
        pwd.type = isText ? 'password' : 'text';
        eyeIcon.className = isText ? 'fas fa-eye text-sm' : 'fas fa-eye-slash text-sm';
    });
</script>

</body>
</html>
