<?php
/**
 * footer.php — Shared script loading, Toast container, Confirm Modal
 *
 * Usage: require_once 'includes/footer.php'; just before </body>
 *
 * Scripts loaded in order:
 *   1. jQuery 3.6.0
 *   2. jQuery DataTables
 *   3. DataTables Bootstrap 4 plugin
 *   4. Bootstrap 5 bundle
 *   5. app.js (Toast, Confirm Modal, sidebar toggle, DataTables init)
 */
?>

    <!-- ── Scripts ─────────────────────────────────────────── -->
    <script src="../asset/js/jquery-3.6.0.min.js"></script>
    <script src="../asset/js/jquery.dataTables.min.js"></script>
    <script src="../asset/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>

    <!-- ── Toast Container ─────────────────────────────────── -->
    <div id="toastContainer"
         class="fixed bottom-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none">
    </div>

    <!-- ── Confirm Modal ───────────────────────────────────── -->
    <div id="confirmModal"
         class="hidden fixed inset-0 z-[9998] flex items-center justify-center p-4">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <!-- Dialog -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-auto overflow-hidden">

            <!-- Header -->
            <div class="px-6 pt-6 pb-2">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                    </div>
                    <h3 id="confirmTitle" class="text-lg font-semibold text-[var(--text)]">Confirm</h3>
                </div>
                <p id="confirmMessage" class="text-sm text-[var(--subtext)] ml-13 pl-1">Are you sure?</p>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 px-6 py-5">
                <button id="confirmCancelBtn"
                        class="flex-1 py-2.5 px-4 bg-gray-100 text-[var(--text)] font-medium rounded-xl hover:bg-gray-200 transition-colors text-sm">
                    Cancel
                </button>
                <button id="confirmOkBtn"
                        class="flex-1 py-2.5 px-4 bg-red-500 text-white font-medium rounded-xl hover:bg-red-600 transition-colors text-sm">
                    Confirm
                </button>
            </div>

        </div>
    </div>
