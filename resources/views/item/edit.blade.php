@extends('layouts.app')
@section('content')

<style>
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    
    .preview-box {
        width: 100%;
        height: 150px;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8fafc;
        overflow: hidden;
        margin-top: 10px;
    }
    .preview-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .form-label { font-weight: 500; color: #4b5563; }
</style>

<div class="container mt-2 mb-5">
    <h3 class="fw-bold mb-4" style="color: #374151;">Data Barang &rsaquo; Edit Barang</h3>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('item.update', $item->kode_barang) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <!-- KOLOM KIRI -->
            <div class="col-md-6">
                {{-- Nama Barang --}}
                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" id="nama_barang" class="form-control bg-light" value="{{ old('nama_barang', $item->nama_barang) }}" required>
                </div>

                {{-- Kategori --}}
                <div class="mb-3">
                    <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="id_kategori" id="id_kategori" class="form-select bg-light" required>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}" {{ old('id_kategori', $item->id_kategori) == $k->id ? 'selected' : '' }}>
                                {{ $k->kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Stok Minimum --}}
                <div class="mb-3">
                    <label for="stok_minimum" class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                    <input type="number" name="stok_minimum" id="stok_minimum" class="form-control bg-light" value="{{ old('stok_minimum', $item->stok_minimum) }}" required>
                </div>

                {{-- Foto & Preview --}}
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto Barang</label>
                    <div class="d-flex gap-3 align-items-start flex-column flex-sm-row">
                        <!-- Kotak Preview -->
                        <div class="preview-box w-100" id="previewBox" style="max-width: 300px;">
                            @if ($item->foto)
                                <img id="imagePreview" src="{{ asset('storage/' . $item->foto) }}" style="display: block;">
                                <span id="previewText" class="text-muted fw-medium" style="display: none;"><i class="bi bi-image me-1"></i>Preview Foto</span>
                            @else
                                <img id="imagePreview" style="display: none;">
                                <span id="previewText" class="text-muted fw-medium"><i class="bi bi-image me-1"></i>Preview Foto</span>
                            @endif
                        </div>
                        <!-- Input File -->
                        <div class="w-100 mt-sm-auto mb-sm-auto text-start">
                            <input type="file" name="foto" id="foto" class="d-none" accept="image/*" onchange="previewFile(event)">
                            <button type="button" class="btn btn-outline-secondary bg-white px-4 fw-medium" onclick="document.getElementById('foto').click()">Ganti Foto</button>
                            <div class="text-muted mt-2" style="font-size: 0.85em;">Biarkan kosong jika tidak ingin mengubah foto.</div>
                            @error('foto')
                                <div class="text-danger mt-1 text-start" style="font-size: 0.85em;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN -->
            <div class="col-md-6">
                {{-- Harga Dasar --}}
                <div class="mb-3">
                    <label for="harga_dasar" class="form-label">Harga Dasar <span class="text-danger">*</span></label>
                    <input type="number" name="harga_dasar" id="harga_dasar" class="form-control bg-light" value="{{ old('harga_dasar', $item->harga_dasar) }}" required>
                </div>

                {{-- Satuan --}}
                <div class="mb-3">
                    <label for="id_satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                    <select name="id_satuan" id="id_satuan" class="form-select bg-light" required>
                        @foreach ($satuan as $s)
                            <option value="{{ $s->id }}" {{ old('id_satuan', $item->id_satuan) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Catatan / Deskripsi --}}
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Catatan / Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control bg-light" rows="5">{{ old('deskripsi', $item->deskripsi) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi di Kanan Bawah --}}
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('item.index') }}" class="btn btn-outline-secondary px-4 fw-medium">Batal</a>
            <button type="submit" class="btn btn-orange px-5 fw-bold"><i class="bi bi-save me-2"></i>Perbarui Barang</button>
        </div>
    </form>
</div>

<script>
    // Preview Gambar Secara Real-time untuk form Edit
    function previewFile(event) {
        const file = event.target.files[0];
        const previewImg = document.getElementById('imagePreview');
        const previewText = document.getElementById('previewText');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                previewText.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection