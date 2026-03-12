<?php
/**
 * JavaScript permission helpers
 * Include this in the <head> or before closing </body>
 */
?>
<script>
    // Permission configuration from PHP
    const userPermissions = <?php echo json_encode($user_permissions); ?>;
    const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;
    const isStaff = <?php echo $is_staff ? 'true' : 'false'; ?>;
    const currentUserRole = '<?php echo $current_user_role; ?>';
    
    /**
     * Check if user has a specific permission
     * @param {string} permission - Permission key to check
     * @returns {boolean}
     */
    function hasPermission(permission) {
        return userPermissions[permission] === true;
    }
    
    /**
     * Execute callback only if user has permission
     * @param {string} permission - Permission key required
     * @param {Function} callback - Function to execute if permitted
     * @param {string} errorMessage - Optional custom error message
     */
    function withPermission(permission, callback, errorMessage = null) {
        if (hasPermission(permission)) {
            callback();
        } else {
            const message = errorMessage || 'You do not have permission to perform this action.';
            showPermissionError(message);
        }
    }
    
    /**
     * Show permission error notification
     * @param {string} message - Error message to display
     */
    function showPermissionError(message) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 z-[9999] bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in';
        toast.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('animate-slide-out');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    /**
     * Confirm action with permission check
     * @param {string} permission - Permission key required
     * @param {string} confirmMessage - Confirmation message
     * @param {Function} callback - Function to execute if confirmed and permitted
     */
    function confirmWithPermission(permission, confirmMessage, callback) {
        if (!hasPermission(permission)) {
            showPermissionError('You do not have permission to perform this action.');
            return;
        }
        
        if (confirm(confirmMessage)) {
            callback();
        }
    }
    
    /**
     * Disable a single button element
     * @param {HTMLElement} button - Button element to disable
     */
    function disableButton(button) {
        button.disabled = true;
        button.classList.add('cursor-not-allowed', 'opacity-50', 'pointer-events-none');
        button.classList.remove('hover:bg-gray-200', 'hover:bg-red-200', 'hover:bg-blue-200', 'hover:shadow-xl');
        button.style.pointerEvents = 'none';
        
        // Store original onclick and remove it
        if (button.onclick) {
            button.setAttribute('data-original-onclick', button.onclick.toString());
            button.onclick = null;
        }
        
        // Remove onclick attribute
        if (button.hasAttribute('onclick')) {
            button.setAttribute('data-original-onclick-attr', button.getAttribute('onclick'));
            button.removeAttribute('onclick');
        }
        
        // Add tooltip
        button.title = 'You do not have permission to perform this action';
    }
    
    /**
     * Disable all buttons in the page for STAFF users
     * This is the main function that handles all button disabling
     */
    function disableAllButtonsForStaff() {
        if (isAdmin) return; // Admin has full access
        
        // Get current page name
        const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
        
        // Define which buttons to disable based on page
        const pagePermissions = {
            'staff': {
                addButtons: 'staff_add',
                editButtons: 'staff_edit',
                deleteButtons: 'staff_delete'
            },
            'products': {
                addButtons: 'product_add',
                editButtons: 'product_edit',
                deleteButtons: 'product_delete'
            },
            'companies': {
                addButtons: 'company_add',
                editButtons: 'company_edit',
                deleteButtons: 'company_delete'
            },
            'purchases': {
                addButtons: 'purchase_add',
                editButtons: 'purchase_edit',
                deleteButtons: 'purchase_delete'
            },
            'sales': {
                addButtons: 'sales_add',
                editButtons: 'sales_edit',
                deleteButtons: 'sales_delete'
            },
            'reports': {
                exportButtons: 'reports_export'
            },
            'settings': {
                allButtons: 'settings_edit'
            }
        };
        
        // Get permissions for current page
        const pageConfig = pagePermissions[currentPage];
        
        // 1. Disable all Add buttons if no add permission
        if (pageConfig?.addButtons && !hasPermission(pageConfig.addButtons)) {
            disableButtonsBySelector([
                'button[onclick*="openAdd"]',
                'button[onclick*="Add"]',
                'a[href*="add"]',
                '.btn-add',
                '[data-action="add"]'
            ]);
        }
        
        // 2. Disable all Edit buttons if no edit permission
        if (pageConfig?.editButtons && !hasPermission(pageConfig.editButtons)) {
            disableButtonsBySelector([
                'button[onclick*="openEdit"]',
                'button[onclick*="Edit"]',
                'button[onclick*="edit"]',
                'a[href*="edit"]',
                '.btn-edit',
                '[data-action="edit"]',
                'button:has(.fa-edit)',
                'button:has(.fa-pen)',
                'button:has(.fa-pencil)'
            ]);
            
            // Also disable by icon class
            document.querySelectorAll('.fa-edit, .fa-pen, .fa-pencil').forEach(icon => {
                const button = icon.closest('button') || icon.closest('a');
                if (button) disableButton(button);
            });
        }
        
        // 3. Disable all Delete/Disable buttons if no delete permission
        if (pageConfig?.deleteButtons && !hasPermission(pageConfig.deleteButtons)) {
            disableButtonsBySelector([
                'button[onclick*="delete"]',
                'button[onclick*="Delete"]',
                'button[onclick*="disable"]',
                'button[onclick*="Disable"]',
                'button[onclick*="remove"]',
                'a[href*="delete"]',
                'a[href*="disable"]',
                '.btn-delete',
                '.btn-danger',
                '[data-action="delete"]',
                '[data-action="disable"]'
            ]);
            
            // Also disable by icon class
            document.querySelectorAll('.fa-trash, .fa-trash-alt, .fa-ban, .fa-times-circle').forEach(icon => {
                const button = icon.closest('button') || icon.closest('a');
                if (button) disableButton(button);
            });
        }
        
        // 4. Disable export buttons if no export permission
        if (pageConfig?.exportButtons && !hasPermission(pageConfig.exportButtons)) {
            disableButtonsBySelector([
                'button[onclick*="export"]',
                'button[onclick*="Export"]',
                'button[onclick*="download"]',
                'a[href*="export"]',
                '.btn-export',
                '[data-action="export"]'
            ]);
            
            document.querySelectorAll('.fa-download, .fa-file-export, .fa-file-pdf, .fa-file-excel').forEach(icon => {
                const button = icon.closest('button') || icon.closest('a');
                if (button) disableButton(button);
            });
        }
        
        // 5. Disable all buttons on settings page if no settings permission
        if (pageConfig?.allButtons && !hasPermission(pageConfig.allButtons)) {
            document.querySelectorAll('button[type="submit"], button[onclick], .btn-primary').forEach(button => {
                disableButton(button);
            });
        }
        
        // 6. Handle elements with data-permission attribute
        document.querySelectorAll('[data-permission]').forEach(element => {
            const permission = element.getAttribute('data-permission');
            if (!hasPermission(permission)) {
                disableButton(element);
            }
        });
        
        // 7. Hide elements with data-permission-hide attribute
        document.querySelectorAll('[data-permission-hide]').forEach(element => {
            const permission = element.getAttribute('data-permission-hide');
            if (!hasPermission(permission)) {
                element.style.display = 'none';
            }
        });
        
        // 8. Disable form submissions in modals
        disableFormsWithoutPermission();
        
        // 9. Show permission notice if staff
        showStaffPermissionNotice();
    }
    
    /**
     * Disable buttons matching any of the given selectors
     * @param {Array} selectors - Array of CSS selectors
     */
    function disableButtonsBySelector(selectors) {
        selectors.forEach(selector => {
            try {
                document.querySelectorAll(selector).forEach(button => {
                    disableButton(button);
                });
            } catch (e) {
                // Selector might not be valid, skip it
            }
        });
    }
    
    /**
     * Disable form submissions that require permissions
     */
    function disableFormsWithoutPermission() {
        // Get current page
        const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
        
        const formPermissions = {
            'staff': { add: 'staff_add', edit: 'staff_edit' },
            'products': { add: 'product_add', edit: 'product_edit' },
            'companies': { add: 'company_add', edit: 'company_edit' },
            'purchases': { add: 'purchase_add', edit: 'purchase_edit' },
            'sales': { add: 'sales_add', edit: 'sales_edit' },
            'settings': { edit: 'settings_edit' }
        };
        
        const pageFormConfig = formPermissions[currentPage];
        if (!pageFormConfig) return;
        
        // Disable add forms
        if (pageFormConfig.add && !hasPermission(pageFormConfig.add)) {
            document.querySelectorAll('form[id*="add"], form[id*="Add"], form[action*="add"]').forEach(form => {
                disableForm(form);
            });
        }
        
        // Disable edit forms
        if (pageFormConfig.edit && !hasPermission(pageFormConfig.edit)) {
            document.querySelectorAll('form[id*="edit"], form[id*="Edit"], form[action*="edit"]').forEach(form => {
                disableForm(form);
            });
        }
    }
    
    /**
     * Disable a form and all its inputs
     * @param {HTMLFormElement} form - Form element to disable
     */
    function disableForm(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            showPermissionError('You do not have permission to submit this form.');
        });
        
        // Disable submit buttons
        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(btn => {
            disableButton(btn);
        });
    }
    
    /**
     * Show a subtle notice for staff users about limited permissions
     */
    function showStaffPermissionNotice() {
        if (!isStaff) return;
        
        // Check if notice already exists
        if (document.getElementById('staff-permission-notice')) return;
        
        // Check if there's a main content area
        const mainContent = document.querySelector('main') || document.querySelector('.main-content');
        if (!mainContent) return;
        
        // Check if notice placeholder exists
        const existingNotice = mainContent.querySelector('.permission-notice');
        if (existingNotice) return;
        
        // Create notice element
        const notice = document.createElement('div');
        notice.id = 'staff-permission-notice';
        notice.className = 'mt-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg flex items-center gap-3';
        notice.innerHTML = `
            <i class="fas fa-info-circle text-yellow-500"></i>
            <span>You have limited access as a Staff member. Some actions are restricted. Contact an Admin for additional permissions.</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-yellow-500 hover:text-yellow-700">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Append at the end of main content
        mainContent.appendChild(notice);
    }
    
    /**
     * Protect specific action by checking permission before execution
     * Use this to wrap existing functions
     * @param {string} permission - Required permission
     * @param {Function} originalFunction - Original function to wrap
     * @returns {Function} - Wrapped function with permission check
     */
    function protectAction(permission, originalFunction) {
        return function(...args) {
            if (hasPermission(permission)) {
                return originalFunction.apply(this, args);
            } else {
                showPermissionError('You do not have permission to perform this action.');
                return false;
            }
        };
    }
    
    /**
     * Disable buttons by text content
     * @param {Array} textPatterns - Array of text patterns to match
     */
    function disableButtonsByText(textPatterns) {
        document.querySelectorAll('button, a.btn, [role="button"]').forEach(button => {
            const buttonText = button.textContent.toLowerCase().trim();
            textPatterns.forEach(pattern => {
                if (buttonText.includes(pattern.toLowerCase())) {
                    disableButton(button);
                }
            });
        });
    }
    
    /**
     * Quick method to disable all CRUD buttons for staff
     */
    function disableAllCrudButtons() {
        if (isAdmin) return;
        
        // Disable by common button text
        const restrictedTexts = ['add', 'create', 'new', 'edit', 'update', 'delete', 'remove', 'disable'];
        
        document.querySelectorAll('button, a').forEach(element => {
            const text = element.textContent.toLowerCase();
            const hasRestrictedText = restrictedTexts.some(t => text.includes(t));
            
            if (hasRestrictedText) {
                // Check if this is a view-related element (should remain enabled)
                if (text.includes('view') || text.includes('show') || text.includes('details')) {
                    return;
                }
                disableButton(element);
            }
        });
        
        // Disable by common icons
        const restrictedIcons = [
            '.fa-plus', '.fa-plus-circle',
            '.fa-edit', '.fa-pen', '.fa-pencil', '.fa-pencil-alt',
            '.fa-trash', '.fa-trash-alt', '.fa-ban', '.fa-times-circle'
        ];
        
        restrictedIcons.forEach(iconClass => {
            document.querySelectorAll(iconClass).forEach(icon => {
                const button = icon.closest('button') || icon.closest('a');
                if (button) {
                    // Don't disable if it's a close/cancel button for modals
                    if (button.closest('.modal-header') || button.getAttribute('onclick')?.includes('close')) {
                        return;
                    }
                    disableButton(button);
                }
            });
        });
    }
    
    // Add CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
        .animate-slide-out { animation: slideOut 0.3s ease-in; }
        
        /* Disabled button styles */
        button:disabled, a.disabled, [disabled] {
            cursor: not-allowed !important;
            opacity: 0.5 !important;
            pointer-events: none !important;
        }
    `;
    document.head.appendChild(style);
    
    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        // Run the main disable function
        disableAllButtonsForStaff();
        
        // Also run the quick CRUD disable as backup
        if (isStaff) {
            disableAllCrudButtons();
        }
        
        // Observe DOM changes for dynamically added elements
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    // Re-run permission checks on newly added elements
                    setTimeout(() => {
                        if (isStaff) {
                            disableAllCrudButtons();
                        }
                    }, 100);
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
    
    // Also run on window load as backup
    window.addEventListener('load', function() {
        if (isStaff) {
            disableAllButtonsForStaff();
            disableAllCrudButtons();
        }
    });
</script>