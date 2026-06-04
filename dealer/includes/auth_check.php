<?php
/**
 * Authentication and Permission Check
 * Include this file at the top of every dealer panel page
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/scripts/connection.php';

// Check if user is logged in
if (isset($_SESSION['rgt_logedin_user_id']) && (trim($_SESSION['rgt_logedin_user_id']) !== '')) {
    $user_id = $_SESSION['rgt_logedin_user_id'];
    $user_role = $_SESSION['rgt_logedin_user_role'];
    $user_name = $_SESSION['rgt_logedin_user_name'];
    $dealer_id = $_SESSION['rgt_logedin_user_dealer_id'] ?? null;
    
    // Validate role - only ADMIN and STAFF allowed
    if ($user_role != "ADMIN" && $user_role != "STAFF") {
        header("Location: ../404.php");
        exit;
    }
} else {
    header("Location: ../auth/sign-in.php");
    exit;
}

// Set permission flags
$is_admin = ($user_role === 'ADMIN');
$is_staff = ($user_role === 'STAFF');

/**
 * Permission Configuration
 */
$permissions = [
    'ADMIN' => [
        'staff_view' => true, 'staff_add' => true, 'staff_edit' => true, 'staff_delete' => true,
        'product_view' => true, 'product_add' => true, 'product_edit' => true, 'product_delete' => true,
        'company_view' => true, 'company_add' => true, 'company_edit' => true, 'company_delete' => true,
        'purchase_view' => true, 'purchase_add' => true, 'purchase_edit' => true, 'purchase_delete' => true,
        'sales_view' => true, 'sales_add' => true, 'sales_edit' => true, 'sales_delete' => true,
        'reports_view' => true, 'reports_export' => true,
        'settings_view' => true, 'settings_edit' => true,
    ],
    'STAFF' => [
        'staff_view' => true, 'staff_add' => false, 'staff_edit' => false, 'staff_delete' => false,
        'product_view' => true, 'product_add' => false, 'product_edit' => false, 'product_delete' => false,
        'company_view' => true, 'company_add' => false, 'company_edit' => false, 'company_delete' => false,
        'purchase_view' => true, 'purchase_add' => true, 'purchase_edit' => false, 'purchase_delete' => false,
        'sales_view' => true, 'sales_add' => true, 'sales_edit' => false, 'sales_delete' => false,
        'reports_view' => true, 'reports_export' => false,
        'settings_view' => false, 'settings_edit' => false,
    ],
];

$user_permissions = $permissions[$user_role] ?? $permissions['STAFF'];

/**
 * Check if user has a specific permission
 */
function hasPermission($permission) {
    global $user_permissions;
    return isset($user_permissions[$permission]) && $user_permissions[$permission] === true;
}

/**
 * Require a specific permission or redirect
 */
function requirePermission($permission, $redirect_url = null) {
    if (!hasPermission($permission)) {
        $_SESSION['error'] = 'You do not have permission to perform this action.';
        header("Location: " . ($redirect_url ?? $_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
        exit;
    }
}

/**
 * Include permission JS - call this in <head> section
 * Only includes the script if user is NOT admin
 */
function includePermissionJS() {
    global $is_admin, $is_staff, $user_role, $user_permissions;
    
    // Always include for proper functioning, but behavior differs based on role
    ?>
    <script>
        const userPermissions = <?php echo json_encode($user_permissions); ?>;
        const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;
        const isStaff = <?php echo $is_staff ? 'true' : 'false'; ?>;
        const currentUserRole = '<?php echo $user_role; ?>';
        
        <?php if (!$is_admin): ?>
        // Only run permission restrictions for non-admin users
        function hasPermission(p) { return userPermissions[p] === true; }
        
        function showPermissionError(msg) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 z-[9999] bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3';
            toast.innerHTML = '<i class="fas fa-exclamation-circle"></i><span>' + msg + '</span>';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
        
        function withPermission(permission, callback) {
            if (hasPermission(permission)) { callback(); }
            else { showPermissionError('You do not have permission to perform this action.'); }
        }
        
        function confirmWithPermission(permission, msg, callback) {
            if (!hasPermission(permission)) { showPermissionError('You do not have permission.'); return; }
            if (confirm(msg)) { callback(); }
        }
        
        function disableButton(btn) {
            btn.disabled = true;
            btn.classList.add('cursor-not-allowed', 'opacity-50');
            btn.style.pointerEvents = 'none';
            if (btn.hasAttribute('onclick')) {
                btn.setAttribute('data-onclick', btn.getAttribute('onclick'));
                btn.removeAttribute('onclick');
            }
            btn.onclick = null;
            btn.title = 'You do not have permission';
        }
        
        function disableAllRestrictedButtons() {
            // Map onclick/icon patterns to the permission they require.
            // Only disable if the user does NOT have that permission.
            const permissionMap = [
                // Staff management
                { selector: '[onclick*="openAddStaffModal"], [onclick*="addStaff"]',   perm: 'staff_add'      },
                { selector: '[onclick*="openEditStaffModal"], [onclick*="saveStaff"]', perm: 'staff_edit'     },
                { selector: '[onclick*="disableStaff"]',                               perm: 'staff_delete'   },
                // Product management
                { selector: '[onclick*="openAddProductModal"]',                        perm: 'product_add'    },
                { selector: '[onclick*="openEditProductModal"]',                       perm: 'product_edit'   },
                // Company management
                { selector: '[onclick*="openAddCompanyModal"], [onclick*="openAddProductModal"][data-type="company"]', perm: 'company_add'  },
                { selector: '[onclick*="openEditCompanyModal"], [onclick*="openEditProductModal"][data-type="company"]', perm: 'company_edit' },
                // Purchase management
                { selector: '[onclick*="purchase_product.php"]',                       perm: 'purchase_add'   },
                // Delete actions (non-sale)
                { selector: '[onclick*="deleteProduct"]',                              perm: 'product_delete' },
                { selector: '[onclick*="deleteCompany"]',                              perm: 'company_delete' },
                { selector: '[onclick*="deletePurchase"]',                             perm: 'purchase_delete'},
            ];

            permissionMap.forEach(({ selector, perm }) => {
                if (!hasPermission(perm)) {
                    document.querySelectorAll(selector).forEach(btn => {
                        if (btn) disableButton(btn);
                    });
                }
            });

            // Handle data-permission attributes (explicit, always respected)
            document.querySelectorAll('[data-permission]').forEach(el => {
                if (!hasPermission(el.getAttribute('data-permission'))) disableButton(el);
            });

            // Show staff notice once
            const main = document.querySelector('main');
            if (main && !document.getElementById('staff-notice')) {
                const notice = document.createElement('div');
                notice.id = 'staff-notice';
                notice.className = 'mt-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg flex items-center gap-3';
                notice.innerHTML = '<i class="fas fa-info-circle text-yellow-500"></i><span>You have limited access. Contact an Admin for additional permissions.</span>';
                main.appendChild(notice);
            }
        }
        
        document.addEventListener('DOMContentLoaded', disableAllRestrictedButtons);
        window.addEventListener('load', disableAllRestrictedButtons);
        <?php else: ?>
        // Admin has full access - provide helper functions without restrictions
        function hasPermission(p) { return true; }
        function withPermission(p, callback) { callback(); }
        function confirmWithPermission(p, msg, callback) { if (confirm(msg)) callback(); }
        <?php endif; ?>
    </script>
    <?php
}
?>