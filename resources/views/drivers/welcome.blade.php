@extends('layouts.app')

@section('styles_Driver')
<style>
    .dashboard-card {
        border: 1px solid #dee2e6;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: 0.3s;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .dashboard-icon {
        font-size: 2rem;
        color: #198754;
    }

    .quick-links .btn {
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="container">
    <h2 class="mb-4">Welcome back, {{ Auth::user()->name }}!</h2>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="dashboard-card text-center">
                <i class="bi bi-truck dashboard-icon"></i>
                <h5 class="mt-3">My Deliveries</h5>
                <p class="fs-4 text-success">{{ $myDeliveries ?? 0 }}</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card text-center">
                <i class="bi bi-clock-history dashboard-icon"></i>
                <h5 class="mt-3">Pending Pickups</h5>
                <p class="fs-4 text-success">{{ $pendingPickups ?? 0 }}</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card text-center">
                <i class="bi bi-check-circle dashboard-icon"></i>
                <h5 class="mt-3">Completed</h5>
                <p class="fs-4 text-success">{{ $completedDeliveries ?? 0 }}</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card text-center">
                <i class="bi bi-exclamation-triangle dashboard-icon"></i>
                <h5 class="mt-3">Delivery Issues</h5>
                <p class="fs-4 text-success">{{ $deliveryIssues ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
