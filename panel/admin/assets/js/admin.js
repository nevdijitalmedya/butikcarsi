/**
 * ButikÃ‡arÅŸÄ± Admin Panel â€” JavaScript
 */
document.addEventListener('DOMContentLoaded', () => {
    // Sidebar toggle for mobile
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
        // Close sidebar on outside click (mobile)
        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }

    // Confirm delete actions
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm || 'Emin misiniz?')) {
                e.preventDefault();
            }
        });
    });

    // Auto-generate slug from name
    const nameInput = document.getElementById('name') || document.getElementById('brand_name');
    const slugInput = document.getElementById('slug');
    if (nameInput && slugInput && !slugInput.value) {
        nameInput.addEventListener('input', () => {
            const tr = {'Ã§':'c','Ã‡':'c','ÄŸ':'g','Ä':'g','Ä±':'i','Ä°':'i','Ã¶':'o','Ã–':'o','ÅŸ':'s','Å':'s','Ã¼':'u','Ãœ':'u'};
            let slug = nameInput.value.toLowerCase();
            Object.entries(tr).forEach(([k, v]) => { slug = slug.replace(new RegExp(k, 'g'), v); });
            slug = slug.replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-').replace(/^-|-$/g, '');
            slugInput.value = slug;
        });
    }

    // Image preview
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', (e) => {
            const previewId = input.dataset.preview;
            const preview = document.getElementById(previewId);
            if (preview && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    });
});
