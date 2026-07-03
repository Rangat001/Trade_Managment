<?php
session_start();
if (isset($_SESSION['rgt_logedin_user_id']) && trim($_SESSION['rgt_logedin_user_id']) !== '') {
    header('Location: ../dealer/dashboard.php');
    exit;
}
$error = $_SESSION['rgt_error_message'] ?? '';
unset($_SESSION['rgt_error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — DealerPro</title>

      <link rel="icon" href="../asset/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="192x192" href="../asset/dp_favicon_192.png">

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

<div class="w-full max-w-lg">

    <!-- Logo -->
    <div class="text-center mb-8">
        <a href="../index.php" class="inline-flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-base shadow-lg shadow-indigo-300">DP</div>
            <span class="text-2xl font-bold text-gray-900">DealerPro</span>
        </a>
        <p class="text-gray-500 text-sm mt-2">Create your free account</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">

        <!-- Error message -->
        <?php if ($error): ?>
        <div class="mb-5 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form action="../includes/scripts/signmeup.php" method="POST" class="space-y-4" id="signupForm">

            <!-- Business Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Business Name *</label>
                <div class="relative">
                    <i class="fas fa-store absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" name="business_name" required placeholder="Your shop / business name"
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
            </div>

            <!-- Owner Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Owner Name *</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" name="owner_name" required placeholder="Full name"
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address *</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="email" name="user_email" required placeholder="you@example.com"
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
            </div>

            <!-- Mobile -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mobile Number *</label>
                <div class="relative">
                    <i class="fas fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="tel" name="user_phone" required placeholder="10-digit mobile number"
                           pattern="[0-9]{10}" title="Mobile number must be exactly 10 digits"
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
            </div>

            <!-- Passwords -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password *</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="password" id="pwd1" name="user_password" required
                               placeholder="Min 8 characters"
                               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                               title="Must contain uppercase, lowercase, digit, special character, min 8 chars"
                               class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <button type="button" onclick="togglePwd('pwd1','eye1')"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i id="eye1" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password *</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="password" id="pwd2" name="user_confirm_password" required
                               placeholder="Repeat password"
                               class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <button type="button" onclick="togglePwd('pwd2','eye2')"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i id="eye2" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Password hint -->
            <p class="text-xs text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                Password must have uppercase, lowercase, number, and special character (@$!%*?&), min 8 chars.
            </p>

            <!-- Submit -->
            <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-xl shadow-lg shadow-indigo-300 hover:shadow-xl transition-all text-sm mt-2">
                Create Account
            </button>

        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Already have an account?
            <a href="sign-in.php" class="text-primary font-semibold hover:underline">Sign in</a>
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
    function togglePwd(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon  = document.getElementById(iconId);
        var isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        icon.className = isText ? 'fas fa-eye text-sm' : 'fas fa-eye-slash text-sm';
    }

    // Client-side password match check
    document.getElementById('signupForm').addEventListener('submit', function (e) {
        var p1 = document.getElementById('pwd1').value;
        var p2 = document.getElementById('pwd2').value;
        if (p1 !== p2) {
            e.preventDefault();
            alert('Passwords do not match. Please try again.');
        }
    });
</script>

</body>
</html>
