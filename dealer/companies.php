<?php
require_once 'includes/auth_check.php';
$pageTitle  = 'Companies';
$activePage = 'companies';

$error   = $_SESSION['rgt_error_message']   ?? '';


?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/header.php'; ?>
</head>
<body class="bg-[var(--bg)]">
<?php require_once 'includes/sidebar.php'; ?>

<div class="md:ml-64 pb-16 md:pb-0">

    <!-- Page Content -->
    <main class="p-6 md:p-8">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-[var(--text)]">Companies Management</h2>
            <button onclick="openAddProductModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                <i class="fas fa-plus"></i>
                <span>Add Companies</span>
            </button>
        </div>

        <!-- Flash messages -->
        <?php if (!empty($_SESSION['success'])): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('<?= addslashes($_SESSION['success']) ?>', 'success'); });</script>
        <?php unset($_SESSION['success']); endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('<?= addslashes($_SESSION['error']) ?>', 'error'); });</script>
        <?php unset($_SESSION['error']); endif; ?>
        <?php if (!empty($_SESSION['rgt_error_message'])): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('<?= addslashes($_SESSION['rgt_error_message']) ?>', 'error'); });</script>
        <?php unset($_SESSION['rgt_error_message']); endif; ?>

        <!-- Companies Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <div class="p-6">
                
                <div class="table-responsive">
                    <table id="companiesTable" class="table w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-[var(--border)]">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">No.</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">dealer_id</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Company Name</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Owner Name</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Phone</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Email</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Balance</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- Add Company Modal -->
<div id="addProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Add New Company</h3>
            <button onclick="closeAddProductModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-times text-gray-500"></i>
            </button>
        </div>

       
        <?php if ($error): ?>
        <div class="mb-5 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form id="addCompanyForm" action="add_company.php" method="POST" class="p-6">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                    <input type="text" name="company_name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person Name</label>
                    <input type="text" name="contact_person_name"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone No.</label>
                    <input type="number" name="phone_no" pattern="[0-9]{10}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeAddProductModal()"
                        class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                    Add Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Company Modal -->
<div id="editProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Edit Company</h3>
            <button onclick="closeEditProductModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-times text-gray-500"></i>
            </button>
        </div>

        <?php if ($error): ?>
        <div class="mb-5 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form id="editCompanyForm" action="edit_company.php" method="POST" class="p-6">
            <input type="hidden" name="comp_id" value="">

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Owner Name</label>
                    <input type="text" name="owner_name" value="Rice (1kg)" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Owner Contact</label>
                    <input type="text" name="owner_contact" value="1234567890" pattern="[0-9]{10}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="edit_email" value="" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeEditProductModal()"
                        class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
    function openAddProductModal() {
        document.getElementById('addProductModal').classList.remove('hidden');
    }
    function closeAddProductModal() {
        document.getElementById('addProductModal').classList.add('hidden');
    }
    function openEditProductModal(id, companyName, contactPerson, phone, email) {
        document.getElementById('editProductModal').classList.remove('hidden');
        document.querySelector('input[name="comp_id"]').value         = id;
        document.querySelector('input[name="owner_name"]').value      = contactPerson;
        document.querySelector('input[name="owner_contact"]').value   = phone;
        document.querySelector('input[name="edit_email"]').value      = email;
    }
    function closeEditProductModal() {
        document.getElementById('editProductModal').classList.add('hidden');
    }
    document.getElementById('addProductModal').addEventListener('click', function(e) {
        if (e.target === this) closeAddProductModal();
    });
    document.getElementById('editProductModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditProductModal();
    });

    function _setSubmitting(btn, busy) {
        btn.disabled = busy;
        btn.textContent = busy ? 'Saving…' : btn.dataset.label;
    }

    // ── Add Company (async) ───────────────────────────────────────
    document.getElementById('addCompanyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = this.querySelector('[type="submit"]');
        btn.dataset.label = btn.textContent;
        _setSubmitting(btn, true);
        fetch('add_company.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(this)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            _setSubmitting(btn, false);
            if (res.success) {
                showToast(res.message, 'success');
                closeAddProductModal();
                document.getElementById('addCompanyForm').reset();
                jQuery('#companiesTable').DataTable().ajax.reload(null, false);
            } else {
                showToast(res.message || 'Failed to add company.', 'error');
            }
        })
        .catch(function() { _setSubmitting(btn, false); showToast('Network error.', 'error'); });
    });

    // ── Edit Company (async) ──────────────────────────────────────
    document.getElementById('editCompanyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = this.querySelector('[type="submit"]');
        btn.dataset.label = btn.textContent;
        _setSubmitting(btn, true);
        fetch('edit_company.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(this)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            _setSubmitting(btn, false);
            if (res.success) {
                showToast(res.message, 'success');
                closeEditProductModal();
                jQuery('#companiesTable').DataTable().ajax.reload(null, false);
            } else {
                showToast(res.message || 'Failed to update company.', 'error');
            }
        })
        .catch(function() { _setSubmitting(btn, false); showToast('Network error.', 'error'); });
    });
</script>
</body>
</html>
