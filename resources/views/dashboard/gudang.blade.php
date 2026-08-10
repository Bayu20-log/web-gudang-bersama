@extends('layouts.app')

@section('content')
<style>
    .dbg-wrap { max-width: 1200px; margin: 0 auto; padding: 24px 4px; }
    .dbg-title { font-size: 22px; font-weight: 700; margin: 0 0 2px; color: #1f2430; }
    .dbg-subtitle { font-size: 13px; color: #8a8f9c; margin-bottom: 22px; }

    .dbg-card {
        background: #fff;
        border: 1px solid #e7e9ee;
        border-radius: 10px;
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .dbg-row { display: grid; gap: 16px; margin-bottom: 16px; }
    .dbg-row-2 { grid-template-columns: 1fr 1fr; }
    @media (max-width: 767px) { .dbg-row-2 { grid-template-columns: 1fr; } }

    .dbg-kpi-label { font-size: 12px; color: #8a8f9c; letter-spacing: .5px; font-weight: 600; }
    .dbg-kpi-value { font-size: 28px; font-weight: 800; margin: 6px 0 2px; }
    .dbg-kpi-value.green { color: #2e9e5b; }
    .dbg-kpi-value.red { color: #d64545; }
    .dbg-kpi-period { font-size: 12px; color: #a3a8b3; }

    .dbg-card-title { font-size: 14px; font-weight: 700; margin: 0 0 2px; color: #1f2430; }
    .dbg-card-sub { font-size: 12px; color: #8a8f9c; margin: 0 0 14px; }
    .dbg-link { color: #3d6bff; font-size: 12px; text-decoration: none; float: right; }

    .dbg-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .dbg-table th { text-align: left; color: #8a8f9c; font-weight: 600; font-size: 11px; padding: 6px 4px; border-bottom: 1px solid #eee; }
    .dbg-table td { padding: 9px 4px; border-bottom: 1px solid #f2f2f2; vertical-align: middle; }

    .dbg-badge { padding: 2px 9px; border-radius: 10px; font-size: 11px; font-weight: 600; display: inline-block; }
    .dbg-badge.kritis { background: #fdeaea; color: #d64545; }
    .dbg-badge.peringatan { background: #fff4e0; color: #c07d10; }
    .dbg-badge.idle { background: #eef0f4; color: #666; }
    .dbg-badge.kadaluarsa { background: #fdeaea; color: #d64545; }

    .dbg-exp-item { border-left: 3px solid #d64545; padding: 8px 10px; margin-bottom: 8px; border-radius: 4px; background: #fafafa; }
    .dbg-exp-item .nm { font-weight: 600; font-size: 13px; }
    .dbg-exp-item .meta { font-size: 11px; color: #a3a8b3; }
    .dbg-exp-item .days { float: right; color: #d64545; font-weight: 600; font-size: 12px; }

    .dbg-prio-row { display: flex; align-items: center; padding: 10px 4px; border-bottom: 1px solid #f2f2f2; font-size: 13px; }
    .dbg-prio-num { width: 22px; color: #a3a8b3; }
    .dbg-prio-name { flex: 1; }
    .dbg-prio-note { color: #8a8f9c; font-size: 12px; margin-left: 12px; }

    .dbg-btn { background: #e8622c; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; }
    canvas { max-height: 260px; }
</style>

<div class="dbg-wrap">
    <div class="dbg-title">Dashboard Gudang</div>
    <div class="dbg-subtitle">Update terakhir: Hari ini, {{ now()->translatedFormat('H:i') }} WITA</div>

    {{-- Baris 1: KPI --}}
    <div class="dbg-row dbg-row-2">
        <div class="dbg-card">
            <div class="dbg-kpi-label">TOTAL BARANG MASUK</div>
            <div class="dbg-kpi-value green">{{ $kpi['total_masuk'] }}</div>
            <div class="dbg-kpi-period">Periode: {{ $kpi['periode'] }}</div>
        </div>
        <div class="dbg-card">
            <div class="dbg-kpi-label">TOTAL BARANG KELUAR</div>
            <div class="dbg-kpi-value red">{{ $kpi['total_keluar'] }}</div>
            <div class="dbg-kpi-period">Periode: {{ $kpi['periode'] }}</div>
        </div>
    </div>

    {{-- Baris 2: Grafik Tren Transaksi --}}
    <div class="dbg-row">
        <div class="dbg-card">
            <div class="dbg-card-title">Grafik Tren Transaksi</div>
            <div class="dbg-card-sub">Barang masuk vs keluar dari waktu ke waktu (7 hari terakhir)</div>
            <canvas id="trenChart"></canvas>
        </div>
    </div>

    {{-- Baris 3: Top 5 Barang Keluar --}}
    <div class="dbg-row">
        <div class="dbg-card">
            <div class="dbg-card-title">Top 5 Barang Keluar</div>
            <div class="dbg-card-sub">Barang paling sering keluar gudang &middot; Metrik: Frekuensi Transaksi</div>
            <canvas id="topKeluarChart"></canvas>
        </div>
    </div>

    {{-- Baris 4: Stok Terendah + Segera Kadaluarsa --}}
    <div class="dbg-row dbg-row-2">
        <div class="dbg-card">
            <a href="#" class="dbg-link">Lihat Semua</a>
            <div class="dbg-card-title">5 Barang dengan Stok Terendah</div>
            <table class="dbg-table">
                <thead>
                    <tr><th>Nama Barang</th><th>Stok</th><th>Safety Stock</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($stokTerendah as $item)
                    @php $status = $item->stok <= $item->stok_minimum ? 'Kritis' : 'Peringatan'; @endphp
                    <tr>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->stok }}</td>
                        <td>{{ $item->stok_minimum }}</td>
                        <td><span class="dbg-badge {{ strtolower($status) }}">{{ $status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4">Belum ada data barang</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="dbg-card">
            <a href="#" class="dbg-link">Review Semua</a>
            <div class="dbg-card-title">Segera Kadaluarsa</div>
            @forelse($kadaluarsa as $item)
            @php $sisaHari = max(now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($item->tanggal_kadaluarsa), false), 0); @endphp
            <div class="dbg-exp-item">
                <span class="days">{{ $sisaHari }} Hari Lagi</span>
                <div class="nm">{{ $item->nama_barang }}</div>
                <div class="meta">Qty {{ $item->jumlah }} &middot; Kadaluarsa {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->translatedFormat('d M Y') }}</div>
            </div>
            @empty
            <p style="color:#8a8f9c; font-size:13px;">Tidak ada barang yang akan kadaluarsa dalam 30 hari.</p>
            @endforelse
        </div>
    </div>

    {{-- Baris 5: Prioritas Tindakan --}}
    <div class="dbg-row">
        <div class="dbg-card">
            <div class="dbg-card-title">Prioritas Tindakan</div>
            <div class="dbg-card-sub">Gabungan status Kritis + Segera Kadaluarsa + Idle, diurutkan berdasarkan urgensi</div>
            @forelse($prioritasTindakan as $i => $item)
            <div class="dbg-prio-row">
                <span class="dbg-prio-num">{{ $i + 1 }}</span>
                <span class="dbg-prio-name">{{ $item['nama'] }}</span>
                <span class="dbg-badge {{ $item['tag'] === 'Kritis' ? 'kritis' : ($item['tag'] === 'Idle' ? 'idle' : 'kadaluarsa') }}">{{ $item['tag'] }}</span>
                <span class="dbg-prio-note">{{ $item['keterangan'] }}</span>
            </div>
            @empty
            <p style="color:#8a8f9c; font-size:13px;">Tidak ada tindakan prioritas saat ini.</p>
            @endforelse
        </div>
    </div>

    {{-- Baris 6: Idle Stock --}}
    <div class="dbg-row">
        <div class="dbg-card">
            <div class="dbg-card-title">Idle Stock (&gt;30 Hari)</div>
            <div class="dbg-card-sub">
                Daftar inventaris yang belum bergerak lebih dari sebulan
                <button class="dbg-btn" style="float:right;" disabled title="Fitur export menyusul">&#8595; Export</button>
            </div>
            <table class="dbg-table">
                <thead>
                    <tr><th>Nama Barang</th><th>Lokasi</th><th>Hari Idle</th><th>Terakhir Bergerak</th></tr>
                </thead>
                <tbody>
                    @forelse($idleStock as $item)
                    @php $hariIdle = \Carbon\Carbon::parse($item->tanggal_masuk)->diffInDays(now()); @endphp
                    <tr>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->nama_lokasi }}</td>
                        <td>{{ $hariIdle }} Hari</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->translatedFormat('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4">Tidak ada barang idle</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- Kalau layouts.app SUDAH memuat Chart.js, hapus baris <script src> di bawah ini --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const trenData = @json($trenTransaksi);
    new Chart(document.getElementById('trenChart'), {
        type: 'line',
        data: {
            labels: trenData.map(d => d.label),
            datasets: [
                { label: 'Barang Masuk', data: trenData.map(d => d.masuk), borderColor: '#2e9e5b', backgroundColor: 'transparent', tension: .3 },
                { label: 'Barang Keluar', data: trenData.map(d => d.keluar), borderColor: '#d64545', backgroundColor: 'transparent', tension: .3 },
            ]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    const topKeluarData = @json($topKeluar);
    new Chart(document.getElementById('topKeluarChart'), {
        type: 'bar',
        data: {
            labels: topKeluarData.map(b => b.nama_barang),
            datasets: [{ label: 'Frekuensi Transaksi', data: topKeluarData.map(b => b.frekuensi), backgroundColor: '#e8622c' }]
        },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false }
    });
</script>
@endsection