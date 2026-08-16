@extends('layouts.app')
@section('content')
<div class="container py-4">

    <h2 class="mb-4">Monitoring &amp; Notifikasi Stok</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tombol trigger evaluasi manual --}}
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <p class="mb-0 text-muted">
                Evaluasi ulang status stok seluruh barang berdasarkan threshold terkini.
            </p>
            <form method="POST" action="{{ route('stock-notifications.evaluate') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    Evaluasi Sekarang
                </button>
            </form>
        </div>
    </div>

    {{-- Legenda warna status --}}
    <div class="mb-3">
        <span class="badge bg-success me-2">Aman</span>
        <span class="badge bg-warning text-dark me-2">Rendah</span>
        <span class="badge" style="background-color:#fd7e14;" >Kritis</span>
        <span class="badge bg-danger ms-2">Habis</span>
    </div>

    {{-- Tabel status stok per item --}}
    <div class="card mb-4">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Barang</th>
                        <th>Stok</th>
                        <th>Threshold Rendah</th>
                        <th>Threshold Kritis</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $badgeClass = [
                            'aman'   => 'bg-success',
                            'rendah' => 'bg-warning text-dark',
                            'kritis' => 'text-white',
                            'habis'  => 'bg-danger',
                        ];
                        $badgeStyle = [
                            'kritis' => 'background-color:#fd7e14;',
                        ];
                    @endphp
                    @forelse ($itemStatuses as $item)
                        <tr>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->stok }}</td>
                            <td>{{ $item->low_threshold ?? '-' }}</td>
                            <td>{{ $item->critical_threshold ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $badgeClass[$item->status] ?? 'bg-secondary' }}"
                                    style="{{ $badgeStyle[$item->status] ?? '' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Panel riwayat notifikasi --}}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Riwayat Notifikasi Terbaru</h5>
            @php
                $levelBorder = [
                    'info'     => 'border-info',
                    'warning'  => 'border-warning',
                    'critical' => 'border-danger',
                ];
            @endphp
            @forelse ($notifications as $notif)
                <div class="border-start border-4 {{ $levelBorder[$notif->level] ?? 'border-secondary' }} ps-3 py-2 mb-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-semibold">{{ $notif->title }}</div>
                            <div class="text-muted small">{{ $notif->message }}</div>
                        </div>
                        <span class="text-muted small text-nowrap ms-2">
                            {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada notifikasi.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection