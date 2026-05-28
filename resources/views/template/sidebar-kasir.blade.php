<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('style/images/logo.png') }}" alt="Logo Cafe">
        <p>CAFÉ CINTA RASA</p>
    </div>

    <ul class="menu">
        <li>
            <a href="/kasir/dashboard" class="{{ request()->is('kasir/dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
        </li>
    </ul>

    <div style="position:absolute; bottom:24px; width:100%; left:0; padding:0 16px; box-sizing:border-box;">
        <a href="{{ url('/admin/login') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           style="display:flex; align-items:center; gap:8px; color:#9b7a62; font-size:13px; text-decoration:none; padding:10px 12px; border-radius:10px;"
           onmouseover="this.style.background='#f3dfcf'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
        <form id="logout-form" action="{{ url('/kasir/logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</div>