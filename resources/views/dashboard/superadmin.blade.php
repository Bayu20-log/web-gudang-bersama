@extends('layouts.app')


@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Dashboard Superadmin</h2>


    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <h5 class="card-title">Total User</h5>
                    <p class="card-text display-6">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
    </div>


    <h4>Jumlah User per Role</h4>
    <div class="row mb-4">
        @foreach($usersPerRole as $role => $jumlah)
        <div class="col-md-4">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted text-capitalize">{{ $role }}</h6>
                    <p class="card-text fw-bold fs-5">{{ $jumlah }} pengguna</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>


    <h4>Status Aktif / Nonaktif per Role</h4>
    <div class="row">
        @foreach($statusPerRole as $role => $status)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-capitalize">{{ $role }}</h6>
                    <p class="mb-1 text-success">Aktif: {{ $status['aktif'] }}</p>
                    <p class="mb-0 text-danger">Nonaktif: {{ $status['nonaktif'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
