@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/admin/dashboard.css') }}">
@endpush

@section('title', 'Dashboard')

@section('content')

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <div class="logo">
             <img src="{{ asset('style/images/logo.png') }}" alt="Logo Cafe">
            <p>CAFÉ CINTA RASA</p>
        </div>

        <ul class="menu">

            <li>
                <a href="#" class="active">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="/admin/orders">
                    <i class="bi bi-card-list"></i>
                    Order List
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="bi bi-people"></i>
                    Customers
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="bi bi-graph-up"></i>
                    Analytics
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="bi bi-pencil"></i>
                    Reviews
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="bi bi-cup-hot"></i>
                    Menu
                </a>
            </li>

        </ul>

    </div>

    <!-- CONTENT -->
    <div class="content">

        <div class="header">
            <h1>Dashboard</h1>
            <p>Hi Angeline, Welcome to Cinta Rasa</p>
        </div>

    </div>

</div>

@endsection