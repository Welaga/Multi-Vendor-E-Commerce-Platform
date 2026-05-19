/* ============================================================
   MarketHub — main.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Sidebar Toggle ----
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar   = document.getElementById('sidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            const isMobile = window.innerWidth <= 768;
            if (isMobile) {
                sidebar.classList.toggle('mobile-open');
            } else {
                sidebar.classList.toggle('collapsed');
                const content = document.getElementById('adminContent');
                if (content) {
                    content.style.width = sidebar.classList.contains('collapsed')
                        ? '100%' : 'calc(100% - 240px)';
                }
            }
        });

        // Close sidebar on mobile overlay click
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 768
                && sidebar.classList.contains('mobile-open')
                && !sidebar.contains(e.target)
                && e.target !== toggleBtn) {
                sidebar.classList.remove('mobile-open');
            }
        });
    }

    // ---- Password Toggle Utility ----
    window.togglePw = function (fieldId, btn) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        const isText = field.type === 'text';
        field.type = isText ? 'password' : 'text';
        const icon = btn.querySelector('i');
        if (icon) {
            icon.className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
        }
    };

    // ---- Auto-dismiss Alerts after 5s ----
    document.querySelectorAll('.alert.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

    // ---- Uppercase Coupon Input ----
    const couponInput = document.querySelector('input[name="coupon_code"]');
    if (couponInput) {
        couponInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    }

    // ---- Confirm Delete links (extra safety) ----
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });

    // ---- Price Range Sync Labels (if sliders used) ----
    const minInput = document.querySelector('input[name="min"]');
    const maxInput = document.querySelector('input[name="max"]');
    if (minInput && maxInput) {
        minInput.addEventListener('change', function () {
            if (parseFloat(maxInput.value) && parseFloat(this.value) > parseFloat(maxInput.value)) {
                maxInput.value = this.value;
            }
        });
    }

    // ---- Bootstrap Tooltips ----
    const tooltipEls = document.querySelectorAll('[title]');
    tooltipEls.forEach(function (el) {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });

    // ---- Image Preview Helper (global) ----
    window.previewImage = function (input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.getElementById(previewId);
                const wrap = document.getElementById(previewId + 'Wrap');
                if (img) img.src = e.target.result;
                if (wrap) wrap.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    // ---- Quantity Step ----
    window.stepQty = function (delta) {
        const inp = document.getElementById('qtyInput');
        if (!inp) return;
        const max = parseInt(inp.max) || 9999;
        inp.value = Math.max(1, Math.min(max, parseInt(inp.value || 1) + delta));
    };

    // ---- Icon Preview for Category Form ----
    const iconInput   = document.getElementById('iconInput');
    const iconPreview = document.getElementById('iconPreview');
    if (iconInput && iconPreview) {
        iconInput.addEventListener('input', function () {
            iconPreview.innerHTML = '<i class="bi ' + this.value + '"></i>';
        });
    }

    // ---- Table Search Filter ----
    const tableSearch = document.getElementById('tableSearch');
    if (tableSearch) {
        tableSearch.addEventListener('input', function () {
            const q    = this.value.toLowerCase();
            const rows = document.querySelectorAll('[data-table-row]');
            rows.forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // ---- Cart quantity: auto-submit on change ----
    document.querySelectorAll('.cart-qty-input').forEach(function (inp) {
        inp.addEventListener('change', function () {
            this.closest('form').submit();
        });
    });

    // ---- Fix: prevent double form submit ----
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('[type="submit"]');
            if (btn && !btn.dataset.noDisable) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Please wait…';
            }
        });
    });

});
