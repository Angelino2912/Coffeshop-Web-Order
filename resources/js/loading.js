import axios from 'axios';

// Buat elemen overlay
const overlay = document.createElement('div');
overlay.id = 'ajax-loading-overlay';
overlay.innerHTML = `
  <div class="ajax-spinner"></div>
  <span class="ajax-loading-text">Memproses...</span>
`;
document.body.appendChild(overlay);

// Style
const style = document.createElement('style');
style.textContent = `
  #ajax-loading-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 9999;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  #ajax-loading-overlay.active { display: flex; }
  .ajax-spinner {
    width: 52px; height: 52px;
    border: 4px solid rgba(255,255,255,0.25);
    border-top: 4px solid #ffffff;
    border-radius: 50%;
    animation: ajaxSpin 0.8s linear infinite;
    margin-bottom: 16px;
  }
  @keyframes ajaxSpin { to { transform: rotate(360deg); } }
  .ajax-loading-text {
    color: #fff;
    font-size: 15px;
    font-weight: 500;
    letter-spacing: 0.02em;
  }
`;
document.head.appendChild(style);

// Hitung jumlah request aktif (agar overlay hanya hilang kalau semua selesai)
let activeRequests = 0;

const showLoading = (text = 'Memproses...') => {
  activeRequests++;
  overlay.querySelector('.ajax-loading-text').textContent = text;
  overlay.classList.add('active');
};

const hideLoading = () => {
  activeRequests = Math.max(0, activeRequests - 1);
  if (activeRequests === 0) overlay.classList.remove('active');
};

// Axios interceptor — otomatis trigger di setiap request
axios.interceptors.request.use(config => {
  showLoading(config.loadingText ?? 'Memproses...');
  return config;
});

axios.interceptors.response.use(
  response => { hideLoading(); return response; },
  error    => { hideLoading(); return Promise.reject(error); }
);

export { showLoading, hideLoading };