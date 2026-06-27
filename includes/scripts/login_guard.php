<?php


define('MAX_ATTEMPTS', 5);
define('LOCKOUT_TIME', 2 * 60); // 2 minutes in seconds

function isLockedOut(): bool {
    if (!isset($_SESSION['login_attempts'])) return false;

    $attempts = $_SESSION['login_attempts'];
    $lastAttempt = $_SESSION['last_attempt_time'] ?? 0;

    if ($attempts >= MAX_ATTEMPTS) {
        if (time() - $lastAttempt < LOCKOUT_TIME) {
            return true; // Still locked
        }
        // Lockout expired — reset
        resetAttempts();
    }
    return false;
}

function blockIfLockedOut(): void {
    if (isLockedOut()) {
        $remaining = getRemainingLockoutTime();
        $mins = ceil($remaining / 60);

        http_response_code(429); // Too Many Requests

        // Option A: die with a plain message
        // die("Too many login attempts. Try again in {$mins} minute(s).");

        // Option B: redirect back with lockout info in session (recommended)
        $_SESSION['lockout_message'] = "Too many attempts. Try again in {$mins} minute(s).";
        $_SESSION['lockout_until']   = time() + $remaining;
        header('Location: ../../auth/sign-in.php');
        exit; // ✅ Hard stop — nothing below this runs
    }
}


function recordFailedAttempt(): void {
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['last_attempt_time'] = time();
}

function resetAttempts(): void {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = null;
}

function getRemainingLockoutTime(): int {
    $elapsed = time() - ($_SESSION['last_attempt_time'] ?? 0);
    return max(0, LOCKOUT_TIME - $elapsed);
}