<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('style/images/logo.png') }}" alt="Logo Cafe">
        <p>CAFÉ CINTA RASA</p>
    </div>

    <ul class="menu">
        <li>
            <a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="/admin/manajemen-menu" class="{{ request()->is('admin/manajemen-menu*') ? 'active' : '' }}">
                <i class="bi bi-cup-hot"></i> Manajemen Menu
            </a>
        </li>
        <li>
            <a href="/admin/analytics" class="{{ request()->is('admin/analytics*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Analytics
            </a>
        </li>
        <li>
            <a href="/admin/reviews" class="{{ request()->is('admin/reviews*') ? 'active' : '' }}">
                <i class="bi bi-pencil"></i> Review
            </a>
        </li>
    </ul>

    <div class="logout-area">
        <a href="/login-karyawan"
           onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
        <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</div>
