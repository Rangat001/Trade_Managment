<?php
require_once 'includes/auth_check.php';
$pageTitle  = 'Staff';
$activePage = 'staff';

// Fetch staff members
$stmt = $conn->prepare("SELECT id, name, email, role, is_verified FROM users WHERE dealer_id = ? and is_active = 1");
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$staff_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/header.php'; ?>
</head>
<body class="bg-[var(--bg)]">
<?php require_once 'includes/sidebar.php'; ?>

<div class="md:ml-64 pb-16 md:pb-0">

    <!-- Session flash messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <script>document.addEventListener('DOMContentLoaded', function() { showToast('<?= addslashes($_SESSION['success']) ?>', 'success'); });</script>
    <?php unset($_SESSION['success']); endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <script>document.addEventListener('DOMContentLoaded', function() { showToast('<?= addslashes($_SESSION['error']) ?>', 'error'); });</script>
    <?php unset($_SESSION['error']); endif; ?>

    <!-- Page Content -->
    <main class="p-6 md:p-8">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-[var(--text)]">Staff Members</h2>
            <button onclick="openAddStaffModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#4F46E5] to-indigo-500 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                <i class="fas fa-plus"></i>
                <span>Add Staff</span>
            </button>
        </div>

        <!-- Staff Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-[var(--border)]">
                            <th class="py-3 px-6 text-left text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Name</th>
                            <th class="py-3 px-6 text-left text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Email</th>
                            <th class="py-3 px-6 text-left text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Role</th>
                            <th class="py-3 px-6 text-left text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <?php while ($staff = $staff_result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6 text-sm font-medium text-[var(--text)]"><?php echo htmlspecialchars($staff['name']); ?></td>
                            <td class="py-4 px-6 text-sm text-[var(--text)]"><?php echo htmlspecialchars($staff['email']); ?></td>
                            <td class="py-4 px-6">
                                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full <?php echo $staff['role'] === 'ADMIN' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'; ?>">
                                    <?php echo $staff['role']; ?>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <button
                                        data-id="<?= $staff['id'] ?>"
                                        data-name="<?= htmlspecialchars($staff['name']) ?>"
                                        data-email="<?= htmlspecialchars($staff['email']) ?>"
                                        data-role="<?= htmlspecialchars($staff['role']) ?>"
                                        onclick="openEditStaffModal(this)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button onclick="disableStaff(<?php echo $staff['id']; ?>)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition-colors">
                                        <i class="fas fa-ban"></i> Disable
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- Add Staff Modal -->
<div id="addStaffModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Add Staff Member</h3>
            <button onclick="closeAddStaffModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-times text-gray-500"></i>
            </button>
        </div>

        <form action="add_staff.php" method="POST" class="p-6">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="staff_name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="staff_email" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                    <select name="staff_role" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <option value="STAFF">Staff</option>
                        <option value="ADMIN">Admin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                    <input type="password" name="staff_password" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeAddStaffModal()"
                        class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-[#4F46E5] to-indigo-500 text-white font-medium rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                    Add Staff
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Staff Modal -->
<div id="editStaffModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Edit Staff Member</h3>
            <button onclick="closeEditStaffModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-times text-gray-500"></i>
            </button>
        </div>

        <form id="editStaffForm" class="p-6">
            <input type="hidden" name="staff_id" id="edit_staff_id" value="1">

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="staff_name" id="edit_staff_name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="staff_email" id="edit_staff_email" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                    <select name="staff_role" id="edit_staff_role" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <option value="STAFF">Staff</option>
                        <option value="ADMIN">Admin</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeEditStaffModal()"
                        class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="saveStaff()"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-[#4F46E5] to-indigo-500 text-white font-medium rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                    Update Staff
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script>
    // ── Modal helpers ────────────────────────────────────────────
    function openAddStaffModal() {
        document.getElementById('addStaffModal').classList.remove('hidden');
    }

    function closeAddStaffModal() {
        document.getElementById('addStaffModal').classList.add('hidden');
    }

    function openEditStaffModal(btn) {
        document.getElementById('edit_staff_id').value    = btn.dataset.id;
        document.getElementById('edit_staff_name').value  = btn.dataset.name;
        document.getElementById('edit_staff_email').value = btn.dataset.email;
        document.getElementById('edit_staff_role').value  = btn.dataset.role;
        document.getElementById('editStaffModal').classList.remove('hidden');
    }

    function closeEditStaffModal() {
        document.getElementById('editStaffModal').classList.add('hidden');
    }

    // Close modals on backdrop click
    document.getElementById('addStaffModal').addEventListener('click', function(e) {
        if (e.target === this) closeAddStaffModal();
    });

    document.getElementById('editStaffModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditStaffModal();
    });

    // ── Save staff (edit) ────────────────────────────────────────
    function saveStaff() {
        var formData = new FormData();
        formData.append('staff_id',    document.getElementById('edit_staff_id').value);
        formData.append('staff_name',  document.getElementById('edit_staff_name').value);
        formData.append('staff_email', document.getElementById('edit_staff_email').value);
        formData.append('staff_role',  document.getElementById('edit_staff_role').value);
        fetch('ajax/edit_staff.php', { method: 'POST', body: formData })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) {
                    showToast('Staff updated successfully', 'success');
                    closeEditStaffModal();
                    setTimeout(function() { location.reload(); }, 800);
                } else {
                    showToast(res.message || 'Failed to update staff', 'error');
                }
            })
            .catch(function() { showToast('Network error. Please try again.', 'error'); });
    }

    // ── Disable staff ────────────────────────────────────────────
    function disableStaff(staff_id) {
        showConfirm('Disable Staff', 'Are you sure you want to disable this staff member?')
            .then(function(confirmed) {
                if (!confirmed) return;
                var formData = new FormData();
                formData.append('staff_id', staff_id);
                fetch('ajax/disable_staff.php', { method: 'POST', body: formData })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res.success) {
                            showToast('Staff disabled successfully', 'success');
                            setTimeout(function() { location.reload(); }, 800);
                        } else {
                            showToast(res.message || 'Failed to disable staff', 'error');
                        }
                    })
                    .catch(function() { showToast('Network error. Please try again.', 'error'); });
            });
    }
</script>
</body>
</html>
