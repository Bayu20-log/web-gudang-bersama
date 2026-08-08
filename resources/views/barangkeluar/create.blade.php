@extends('layouts.app')
@section('content')

<style>
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .form-label { font-weight: 500; color: #4b5563; }
    .readonly-bg { background-color: #e2e8f0 !important; cursor: not-allowed; }
</style>

<div class="container mt-2 mb-5">
    <h3 class="fw-bold mb-4" style="color: #374151;">Barang Keluar &rsaquo; Tambah Barang Keluar</h3>
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- PERBAIKAN: onsubmit diganti memanggil event SweetAlert2 -->
    <form action="{{ route('barang-keluar.store') }}" method="POST" id="barangKeluarForm" onsubmit="confirmSimpan(event)">
        @csrf
        <div class="row g-4">
            
            <!-- KOLOM KIRI (Informasi Barang & Stok) -->
            <div class="col-md-6">
                <div class="mb-4">
                    <label for="kode_lokasi_kondisi" class="form-label">Pilih Barang (Kode - Lokasi - Kondisi) <span class="text-danger">*</span></label>
                    <select name="kode_lokasi_kondisi" id="kode_lokasi_kondisi" class="form-select bg-light @error('kode_lokasi_kondisi') is-invalid @enderror" required>
                        <option value="" disabled selected>-- Pilih Barang yang Tersedia --</option>
                    </select>
                    @error('kode_lokasi_kondisi') <div class="text-danger mt-1" style="font-size: 0.85em;">{{ $message }}</div> @enderror
                </div>

                <!-- Input Hidden untuk dikirim ke Backend -->
                <input type="hidden" name="kode_barang" id="kode_barang">
                <input type="hidden" name="id_lokasi" id="id_lokasi">
                <input type="hidden" name="id_kondisi" id="id_kondisi">

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" id="nama_barang" class="form-control readonly-bg" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" id="satuan" class="form-control readonly-bg" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="stok_tersedia" class="form-label">Stok Tersedia</label>
                        <input type="number" class="form-control readonly-bg" id="stok_tersedia" readonly value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="harga_dasar" class="form-label">Harga Rata-Rata <small class="text-muted fw-normal">(Dari Beli)</small></label>
                        <input type="number" id="harga_dasar" class="form-control readonly-bg" readonly value="0">
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (Formulir Pengeluaran) -->
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jumlah_keluar" class="form-label">Jumlah Keluar <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_keluar" id="jumlah_keluar" class="form-control bg-light" placeholder="0" required>
                        <div id="jumlah_warning" class="text-danger mt-1 fw-medium" style="font-size: 0.85em;"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual / Unit <span class="text-danger">*</span></label>
                        <input type="number" name="harga_jual" id="harga_jual" class="form-control bg-light" placeholder="Rp" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="total_harga" class="form-label">Total Harga Jual</label>
                    <input type="number" class="form-control readonly-bg" id="total_harga" readonly value="0">
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan Pengeluaran <span class="text-danger">*</span></label>
                    <select name="catatan" id="catatan" class="form-select bg-light" required>
                        <option value="" disabled selected>-- Pilih Tujuan --</option>
                        <option value="Penerimaan Penjualan">Penerimaan Penjualan</option>
                        <option value="Penghapusan">Penghapusan</option>
                        <option value="Retur">Retur</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="penerima" class="form-label">Penerima <span class="text-danger">*</span></label>
                        <input type="text" name="penerima" id="penerima" class="form-control bg-light @error('penerima') is-invalid @enderror" placeholder="Nama penerima" required>
                        @error('penerima') <div class="text-danger mt-1" style="font-size: 0.85em;">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lokasi_tujuan" class="form-label">Lokasi Tujuan <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi_tujuan" id="lokasi_tujuan" class="form-control bg-light @error('lokasi_tujuan') is-invalid @enderror" placeholder="Gudang/Toko tujuan" required>
                        @error('lokasi_tujuan') <div class="text-danger mt-1" style="font-size: 0.85em;">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi di Kanan Bawah --}}
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('barang-keluar.index') }}" class="btn btn-outline-secondary px-4 fw-medium">Batal</a>
            <button type="submit" class="btn btn-orange px-5 fw-bold" id="submitBtn"><i class="bi bi-box-arrow-up me-2"></i>Keluarkan Barang</button>
        </div>
    </form>
</div>

<!-- ============================================== -->
<!-- SCRIPT PENGAMBILAN DATA STOK & KONFIRMASI -->
<!-- ============================================== -->
<script>
// Helper membersihkan angka
function num(val) {
  if (val == null) return 0;
  const n = parseFloat(String(val).replace(/[^\d.-]/g, ''));
  return isNaN(n) ? 0 : n;
}

// Ambil harga dari response JSON
function pickHarga(obj) {
  if (!obj || typeof obj !== 'object') return 0;
  if (obj.harga_dasar != null) return num(obj.harga_dasar);
  if (obj.harga != null) return num(obj.harga);
  if (obj.harga_satuan != null) return num(obj.harga_satuan);
  if (obj.item && obj.item.harga_dasar != null) return num(obj.item.harga_dasar);
  return 0;
}

// Load opsi barang ke dalam dropdown
async function loadPilihanBarang() {
  const res = await fetch("{{ route('barang-keluar.pilihan-barang') }}");
  const data = await res.json();
  const select = document.getElementById("kode_lokasi_kondisi");
  
  data.forEach(item => {
    const opt = document.createElement("option");
    opt.value = `${item.kode}|${item.lokasi_id}|${item.kondisi_id}`;
    opt.text  = `${item.kode} - ${item.nama_barang} - ${item.lokasi} - ${item.kondisi}`;
    if (item.nama_barang) opt.dataset.nama = item.nama_barang;
    if (item.satuan)      opt.dataset.satuan = item.satuan;
    if (item.stok != null) opt.dataset.stok = item.stok;
    
    const harga = pickHarga(item);
    if (item.harga_dasar !== undefined || item.harga !== undefined || item.harga_satuan !== undefined) {
      opt.dataset.harga = harga; 
    }
    select.appendChild(opt);
  });
}

// Mengambil detail khusus saat barang dipilih
async function fetchDetail(kode, lokasi, kondisi) {
  const DETAIL_URL = "{{ route('barang-keluar.detail-barang') }}";
  const url = `${DETAIL_URL}?kode_barang=${encodeURIComponent(kode)}&id_lokasi=${encodeURIComponent(lokasi)}&id_kondisi=${encodeURIComponent(kondisi)}`;
  const res = await fetch(url);
  const data = await res.json();
  
  document.getElementById('nama_barang').value   = data.nama_barang ?? document.getElementById('nama_barang').value;
  document.getElementById('satuan').value        = data.satuan ?? document.getElementById('satuan').value;
  document.getElementById('stok_tersedia').value = num(data.stok ?? document.getElementById('stok_tersedia').value);
  document.getElementById('harga_dasar').value   = pickHarga(data);
}

// PERBAIKAN: Fungsi SweetAlert2 untuk konfirmasi simpan
function confirmSimpan(event) {
    event.preventDefault(); // Mencegah form langsung tersubmit

    Swal.fire({
        title: 'Konfirmasi Pengeluaran',
        text: "Apakah Anda yakin ingin mengeluarkan barang ini dari gudang?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316', // Warna oranye sesuai tema
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-box-arrow-up me-1"></i> Ya, Keluarkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika user klik "Ya", submit form secara manual
            document.getElementById('barangKeluarForm').submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
  const select        = document.getElementById('kode_lokasi_kondisi');
  const jumlahKeluar  = document.getElementById('jumlah_keluar');
  const hargaJual     = document.getElementById('harga_jual');
  const totalHarga    = document.getElementById('total_harga');
  const submitBtn     = document.getElementById('submitBtn');

  // Load Data
  loadPilihanBarang().then(() => {
    if (select.value) select.dispatchEvent(new Event('change'));
  });

  async function onSelectChange() {
    const val = select.value; 
    if (!val) return;
    
    const [kode, lokasi, kondisi] = val.split('|');
    document.getElementById('kode_barang').value = kode;
    document.getElementById('id_lokasi').value   = lokasi;
    document.getElementById('id_kondisi').value  = kondisi;
    
    const opt = select.options[select.selectedIndex];
    if (opt) {
      if (opt.dataset.nama)   document.getElementById('nama_barang').value = opt.dataset.nama;
      if (opt.dataset.satuan) document.getElementById('satuan').value      = opt.dataset.satuan;
      if (opt.dataset.stok != null) document.getElementById('stok_tersedia').value = num(opt.dataset.stok);
      if (opt.dataset.harga !== undefined) document.getElementById('harga_dasar').value = num(opt.dataset.harga);
    }
    
    await fetchDetail(kode, lokasi, kondisi);
    updateTotal();
  }

  select.addEventListener('change', onSelectChange);

  function updateTotal() {
    totalHarga.value = num(jumlahKeluar.value) * num(hargaJual.value);
  }

  jumlahKeluar.addEventListener('input', function() {
    const jumlahStr = this.value;
    const jumlah = num(jumlahStr);
    const stok   = num(document.getElementById('stok_tersedia').value);
    const warning = document.getElementById('jumlah_warning');
    
    if (!/^\d+$/.test(jumlahStr) && jumlahStr !== "") { 
        warning.textContent = "Jumlah harus bilangan bulat positif."; 
        submitBtn.disabled = true; 
    }
    else if (jumlah < 1 && jumlahStr !== "") { 
        warning.textContent = "Jumlah tidak boleh kurang dari 1."; 
        submitBtn.disabled = true; 
    }
    else if (jumlah > stok) { 
        warning.textContent = `Peringatan: Jumlah melebihi stok yang tersedia (${stok}).`; 
        submitBtn.disabled = true; 
    }
    else { 
        warning.textContent = ""; 
        submitBtn.disabled = false; 
    }
    updateTotal();
  });

  hargaJual.addEventListener('input', function() {
    const harga = num(this.value);
    if (harga < 0 || isNaN(harga)) {
        submitBtn.disabled = true;
    } else {
        // Cek kembali kondisi jumlah keluar sebelum mengaktifkan tombol
        const jumlah = num(jumlahKeluar.value);
        const stok   = num(document.getElementById('stok_tersedia').value);
        if (jumlah > 0 && jumlah <= stok) {
            submitBtn.disabled = false;
        }
    }
    updateTotal();
  });
});
</script>
@endsection