@extends('layouts.app')


@section('content')
<div class="container">
    <h4>Tambah Barang Masuk</h4>


    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <form method="POST" action="{{ route('barang-masuk.store') }}" onsubmit="return confirmSimpan();">
        @csrf


        <div class="mb-3">
            <label for="kode_barang">Pilih Item</label>
            <select name="kode_barang" class="form-control @error('kode_barang') is-invalid @enderror" required>
                <option value="" disabled selected>-- Pilih Item --</option>
                @foreach ($items as $item)
                    <option value="{{ $item->kode_barang }}" {{ old('kode_barang') == $item->kode_barang ? 'selected' : '' }}>
                        {{ $item->kode_barang }} - {{ $item->nama_barang }}
                    </option>
                @endforeach
            </select>
            @error('kode_barang') <div class="text-danger">{{ $message }}</div> @enderror
        </div>


        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Jumlah</label>
                <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" min="1" required>
                @error('jumlah') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label>Harga Beli</label>
                <input type="number" name="harga_satuan" class="form-control @error('harga_satuan') is-invalid @enderror" min="0" required>
                @error('harga_satuan') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label>Total Harga</label>
                <input type="number" name="total_harga" class="form-control" readonly>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Tanggal Masuk</label>
                <input type="datetime-local" name="tanggal_masuk" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Tanggal Kadaluarsa</label>
                <input type="datetime-local" name="tanggal_kadaluarsa" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
            </div>
        </div>


        <div class="mb-3">
            <label>Pemasok</label>
            <select name="id_pemasok" class="form-control" required>
                <option value="" disabled selected>-- Pilih Pemasok --</option>
                @foreach ($pemasoks as $pemasok)
                    <option value="{{ $pemasok->id }}">{{ $pemasok->nama_pemasok }}</option>
                @endforeach
            </select>
        </div>


        <div class="mb-3">
            <label>Lokasi</label>
            <select name="id_lokasi" class="form-control" required>
                <option value="" disabled selected>-- Pilih Lokasi --</option>
                @foreach ($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama_lokasi }}</option>
                @endforeach
            </select>
        </div>


        <div class="mb-3">
            <label>Kondisi</label>
            <select name="id_kondisi" class="form-control" required>
                <option value="" disabled selected>-- Pilih Kondisi --</option>
                @foreach ($kondisis as $kondisi)
                    <option value="{{ $kondisi->id }}">{{ $kondisi->nama_kondisi }}</option>
                @endforeach
            </select>
        </div>


        <div class="mb-3">
            <label>Catatan</label>
            <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea>
        </div>


        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>


<script>
    function updateTotalHarga() {
        const jumlah = parseFloat(document.querySelector('input[name="jumlah"]').value) || 0;
        const harga = parseFloat(document.querySelector('input[name="harga_satuan"]').value) || 0;
        document.querySelector('input[name="total_harga"]').value = (jumlah * harga).toFixed(2);
    }


    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector('input[name="jumlah"]').addEventListener('input', updateTotalHarga);
        document.querySelector('input[name="harga_satuan"]').addEventListener('input', updateTotalHarga);
    });


    function confirmSimpan() {
        return confirm("Apakah Anda yakin ingin menyimpan data barang masuk ini?");
    }
</script>
@endsection
