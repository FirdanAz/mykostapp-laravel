import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// ── Global CSRF setup (for fetch) ────────────────────────
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// ── Confirm delete helper ────────────────────────────────
window.confirmDelete = (message = 'Yakin ingin menghapus data ini?') => {
    return window.confirm(message);
};

// ── Auto dismiss flash toasts ────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('flash-container');
    if (container) {
        setTimeout(() => {
            container.style.transition = 'opacity 0.5s ease';
            container.style.opacity = '0';
            setTimeout(() => container.remove(), 500);
        }, 4500);
    }

    // ── Format currency inputs ──────────────────────────
    document.querySelectorAll('input[data-currency]').forEach(input => {
        input.addEventListener('input', function () {
            const raw = this.value.replace(/\D/g, '');
            if (raw) {
                this.dataset.raw = raw;
            }
        });
    });

    // ── Confirm forms ───────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // ── Active sidebar link highlight ──────────────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('aside a[href]').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('bg-blue-600', 'text-white');
        }
    });

    // ── Image lazy load observer ────────────────────────
    if ('IntersectionObserver' in window) {
        const imgObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        imgObserver.unobserve(img);
                    }
                }
            });
        });
        document.querySelectorAll('img[data-src]').forEach(img => imgObserver.observe(img));
    }

    // ── Drag & drop for file inputs ─────────────────────
    document.querySelectorAll('[id$="-drop-area"]').forEach(area => {
        ['dragenter','dragover'].forEach(evt => {
            area.addEventListener(evt, (e) => {
                e.preventDefault();
                area.classList.add('border-blue-400', 'bg-blue-50');
            });
        });
        ['dragleave','drop'].forEach(evt => {
            area.addEventListener(evt, (e) => {
                e.preventDefault();
                area.classList.remove('border-blue-400', 'bg-blue-50');
            });
        });
        area.addEventListener('drop', (e) => {
            const fileInput = area.nextElementSibling || document.querySelector('input[type=file]');
            if (fileInput && e.dataTransfer.files.length) {
                const dt = new DataTransfer();
                for (const f of e.dataTransfer.files) dt.items.add(f);
                fileInput.files = dt.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    });
});

// ── Format Rupiah ─────────────────────────────────────────
window.formatRupiah = (number) => {
    return 'Rp ' + Number(number).toLocaleString('id-ID');
};

// ── Debounce ──────────────────────────────────────────────
window.debounce = (fn, delay = 300) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
};

// ── Live search (optional enhancement) ───────────────────
window.initLiveSearch = (inputId, url, callback) => {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', debounce(async () => {
        const q = input.value.trim();
        if (q.length < 2) return;
        try {
            const res = await fetch(`${url}?q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            callback(data);
        } catch (e) {
            console.error('Search error:', e);
        }
    }, 400));
};

// ── Show toast notification ───────────────────────────────
window.showToast = (message, type = 'success') => {
    const colors = {
        success: { bg: 'border-green-100', icon: 'bg-green-100 text-green-600', label: 'Berhasil' },
        error:   { bg: 'border-red-100',   icon: 'bg-red-100 text-red-600',     label: 'Gagal' },
        info:    { bg: 'border-blue-100',  icon: 'bg-blue-100 text-blue-600',   label: 'Info' },
    };
    const c = colors[type] || colors.success;
    const icon = type === 'success'
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';

    const toast = document.createElement('div');
    toast.className = `toast ${c.bg}`;
    toast.innerHTML = `
        <div class="w-8 h-8 ${c.icon} rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-slate-800">${c.label}!</p>
            <p class="text-sm text-slate-600 mt-0.5">${message}</p>
        </div>
        <button onclick="this.closest('.toast').remove()" class="text-slate-300 hover:text-slate-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;

    let container = document.getElementById('flash-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'flash-container';
        container.className = 'fixed top-4 right-4 z-[100] space-y-2 max-w-sm w-full';
        document.body.appendChild(container);
    }
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
};
