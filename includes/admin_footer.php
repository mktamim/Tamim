<?php
/**
 * Admin Footer
 * Portfolio Website - Admin Includes
 */
?>
                    </div>
                </main>
            </div>
        </div>

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Custom Admin JS -->
        <script src="<?= asset('js/admin.js') ?>"></script>
        
        <script>
            // Initialize sidebar
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('adminSidebar');
                const toggle = document.getElementById('sidebarToggle');
                const mobileToggle = document.getElementById('mobileSidebarToggle');
                const overlay = document.getElementById('sidebarOverlay');
                
                // Desktop toggle
                if (toggle) {
                    toggle.addEventListener('click', function() {
                        sidebar.classList.toggle('collapsed');
                        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                    });
                }
                
                // Mobile toggle
                if (mobileToggle) {
                    mobileToggle.addEventListener('click', function() {
                        sidebar.classList.add('mobile-open');
                        overlay.classList.add('active');
                    });
                }
                
                // Close on overlay click
                if (overlay) {
                    overlay.addEventListener('click', function() {
                        sidebar.classList.remove('mobile-open');
                        overlay.classList.remove('active');
                    });
                }
                
                // Restore sidebar state
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    sidebar.classList.add('collapsed');
                }
                
                // Auto-dismiss alerts
                document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                    setTimeout(function() {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                });
                
                // Confirm delete actions
                document.querySelectorAll('[data-confirm]').forEach(function(el) {
                    el.addEventListener('click', function(e) {
                        if (!confirm(this.dataset.confirm)) {
                            e.preventDefault();
                        }
                    });
                });
            });
        </script>
    </body>
</html>