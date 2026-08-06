@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📢 Daftar Notifikasi</h5>
            <form method="POST" action="{{ route('notifications.markRead') }}">
                @csrf
                <button type="submit" class="btn btn-light btn-sm">Tandai Semua Dibaca</button>
            </form>
        </div>

        <div class="card-body">
            @if($notifications->isEmpty())
                <div class="alert alert-info text-center mb-0">
                    Tidak ada notifikasi saat ini.
                </div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($notifications as $notif)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-bell text-warning me-2"></i>
                                {{ $notif->message }}
                            </div>

                            @if(!$notif->is_read)
                                <form method="POST" action="{{ route('notifications.markSingleRead', $notif->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Tandai Dibaca</button>
                                </form>
                            @else
                                <span class="badge bg-success">Sudah Dibaca</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
