@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Buat Prediksi Barang Keluar</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- STEP 1: Pilih Item --}}
    <form method="GET" action="{{ route('prediksi.create') }}" class="mb-4">
        <div class="row align-items-end">
            <div class="col-md-8 mb-3">
                <label for="kode_barang">Pilih Item yang Akan Diprediksi</label>
                <select name="kode_barang" class="form-control" onchange="this.form.submit()" required>
                    <option value="" disabled {{ !$kodeBarang ? 'selected' : '' }}>-- Pilih Item --</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->kode_barang }}" {{ $kodeBarang == $item->kode_barang ? 'selected' : '' }}>
                            {{ $item->kode_barang }} - {{ $item->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <a href="{{ route('prediksi.index') }}" class="btn btn-secondary">← Kembali ke Riwayat</a>
            </div>
        </div>
    </form>

    @if ($kodeBarang && $selectedItem)

        {{-- Sistem mengecek kelayakan data --}}
        @if (!$feasible)
            <div class="alert alert-warning">
                Data histori "barang keluar" untuk <strong>{{ $selectedItem->nama_barang }}</strong> baru
                <strong>{{ $dataPoints }} hari</strong>. Minimal <strong>14 hari</strong> data diperlukan
                agar prediksi bisa dijalankan. Silakan pilih item lain atau lengkapi data terlebih dahulu.
            </div>
        @else

            @if ($isThin)
                <div class="alert alert-warning">
                    ⚠️ Data histori baru <strong>{{ $dataPoints }} hari</strong> (di bawah rekomendasi 30 hari).
                    Prediksi tetap bisa dijalankan, tapi akurasinya kurang reliabel — terutama untuk model
                    <strong>HWES</strong> yang idealnya butuh minimal 2 siklus mingguan (14 hari) untuk
                    mengenali pola musiman dengan baik. Semakin banyak data historis, semakin akurat hasilnya.
                </div>
            @endif

            {{-- STEP 2: Sistem menguji model & menampilkan rekomendasi --}}
            <div class="card mb-4">
                <div class="card-header">
                    Perbandingan Model untuk <strong>{{ $selectedItem->nama_barang }}</strong>
                    <span class="text-muted">({{ $dataPoints }} hari data historis, split 80/20)</span>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Pilih</th>
                                <th>Model</th>
                                <th>RMSE</th>
                                <th>MAPE</th>
                                <th>MAE</th>
                                <th>Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($evaluations as $ev)
                                <tr class="{{ $ev['is_recommended'] ? 'table-success' : '' }}">
                                    <td>
                                        <input type="radio" name="model_pick" value="{{ $loop->index }}"
                                               form="form-simpan-prediksi"
                                               {{ $ev['is_recommended'] ? 'checked' : '' }} required>
                                    </td>
                                    <td>{{ $ev['model'] }}</td>
                                    <td>{{ number_format($ev['rmse'], 3) }}</td>
                                    <td>{{ number_format($ev['mape'], 2) }}%</td>
                                    <td>{{ number_format($ev['mae'], 3) }}</td>
                                    <td>
                                        @if ($ev['is_recommended'])
                                            <span class="badge bg-success">✔ Direkomendasikan (RMSE terkecil)</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <small class="text-muted">
                        RMSE dipakai sebagai dasar rekomendasi otomatis. MAPE dan MAE ditampilkan sebagai
                        pembanding tambahan. Kamu tetap bisa memilih model lain di luar rekomendasi untuk diuji.
                    </small>
                </div>
            </div>

            {{-- STEP 3: Simpan prediksi final (otomatis masuk history) --}}
            <form id="form-simpan-prediksi" method="POST" action="{{ route('prediksi.store') }}"
                  onsubmit="return confirmSimpanPrediksi();">
                @csrf
                <input type="hidden" name="kode_barang" value="{{ $kodeBarang }}">

                @foreach ($evaluations as $i => $ev)
                    <input type="hidden" class="model-name" data-index="{{ $i }}" value="{{ $ev['model'] }}">
                    <input type="hidden" class="model-params" data-index="{{ $i }}" value='@json($ev['params'])'>
                @endforeach
                <input type="hidden" name="model" id="input-model">
                <input type="hidden" name="params" id="input-params">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Horizon Prediksi (hari ke depan)</label>
                        <input type="number" name="horizon" class="form-control @error('horizon') is-invalid @enderror"
                               value="{{ old('horizon', 7) }}" min="1" max="90" required>
                        @error('horizon') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Prediksi</button>
                <a href="{{ route('prediksi.create') }}" class="btn btn-secondary">Batal</a>
            </form>

        @endif
    @endif
</div>

<script>
    function confirmSimpanPrediksi() {
        // ambil model & params sesuai radio yang dipilih, lalu isi hidden input sebelum submit
        const picked = document.querySelector('input[name="model_pick"]:checked');
        if (!picked) {
            alert('Silakan pilih salah satu model terlebih dahulu.');
            return false;
        }
        const index = picked.value;
        document.getElementById('input-model').value =
            document.querySelector(`.model-name[data-index="${index}"]`).value;
        document.getElementById('input-params').value =
            document.querySelector(`.model-params[data-index="${index}"]`).value;

        return confirm("Simpan prediksi dengan model terpilih? Hasil akan otomatis masuk ke history.");
    }
</script>
@endsection