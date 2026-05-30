@extends('template.main-kasir')

@push('style')
<style>
/* ── Layout ── */
.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    padding: 24px;
    min-height: 100%;
}

.left-panel {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ── Panel Box ── */
.panel-box {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid var(--border);
}

.panel-box-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.panel-box-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    margin: 0;
}

/* ── Meja Grid ── */
.meja-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 14px;
}

.meja-status-box {
    border-radius: 14px;
    border: 2px solid var(--border);
    padding: 14px;
    background: #fff;
    transition: border-color .2s, background .2s;
}
.meja-box-aktif   { border-color: #4CAF50; background: #F0FBF0; }
.meja-box-pending { border-color: #F59E0B; background: #FFFBF0; }
.meja-box-kosong  { border-color: var(--border); background: #fff; }

.meja-status-no {
    font-size: 22px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 4px;
}

.meja-status-label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 4px;
}

.status-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #ccc;
    display: inline-block;
}
.meja-box-aktif   .status-dot { background: #4CAF50; }
.meja-box-pending .status-dot { background: #F59E0B; }
.meja-box-kosong  .status-dot { background: #9E9E9E; }

.meja-cust-name {
    font-size: 11px;
    color: var(--text3);
    margin-bottom: 10px;
    min-height: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-barcode {
    width: 100%;
    border-radius: 8px;
    padding: 5px 0;
    font-size: 11px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    transition: background .15s;
    border: 1px solid var(--border);
    margin-top: 6px;
    background: #FAF7F2;
    color: var(--text2);
}
.btn-barcode:hover { background: #EDE8E0; }

/* ── Modal (tanpa Bootstrap) ── */
.gg-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.gg-overlay.show { display: flex; }

.gg-modal {
    background: #fff;
    border-radius: 16px;
    padding: 28px 28px 24px;
    width: 340px;
    max-width: 92vw;
    box-shadow: 0 8px 40px rgba(0,0,0,.18);
    animation: modalIn .18s ease;
}
@keyframes modalIn {
    from { transform: scale(.94); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}

.gg-modal-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 18px;
}

.gg-modal-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    margin-top: 20px;
}

.gg-input {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 9px 14px;
    font-family: inherit;
    font-size: 13px;
    outline: none;
    background: #fff;
    color: var(--text);
}
.gg-input:focus { border-color: var(--brown); }

.gg-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text2);
    display: block;
    margin-bottom: 6px;
}

/* QR modal */
.qr-modal-body { text-align: center; }
.qr-modal-body h3 { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
.qr-modal-sub  { font-size: 12px; color: var(--text3); margin-bottom: 16px; }
.qr-output-wrap { display: flex; justify-content: center; margin-bottom: 12px; }
</style>
@endpush

@section('title', 'Manajemen Meja')

@section('content')
<div class="kasir-content">
<div class="dashboard-grid">

    <div class="left-panel">
        <div class="panel-box">
            <div class="panel-box-header">
                <h2 class="panel-box-title">Status Meja</h2>
            </div>

            <div class="meja-status-grid" id="mejaStatusGrid">
                @forelse($mejas as $meja)
                @php
                    $activeOrder = \App\Models\Order::where('table_number', $meja->no_meja)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->latest()->first();

                    if ($activeOrder?->status === 'confirmed') {
                        $mejaStatus = 'aktif';
                    } elseif ($activeOrder?->status === 'pending') {
                        $mejaStatus = 'pending';
                    } else {
                        $mejaStatus = 'kosong';
                    }

                    $stLabel = match($mejaStatus) {
                        'pending' => 'Memesan',
                        'aktif'   => 'Diproses',
                        default   => 'Kosong',
                    };
                @endphp
                <div class="meja-status-box meja-box-{{ $mejaStatus }}"
                     data-meja="{{ $meja->no_meja }}"
                     data-uuid="{{ $meja->qr_uuid }}">
                    <div class="meja-status-no">{{ $meja->no_meja }}</div>
                    <div class="meja-status-label">
                        <span class="status-dot"></span>
                        <span class="status-text">{{ $stLabel }}</span>
                    </div>
                    <div class="meja-cust-name" @if($mejaStatus === 'kosong') style="display:none" @endif>
                        {{ $mejaStatus !== 'kosong' ? ($activeOrder?->customer_name ?? '—') : '' }}
                    </div>
                    <button class="btn-barcode"
                        onclick="showBarcode('{{ $meja->no_meja }}', '{{ $meja->qr_uuid }}')">
                        <i class="bi bi-qr-code"></i> Tampilkan barcode
                    </button>
                </div>
                @empty
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text3);">
                    <i class="bi bi-layout-three-columns" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    Belum ada meja.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
</div>

{{-- ===== MODAL: QR Barcode ===== --}}
<div class="gg-overlay" id="modalBarcode" onclick="overlayClose(event, 'modalBarcode')">
    <div class="gg-modal">
        <div class="qr-modal-body">
            <h3 id="qrModalTitle">QR Code Meja</h3>
            <p class="qr-modal-sub">Scan untuk mulai memesan</p>
            <div class="qr-output-wrap">
                <div id="qrcodeOutput"></div>
            </div>
        </div>
        <div class="gg-modal-actions" style="justify-content:center;">
            <button class="btn-secondary" onclick="closeModal('modalBarcode')">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('custom_script')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
// ── Modal helpers ─────────────────────────────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }
function overlayClose(e, id) {
    if (e.target === document.getElementById(id)) closeModal(id);
}

// ── QR Barcode ────────────────────────────────────────────────────────────────
function showBarcode(noMeja, uuid) {
    document.getElementById('qrModalTitle').textContent = 'QR Code Meja ' + noMeja;
    var output = document.getElementById('qrcodeOutput');
    output.innerHTML = '';

    if (!uuid) {
        output.innerHTML = '<p style="color:#e53935;">QR belum digenerate untuk meja ini.</p>';
    } else {
        new QRCode(output, {
            text: window.location.origin + '/table/' + uuid,
            width: 180, height: 180,
            colorDark: '#2D5016', colorLight: '#ffffff'
        });
    }
    openModal('modalBarcode');
}

// ── Polling Status Meja tiap 5 detik ─────────────────────────────────────────
function pollMejaStatus() {
    fetch('/kasir/meja/status')
        .then(r => r.json())
        .then(data => {
            data.forEach(meja => {
                var box = document.querySelector('[data-meja="' + meja.no_meja + '"]');
                if (!box) return;

                box.classList.remove('meja-box-kosong', 'meja-box-pending', 'meja-box-aktif');
                box.classList.add('meja-box-' + meja.status);

                var st = box.querySelector('.status-text');
                if (st) st.textContent =
                    meja.status === 'pending' ? 'Memesan' :
                    meja.status === 'aktif'   ? 'Diproses' : 'Kosong';

                var cn = box.querySelector('.meja-cust-name');
                if (cn) {
                    cn.textContent   = meja.status !== 'kosong' ? (meja.customer_name || '—') : '';
                    cn.style.display = meja.status !== 'kosong' ? '' : 'none';
                }
            });
        })
        .catch(err => console.warn('Polling gagal:', err));
}

setInterval(pollMejaStatus, 5000);
</script>
@endpush