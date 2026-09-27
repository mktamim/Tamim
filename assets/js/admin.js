// Admin JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Confirm delete
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // Auto-submit forms on select change
    document.querySelectorAll('select[data-auto-submit]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

    // Image preview
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function(input) {
        input.addEventListener('change', function() {
            const previewId = this.dataset.preview;
            const preview = document.getElementById(previewId);
            if (preview && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Sortable tables (drag and drop for sort order)
    if (typeof Sortable !== 'undefined') {
        document.querySelectorAll('.sortable-table tbody').forEach(function(tbody) {
            new Sortable(tbody, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: function(evt) {
                    const rows = tbody.querySelectorAll('tr');
                    const ids = [];
                    rows.forEach(function(row, index) {
                        const id = row.dataset.id;
                        if (id) {
                            ids.push({ id: id, sort_order: index + 1 });
                        }
                    });
                    if (ids.length > 0) {
                        updateSortOrder(ids, tbody.dataset.updateUrl);
                    }
                }
            });
        });
    }

    // Update sort order via AJAX
    function updateSortOrder(ids, url) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('input[name="csrf_token"]')?.value || ''
            },
            body: JSON.stringify({ items: ids })
        }).then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Sort order update failed:', data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            location.reload();
        });
    }

    // Delete item via AJAX
    window.deleteItem = function(id, url, confirmMessage = 'Are you sure you want to delete this item?') {
        if (!confirm(confirmMessage)) return;

        const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({ 
                action: 'delete',
                id: id,
                csrf_token: csrfToken
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    };

    // Toggle status via AJAX
    window.toggleStatus = function(id, url, currentStatus) {
        const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({ 
                action: 'toggle_status',
                id: id,
                status: currentStatus ? 0 : 1,
                csrf_token: csrfToken
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update status'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    };

    // Form validation
    document.querySelectorAll('form[data-validate]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Email validation
            form.querySelectorAll('input[type="email"]').forEach(function(field) {
                if (field.value && !isValidEmail(field.value)) {
                    valid = false;
                    field.classList.add('is-invalid');
                }
            });

            // URL validation
            form.querySelectorAll('input[type="url"]').forEach(function(field) {
                if (field.value && !isValidUrl(field.value)) {
                    valid = false;
                    field.classList.add('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault();
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
    });

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }

    // Character counter for textareas
    document.querySelectorAll('textarea[maxlength]').forEach(function(textarea) {
        const maxLength = parseInt(textarea.getAttribute('maxlength'));
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        counter.innerHTML = '<span class="current">0</span> / ' + maxLength;
        textarea.parentNode.appendChild(counter);
        
        const currentSpan = counter.querySelector('.current');
        
        function updateCounter() {
            const length = textarea.value.length;
            currentSpan.textContent = length;
            if (length > maxLength * 0.9) {
                currentSpan.classList.add('text-warning');
            }
            if (length >= maxLength) {
                currentSpan.classList.add('text-danger');
                currentSpan.classList.remove('text-warning');
            } else {
                currentSpan.classList.remove('text-danger', 'text-warning');
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });

    // Rich text editor initialization (simple)
    document.querySelectorAll('textarea[data-rich]').forEach(function(textarea) {
        // Could integrate a lightweight editor like TinyMCE or Quill here
        // For now, just add a class for styling
        textarea.classList.add('rich-text-area');
    });

    // Color picker preview
    document.querySelectorAll('input[type="color"]').forEach(function(input) {
        const preview = document.createElement('div');
        preview.className = 'color-preview';
        preview.style.cssText = 'width: 30px; height: 30px; border-radius: 6px; border: 1px solid #ddd; margin-top: 8px;';
        preview.style.backgroundColor = input.value;
        input.parentNode.appendChild(preview);
        
        input.addEventListener('input', function() {
            preview.style.backgroundColor = this.value;
        });
    });

    // Dynamic form fields (add/remove)
    document.querySelectorAll('[data-dynamic-fields]').forEach(function(container) {
        const template = container.querySelector('template');
        const addBtn = container.querySelector('[data-add-field]');
        
        if (addBtn && template) {
            addBtn.addEventListener('click', function() {
                const clone = template.content.cloneNode(true);
                container.querySelector('.dynamic-fields-list').appendChild(clone);
                
                // Re-initialize any components in the new field
                const newSelect = clone.querySelector('select');
                if (newSelect) {
                    // Re-initialize select2 or similar if used
                }
            });
        }
        
        // Remove field
        container.addEventListener('click', function(e) {
            if (e.target.matches('[data-remove-field]')) {
                e.target.closest('.dynamic-field-item').remove();
            }
        });
    });

    // Search/filter tables
    document.querySelectorAll('[data-table-filter]').forEach(function(input) {
        const tableId = input.dataset.tableFilter;
        const table = document.getElementById(tableId);
        
        if (table) {
            input.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(function(row) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        }
    });

    // Print functionality
    window.printPage = function() {
        window.print();
    };

    // Export table to CSV
    window.exportTableToCSV = function(tableId, filename = 'export.csv') {
        const table = document.getElementById(tableId);
        if (!table) return;
        
        const rows = table.querySelectorAll('tr');
        const csv = [];
        
        rows.forEach(function(row) {
            const cells = row.querySelectorAll('th, td');
            const rowData = [];
            cells.forEach(function(cell) {
                rowData.push('"' + cell.textContent.replace(/"/g, '""') + '"');
            });
            csv.push(rowData.join(','));
        });
        
        const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        URL.revokeObjectURL(url);
    };
});

// Utility functions
const AdminUtils = {
    // Show toast notification
    toast: function(message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer') || this.createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    },
    
    createToastContainer: function() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1055';
        document.body.appendChild(container);
        return container;
    },
    
    // Format number
    formatNumber: function(num) {
        return new Intl.NumberFormat().format(num);
    },
    
    // Format date
    formatDate: function(dateString, options = {}) {
        const defaultOptions = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, { ...defaultOptions, ...options });
    },
    
    // Debounce
    debounce: function(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
};

// Make AdminUtils globally available
window.AdminUtils = AdminUtils;