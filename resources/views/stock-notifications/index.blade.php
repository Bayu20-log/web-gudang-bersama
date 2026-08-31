@extends('layouts.app')
@section('content')
<div class="container py-4">

    <h2 class="mb-4">Monitoring &amp; Notifikasi Stok</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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

    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <span class="badge bg-success me-2">Aman</span>
            <span class="badge bg-warning text-dark me-2">Rendah</span>
            <span class="badge" style="background-color:#fd7e14;" >Kritis</span>
            <span class="badge bg-danger ms-2">Habis</span>
        </div>
        <small class="text-muted">
            💡 Klik <strong>"Lihat Perhitungan"</strong> pada barang untuk melihat rincian rumus ADC dan Threshold
        </small>
    </div>

    <div class="card mb-4">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Barang</th>
                        <th>Stok</th>
                        <th>ADC</th>
                        <th>Threshold Rendah</th>
                        <th>Threshold Kritis</th>
                        <th>Status</th>
                        <th>Detail</th>
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
                            <td>{{ $item->adc ?? '-' }}</td>
                            <td>{{ $item->low_threshold ?? '-' }}</td>
                            <td>{{ $item->critical_threshold ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $badgeClass[$item->status] ?? 'bg-secondary' }}"
                                    style="{{ $badgeStyle[$item->status] ?? '' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#detail-{{ $item->kode_barang }}"
                                    aria-expanded="false">
                                    Lihat Perhitungan
                                </button>
                            </td>
                        </tr>
                        <tr class="collapse" id="detail-{{ $item->kode_barang }}">
                            <td colspan="7" class="bg-light">
                                @php $b = $item->breakdown; @endphp
                                <div class="p-3 small">
                                    <div class="mb-3">
                                        <strong>1. Menghitung ADC (rata-rata barang keluar per hari)</strong>
                                        <div class="mt-1">
                                            Total barang keluar 30 hari terakhir:
                                            <strong>{{ $b['total_outflow'] }} unit</strong>
                                        </div>
                                        <div>
                                            Dibagi jumlah hari valid (sejak transaksi pertama, maks. 30 hari):
                                            <strong>{{ $b['valid_days'] }} hari</strong>
                                        </div>
                                        <div class="mt-1 p-2 bg-white border rounded d-inline-block">
                                            ADC = {{ $b['total_outflow'] }} ÷ {{ $b['valid_days'] }}
                                            = <strong>{{ $b['adc'] }} unit/hari</strong>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <strong>2. Menghitung Threshold Rendah</strong>
                                        <div class="mt-1">
                                            Safety Stock = ADC &times; Hari Buffer
                                            = {{ $b['adc'] }} &times; {{ $b['safety_stock_days'] }}
                                            = <strong>{{ $b['safety_stock'] }}</strong>
                                        </div>
                                        <div class="mt-1 p-2 bg-white border rounded d-inline-block">
                                            Threshold Rendah = (ADC &times; Lead Time) + Safety Stock
                                            = ({{ $b['adc'] }} &times; {{ $b['lead_time_days'] }}) + {{ $b['safety_stock'] }}
                                            = <strong>{{ $b['low_threshold'] }}</strong>
                                        </div>
                                    </div>

                                    <div>
                                        <strong>3. Menghitung Threshold Kritis</strong>
                                        <div class="mt-1 p-2 bg-white border rounded d-inline-block">
                                            Threshold Kritis = ADC &times; Waktu Respons
                                            = {{ $b['adc'] }} &times; {{ $b['response_time_days'] }}
                                            = <strong>{{ $b['critical_threshold'] }}</strong>
                                        </div>
                                    </div>

                                    @if ((float) $b['adc'] === 0.0)
                                        <div class="mt-3 alert alert-warning py-2 px-3 mb-0 small">
                                            ADC = 0 karena barang ini belum pernah ada transaksi keluar.
                                            Sistem otomatis menganggap status minimal <strong>Rendah</strong>
                                            (bukan Aman) untuk kondisi ini.
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Belum ada data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
                        @php
                            $displayTitle = str_replace('item #' . $notif->item_id, $notif->nama_barang, $notif->title);
                            $displayMessage = str_replace('item #' . $notif->item_id, $notif->nama_barang, $notif->message);
                        @endphp
                        <div>
                            <div class="fw-semibold">{{ $displayTitle }}</div>
                            <div class="text-muted small">{{ $displayMessage }}</div>
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