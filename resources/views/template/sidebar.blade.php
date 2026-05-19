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
            <a href="/admin/orders" class="{{ request()->is('admin/orders*') ? 'active' : '' }}">
                <i class="bi bi-card-list"></i> Order List
            </a>
        </li>
        
        <li>
            <a href="/admin/analytics" class="{{ request()->is('admin/analytics*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Analytics
            </a>
        </li>
        <li>
            <a href="/admin/reviews" class="{{ request()->is('admin/reviews*') ? 'active' : '' }}">
                <i class="bi bi-pencil"></i> Reviews
            </a>
        </li>
        <li>
            <a href="/admin/menu" class="{{ request()->is('admin/menu*') ? 'active' : '' }}">
                <i class="bi bi-cup-hot"></i> Menu
            </a>
        </li>
    </ul>
</div>