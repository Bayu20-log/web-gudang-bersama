@extends('layouts.app')
@section('content')

<!-- CDN jQuery & Select2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .bg-dark-header { background-color: #1e293b !important; color: #fff; border-bottom: 2px solid #f97316; }
    .form-label { font-weight: 500; color: #4b5563; }
    
    .select2-container--bootstrap-5 .select2-selection {
        background-color: #f8fafc;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        min-height: 38px;
    }
</style>

<div class="container mt-2 mb-5">
    <h3 class="fw-bold mb-4" style="color: #374151;">Barang Masuk &rsaquo; Tambah Transaksi</h3>
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="formBarangMasuk" method="POST" action="{{ route('barang-masuk.store') }}" onsubmit="confirmSimpan(event)">
        @csrf
        <div class="row g-4">
            
            <!-- KOLOM KIRI -->
            <div class="col-md-6">
                {{-- Pilih Produk --}}
                <div class="mb-3">
                    <label for="kode_barang" class="form-label">Pilih Produk <span class="text-danger">*</span></label>
                    <select name="kode_barang" id="kode_barang" class="form-select select2-custom @error('kode_barang') is-invalid @enderror" data-placeholder="Cari atau pilih produk..." data-action="redirect" required>
                        <option value=""></option>
                        <option value="ADD_NEW">Buat Produk Baru</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->kode_barang }}" {{ old('kode_barang') == $item->kode_barang ? 'selected' : '' }}>
                                {{ $item->kode_barang }} - {{ $item->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                    @error('kode_barang') <div class="text-danger mt-1" style="font-size: 0.85em;">{{ $message }}</div> @enderror
                </div>
                
                {{-- Jumlah & Harga Beli --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control bg-light @error('jumlah') is-invalid @enderror" min="1" value="{{ old('jumlah') }}" required>
                        @error('jumlah') <div class="text-danger mt-1" style="font-size: 0.85em;">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                        <input type="number" name="harga_satuan" id="harga_satuan" class="form-control bg-light @error('harga_satuan') is-invalid @enderror" min="0" value="{{ old('harga_satuan') }}" required>
                        @error('harga_satuan') <div class="text-danger mt-1" style="font-size: 0.85em;">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                {{-- Total Harga (Readonly) --}}
                <div class="mb-3">
                    <label class="form-label">Total Harga</label>
                    <input type="number" name="total_harga" id="total_harga" class="form-control" readonly style="background-color: #e2e8f0;">
                </div>
                
                {{-- Tanggal Masuk & Kadaluarsa --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_masuk" class="form-control bg-light" value="{{ old('tanggal_masuk', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Kadaluarsa <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_kadaluarsa" class="form-control bg-light" value="{{ old('tanggal_kadaluarsa', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN -->
            <div class="col-md-6">
                {{-- Pemasok --}}
                <div class="mb-3">
                    <label class="form-label">Pemasok <span class="text-danger">*</span></label>
                    <select name="id_pemasok" id="id_pemasok" class="form-select select2-custom" data-placeholder="Cari pemasok..." data-action="#modalPemasok" required>
                        <option value=""></option>
                        <option value="ADD_NEW">Buat Pemasok Baru</option>
                        @foreach ($pemasoks as $pemasok)
                            <option value="{{ $pemasok->id }}" {{ old('id_pemasok') == $pemasok->id ? 'selected' : '' }}>
                                {{ $pemasok->nama_pemasok }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Lokasi --}}
                <div class="mb-3">
                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                    <select name="id_lokasi" id="id_lokasi" class="form-select select2-custom" data-placeholder="Cari lokasi gudang..." data-action="#modalLokasi" required>
                        <option value=""></option>
                        <option value="ADD_NEW">Buat Lokasi Baru</option>
                        @foreach ($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}" {{ old('id_lokasi') == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Kondisi --}}
                <div class="mb-3">
                    <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                    <select name="id_kondisi" id="id_kondisi" class="form-select select2-custom" data-placeholder="Cari kondisi..." data-action="#modalKondisi" required>
                        <option value=""></option>
                        <option value="ADD_NEW">Buat Kondisi Baru</option>
                        @foreach ($kondisis as $kondisi)
                            <option value="{{ $kondisi->id }}" {{ old('id_kondisi') == $kondisi->id ? 'selected' : '' }}>
                                {{ $kondisi->nama_kondisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Catatan --}}
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control bg-light" rows="3" placeholder="Tambahkan catatan khusus...">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-secondary px-4 fw-medium">Batal</a>
            <button type="submit" class="btn btn-orange px-5 fw-bold"><i class="bi bi-save me-2"></i>Simpan Barang Masuk</button>
        </div>
    </form>
</div>

<!-- MODAL PEMASOK -->
<div class="modal fade" id="modalPemasok" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-dark-header">
        <h5 class="modal-title fw-bold">Tambah Pemasok Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
         <div class="mb-3">
             <label class="form-label fw-medium text-secondary">Nama Pemasok <span class="text-danger">*</span></label>
             <input type="text" id="input_nama_pemasok" class="form-control" placeholder="Contoh: PT. Sumber Makmur">
             <div id="error_pemasok" class="text-danger mt-1 fw-medium" style="display: none; font-size: 0.875em;"></div>
         </div>
         <div class="mb-3">
             <label class="form-label fw-medium text-secondary">Nama PIC <span class="text-danger">*</span></label>
             <input type="text" id="input_pic_pemasok" class="form-control" placeholder="Nama penanggung jawab">
         </div>
         <div class="mb-2">
             <label class="form-label fw-medium text-secondary">Email <span class="text-danger">*</span></label>
             <input type="email" id="input_email_pemasok" class="form-control" placeholder="email@perusahaan.com">
         </div>
      </div>
      <div class="modal-footer border-0 pb-4 pe-4">
        <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-orange px-4 fw-bold" onclick="simpanPemasok()" id="btnSavePemasok">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL LOKASI -->
<div class="modal fade" id="modalLokasi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-dark-header">
        <h5 class="modal-title fw-bold">Tambah Lokasi Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
         <div class="mb-3">
             <label class="form-label fw-medium text-secondary">Nama Lokasi <span class="text-danger">*</span></label>
             <input type="text" id="input_nama_lokasi" class="form-control" placeholder="Contoh: Gudang A, Rak B">
             <div id="error_lokasi" class="text-danger mt-1 fw-medium" style="display: none; font-size: 0.875em;"></div>
         </div>
         <div class="mb-2">
             <label class="form-label fw-medium text-secondary">Deskripsi <span class="text-muted fw-normal">(Opsional)</span></label>
             <textarea id="input_deskripsi_lokasi" class="form-control" rows="2"></textarea>
         </div>
      </div>
      <div class="modal-footer border-0 pb-4 pe-4">
        <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-orange px-4 fw-bold" onclick="simpanLokasi()" id="btnSaveLokasi">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL KONDISI -->
<div class="modal fade" id="modalKondisi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-dark-header">
        <h5 class="modal-title fw-bold">Tambah Kondisi Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
         <div class="mb-3">
             <label class="form-label fw-medium text-secondary">Nama Kondisi <span class="text-danger">*</span></label>
             <input type="text" id="input_nama_kondisi" class="form-control" placeholder="Contoh: Baik, Rusak">
             <div id="error_kondisi" class="text-danger mt-1 fw-medium" style="display: none; font-size: 0.875em;"></div>
         </div>
         <div class="mb-2">
             <label class="form-label fw-medium text-secondary">Deskripsi <span class="text-muted fw-normal">(Opsional)</span></label>
             <textarea id="input_deskripsi_kondisi" class="form-control" rows="2"></textarea>
         </div>
      </div>
      <div class="modal-footer border-0 pb-4 pe-4">
        <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-orange px-4 fw-bold" onclick="simpanKondisi()" id="btnSaveKondisi">Simpan</button>
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
                let targetAction = $(this).data('action');
                
                // Cek apakah aksinya pindah halaman atau buka modal
                if (targetAction === 'redirect') {
                    window.location.href = "{{ route('item.create') }}?redirect=barang-masuk";
                } else {
                    let modal = new bootstrap.Modal(document.querySelector(targetAction));
                    modal.show();
                }
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

    function updateTotalHarga() {
        const jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
        const harga = parseFloat(document.getElementById('harga_satuan').value) || 0;
        document.getElementById('total_harga').value = (jumlah * harga).toFixed(2);
    }
    document.getElementById('jumlah').addEventListener('input', updateTotalHarga);
    document.getElementById('harga_satuan').addEventListener('input', updateTotalHarga);

    function confirmSimpan(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Penyimpanan',
            text: "Apakah Anda yakin ingin menyimpan data transaksi ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f97316',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-save me-1"></i> Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formBarangMasuk').submit();
            }
        });
    }

    async function simpanPemasok() {
        const inputNama = document.getElementById('input_nama_pemasok').value;
        const inputPic = document.getElementById('input_pic_pemasok').value;
        const inputEmail = document.getElementById('input_email_pemasok').value;
        const errorDiv = document.getElementById('error_pemasok');
        const btnSave = document.getElementById('btnSavePemasok');
        errorDiv.style.display = 'none'; btnSave.disabled = true; btnSave.innerText = 'Menyimpan...';
        try {
            const response = await fetch("{{ route('pemasok.storeAjax') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ nama_pemasok: inputNama, nama_pic: inputPic, email: inputEmail })
            });
            const data = await response.json();
            if (response.ok) {
                var newOption = new Option(data.data.nama_pemasok, data.data.id, true, true);
                $('#id_pemasok').append(newOption).trigger('change');
                bootstrap.Modal.getInstance(document.getElementById('modalPemasok')).hide();
                document.getElementById('input_nama_pemasok').value = '';
                document.getElementById('input_pic_pemasok').value = '';
                document.getElementById('input_email_pemasok').value = '';
            } else {
                errorDiv.innerText = data.message || 'Gagal menyimpan data';
                errorDiv.style.display = 'block';
            }
        } catch (error) { errorDiv.innerText = 'Terjadi kesalahan sistem.'; errorDiv.style.display = 'block'; } 
        finally { btnSave.disabled = false; btnSave.innerText = 'Simpan'; }
    }

    async function simpanLokasi() {
        const inputNama = document.getElementById('input_nama_lokasi').value;
        const inputDeskripsi = document.getElementById('input_deskripsi_lokasi').value;
        const errorDiv = document.getElementById('error_lokasi');
        const btnSave = document.getElementById('btnSaveLokasi');
        errorDiv.style.display = 'none'; btnSave.disabled = true; btnSave.innerText = 'Menyimpan...';
        try {
            const response = await fetch("{{ route('lokasi.storeAjax') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ nama_lokasi: inputNama, deskripsi: inputDeskripsi })
            });
            const data = await response.json();
            if (response.ok) {
                var newOption = new Option(data.data.nama_lokasi, data.data.id, true, true);
                $('#id_lokasi').append(newOption).trigger('change');
                bootstrap.Modal.getInstance(document.getElementById('modalLokasi')).hide();
                document.getElementById('input_nama_lokasi').value = '';
            } else {
                errorDiv.innerText = data.message || 'Gagal menyimpan data';
                errorDiv.style.display = 'block';
            }
        } catch (error) { errorDiv.innerText = 'Terjadi kesalahan sistem.'; errorDiv.style.display = 'block'; } 
        finally { btnSave.disabled = false; btnSave.innerText = 'Simpan'; }
    }

    async function simpanKondisi() {
        const inputNama = document.getElementById('input_nama_kondisi').value;
        const inputDeskripsi = document.getElementById('input_deskripsi_kondisi').value;
        const errorDiv = document.getElementById('error_kondisi');
        const btnSave = document.getElementById('btnSaveKondisi');
        errorDiv.style.display = 'none'; btnSave.disabled = true; btnSave.innerText = 'Menyimpan...';
        try {
            const response = await fetch("{{ route('kondisi.storeAjax') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ nama_kondisi: inputNama, deskripsi: inputDeskripsi })
            });
            const data = await response.json();
            if (response.ok) {
                var newOption = new Option(data.data.nama_kondisi, data.data.id, true, true);
                $('#id_kondisi').append(newOption).trigger('change');
                bootstrap.Modal.getInstance(document.getElementById('modalKondisi')).hide();
                document.getElementById('input_nama_kondisi').value = '';
            } else {
                errorDiv.innerText = data.message || 'Gagal menyimpan data';
                errorDiv.style.display = 'block';
            }
        } catch (error) { errorDiv.innerText = 'Terjadi kesalahan sistem.'; errorDiv.style.display = 'block'; } 
        finally { btnSave.disabled = false; btnSave.innerText = 'Simpan'; }
    }
</script>
@endsection