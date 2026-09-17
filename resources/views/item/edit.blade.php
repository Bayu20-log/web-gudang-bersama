@extends('layouts.app')
@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .bg-dark-header { background-color: #1e293b !important; color: #fff; border-bottom: 2px solid #f97316; }
    
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
    .preview-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .form-label { font-weight: 500; color: #4b5563; }

    .select2-container--bootstrap-5 .select2-selection {
        background-color: #f8fafc;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        min-height: 38px;
    }
</style>

<div class="container mt-2 mb-5">
    <h3 class="fw-bold mb-4" style="color: #374151;">Katalog Produk &rsaquo; Edit Produk</h3>
    
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
                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" id="nama_barang" class="form-control bg-light" value="{{ old('nama_barang', $item->nama_barang) }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="id_kategori" id="id_kategori" class="form-select select2-custom" data-placeholder="Pilih atau cari kategori..." data-modal="#modalKategori" required>
                        <option value=""></option>
                        <option value="ADD_NEW">Buat Kategori Baru</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}" {{ old('id_kategori', $item->id_kategori) == $k->id ? 'selected' : '' }}>
                                {{ $k->kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="stok_minimum" class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                    <input type="number" name="stok_minimum" id="stok_minimum" class="form-control bg-light" value="{{ old('stok_minimum', $item->stok_minimum) }}" required>
                </div>

                <div class="mb-3">
                    <label for="foto" class="form-label">Foto Produk</label>
                    <div class="d-flex gap-3 align-items-start flex-column flex-sm-row">
                        <div class="preview-box w-100" id="previewBox" style="max-width: 300px;">
                            @if ($item->foto)
                                <img id="imagePreview" src="{{ asset('storage/' . $item->foto) }}" style="display: block;">
                                <span id="previewText" class="text-muted fw-medium" style="display: none;"><i class="bi bi-image me-1"></i>Preview Foto</span>
                            @else
                                <img id="imagePreview" style="display: none;">
                                <span id="previewText" class="text-muted fw-medium"><i class="bi bi-image me-1"></i>Preview Foto</span>
                            @endif
                        </div>
                        <div class="w-100 mt-sm-auto mb-sm-auto text-start">
                            <input type="file" name="foto" id="foto" class="d-none" accept="image/*" onchange="previewFile(event)">
                            <button type="button" class="btn btn-outline-secondary bg-white px-4 fw-medium" onclick="document.getElementById('foto').click()">Ganti Foto</button>
                            <div class="text-muted mt-2" style="font-size: 0.85em;">Biarkan kosong jika tidak ingin mengubah foto.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="harga_dasar" class="form-label">Harga Dasar <span class="text-danger">*</span></label>
                    <input type="number" name="harga_dasar" id="harga_dasar" class="form-control bg-light" value="{{ old('harga_dasar', $item->harga_dasar) }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="id_satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                    <select name="id_satuan" id="id_satuan" class="form-select select2-custom" data-placeholder="Pilih atau cari satuan..." data-modal="#modalSatuan" required>
                        <option value=""></option>
                        <option value="ADD_NEW">Buat Satuan Baru</option>
                        @foreach ($satuan as $s)
                            <option value="{{ $s->id }}" {{ old('id_satuan', $item->id_satuan) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Catatan / Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control bg-light" rows="5">{{ old('deskripsi', $item->deskripsi) }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('item.index') }}" class="btn btn-outline-secondary px-4 fw-medium">Batal</a>
            <button type="submit" class="btn btn-orange px-5 fw-bold"><i class="bi bi-save me-2"></i>Perbarui Produk</button>
        </div>
    </form>
</div>

<!-- MODAL KATEGORI & SATUAN -->
<div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-dark-header">
        <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
         <div class="mb-3">
             <label class="form-label fw-medium text-secondary">Nama Kategori <span class="text-danger">*</span></label>
             <input type="text" id="input_kategori_baru" class="form-control" placeholder="Masukkan nama kategori">
             <div id="error_kategori" class="text-danger mt-1 fw-medium" style="display: none; font-size: 0.875em;"></div>
         </div>
         <div class="mb-2">
             <label class="form-label fw-medium text-secondary">Deskripsi <span class="text-muted fw-normal">(Opsional)</span></label>
             <textarea id="input_deskripsi_kategori" class="form-control" rows="2" placeholder="Penjelasan singkat kategori..."></textarea>
         </div>
      </div>
      <div class="modal-footer border-0 pb-4 pe-4">
        <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-orange px-4 fw-bold" onclick="simpanKategori()" id="btnSaveKategori">Simpan</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalSatuan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-dark-header">
        <h5 class="modal-title fw-bold">Tambah Satuan Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
         <div class="mb-2">
             <label class="form-label fw-medium text-secondary">Nama Satuan <span class="text-danger">*</span></label>
             <input type="text" id="input_satuan_baru" class="form-control" placeholder="Contoh: Pcs, Kg, Box">
             <div id="error_satuan" class="text-danger mt-1 fw-medium" style="display: none; font-size: 0.875em;"></div>
         </div>
      </div>
      <div class="modal-footer border-0 pb-4 pe-4">
        <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-orange px-4 fw-bold" onclick="simpanSatuan()" id="btnSaveSatuan">Simpan</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-custom').select2({
            theme: 'bootstrap-5',
            placeholder: function(){ return $(this).data('placeholder'); },
            templateResult: formatOpsiSelect
        }).on('select2:select', function (e) {
            if (e.params.data.id === 'ADD_NEW') {
                $(this).val('').trigger('change');
                let targetModal = $(this).data('modal');
                let modal = new bootstrap.Modal(document.querySelector(targetModal));
                modal.show();
            }
        });

        function formatOpsiSelect (opsi) {
            if (!opsi.id) { return opsi.text; }
            if (opsi.id === 'ADD_NEW') {
                return $('<span style="color: #f97316; font-weight: bold;"><i class="fa-solid fa-plus me-1"></i> ' + opsi.text + '</span>');
            }
            return opsi.text;
        }
    });

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

    async function simpanKategori() {
        const inputNama = document.getElementById('input_kategori_baru').value;
        const inputDeskripsi = document.getElementById('input_deskripsi_kategori').value;
        const errorDiv = document.getElementById('error_kategori');
        const btnSave = document.getElementById('btnSaveKategori');
        errorDiv.style.display = 'none';
        btnSave.disabled = true;
        btnSave.innerText = 'Menyimpan...';
        try {
            const response = await fetch("{{ route('kategori.storeAjax') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ kategori: inputNama, deskripsi: inputDeskripsi })
            });
            const data = await response.json();
            if (response.ok) {
                var newOption = new Option(data.data.kategori, data.data.id, true, true);
                $('#id_kategori').append(newOption).trigger('change');
                bootstrap.Modal.getInstance(document.getElementById('modalKategori')).hide();
                document.getElementById('input_kategori_baru').value = '';
                document.getElementById('input_deskripsi_kategori').value = '';
            } else {
                errorDiv.innerText = data.message || 'Gagal menyimpan data';
                errorDiv.style.display = 'block';
            }
        } catch (error) {
            errorDiv.innerText = 'Terjadi kesalahan pada sistem.';
            errorDiv.style.display = 'block';
        } finally { btnSave.disabled = false; btnSave.innerText = 'Simpan'; }
    }

    async function simpanSatuan() {
        const inputVal = document.getElementById('input_satuan_baru').value;
        const errorDiv = document.getElementById('error_satuan');
        const btnSave = document.getElementById('btnSaveSatuan');
        errorDiv.style.display = 'none';
        btnSave.disabled = true;
        btnSave.innerText = 'Menyimpan...';
        try {
            const response = await fetch("{{ route('satuan.storeAjax') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ nama_satuan: inputVal })
            });
            const data = await response.json();
            if (response.ok) {
                var newOption = new Option(data.data.nama_satuan, data.data.id, true, true);
                $('#id_satuan').append(newOption).trigger('change');
                bootstrap.Modal.getInstance(document.getElementById('modalSatuan')).hide();
                document.getElementById('input_satuan_baru').value = '';
            } else {
                errorDiv.innerText = data.message || 'Gagal menyimpan data';
                errorDiv.style.display = 'block';
            }
        } catch (error) {
            errorDiv.innerText = 'Terjadi kesalahan pada sistem.';
            errorDiv.style.display = 'block';
        } finally { btnSave.disabled = false; btnSave.innerText = 'Simpan'; }
    }
</script>
@endsection