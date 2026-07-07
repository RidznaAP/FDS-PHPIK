/**
 * app-global.js — SIP-HPIK Global JavaScript
 * ==============================================
 * File ini berisi semua JS global yang digunakan di seluruh halaman sistem.
 * Diload melalui layouts/app.blade.php.
 *
 * Konten:
 *  1. Global Modal Konfirmasi Tindakan (confirmAction)
 *  2. Auto-dismiss Flash Messages
 *  3. Notification Polling (Visibility API-aware)
 *  4. Bootstrap Tooltip Initialization
 */

// ══════════════════════════════════════════════════════════════
// 1. GLOBAL MODAL KONFIRMASI
// ══════════════════════════════════════════════════════════════

/**
 * Tampilkan modal konfirmasi sebelum melakukan aksi destructive.
 *
 * @param {string} url       - Action URL untuk form submit
 * @param {string} message   - Pesan konfirmasi yang ditampilkan
 * @param {string} method    - HTTP method (DELETE, POST, dll)
 * @param {string} btnClass  - Bootstrap btn class untuk tombol konfirmasi
 * @param {string} emoji     - Emoji yang ditampilkan di atas modal
 * @param {string} title     - Judul modal
 */
function confirmAction(url, message, method, btnClass, emoji, title) {
    method   = (method || 'DELETE').toUpperCase();
    btnClass = btnClass || (method === 'DELETE' ? 'btn-danger' : 'btn-primary');

    if (!emoji) emoji = (method === 'DELETE') ? '🗑️' : '🚀';
    if (!title) title = (method === 'DELETE') ? 'Hapus Data?' : 'Konfirmasi Tindakan';

    document.getElementById('confirmMessage').textContent = message || 'Apakah Anda yakin?';
    document.getElementById('confirmTitle').textContent   = title;
    document.getElementById('confirmEmoji').textContent   = emoji;

    const methodInput = document.getElementById('confirmMethod');
    if (method === 'POST') {
        methodInput.disabled = true;
    } else {
        methodInput.disabled = false;
        methodInput.value = method;
    }

    document.getElementById('confirmForm').action = url || '#';

    const btn = document.getElementById('confirmBtn');
    btn.className = 'btn flex-fill ' + btnClass;
    btn.textContent = (method === 'DELETE') ? 'Ya, Hapus' : 'Ya, Lanjutkan';
    btn.onclick = function() { submitConfirmForm(); };

    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

function submitConfirmForm() {
    document.getElementById('confirmForm').submit();
}


// ══════════════════════════════════════════════════════════════
// 2. AUTO-DISMISS FLASH MESSAGES (5 detik)
// ══════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        ['flash-msg', 'flash-msg-err', 'flash-msg-warn', 'flash-msg-info'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                const instance = bootstrap.Alert.getOrCreateInstance(el);
                if (instance) instance.close();
            }
        });
    }, 5000);
});


// ══════════════════════════════════════════════════════════════
// 3. BOOTSTRAP TOOLTIP INITIALIZATION (Global)
// ══════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function() {
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(el => new bootstrap.Tooltip(el, { trigger: 'hover' }));
});


// ══════════════════════════════════════════════════════════════
// 4. NOTIFICATION POLLING (Simulated Real-time)
// ══════════════════════════════════════════════════════════════
function initNotifPolling(notifUrl) {
    let pollingTimer = null;
    const POLL_INTERVAL = 10000; // 10 detik (lebih cepat agar terasa real-time)
    let lastCount = -1;

    function showToastNotification(message) {
        const toastHTML = `
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
                <div class="toast align-items-center text-bg-primary border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center gap-2 fw-semibold">
                            <i class="ti ti-bell-ringing fs-3"></i> ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', toastHTML);
        const toastEl = document.body.lastElementChild.querySelector('.toast');
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
        
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.parentElement.remove();
        });
    }

    function updateNotifBadge() {
        if (document.visibilityState !== 'visible') return;

        fetch(notifUrl, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(data => {
            const count     = data.count || 0;
            const badge     = document.getElementById('sidebar-notif-badge');
            const badgeText = document.getElementById('sidebar-notif-count');

            if (badge) {
                badge.textContent = count > 9 ? '9+' : count;
                badge.classList.toggle('d-none', count === 0);
            }
            if (badgeText) {
                badgeText.textContent = count > 9 ? '9+' : count;
                badgeText.classList.toggle('d-none', count === 0);
            }

            // Real-time toast alert
            if (lastCount !== -1 && count > lastCount) {
                const diff = count - lastCount;
                showToastNotification(`Terdapat ${diff} notifikasi baru!`);
            }
            lastCount = count;
        })
        .catch(() => {});
    }

    function startPolling() {
        if (pollingTimer) clearInterval(pollingTimer);
        pollingTimer = setInterval(updateNotifBadge, POLL_INTERVAL);
    }

    function stopPolling() {
        if (pollingTimer) {
            clearInterval(pollingTimer);
            pollingTimer = null;
        }
    }

    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            updateNotifBadge();
            startPolling();
        } else {
            stopPolling();
        }
    });

    updateNotifBadge();
    startPolling();
}
