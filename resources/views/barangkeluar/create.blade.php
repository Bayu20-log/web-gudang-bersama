@extends('layouts.app')


@section('content')
<div class="container"  style="margin-top: 50px;"><!-- turunin dikit dari navbar -->
    <h2>Form Barang Keluar</h2>


    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <form action="{{ route('barang-keluar.store') }}" method="POST" id="barangKeluarForm">
        @csrf


        <div class="mb-3">
            <label for="kode_lokasi_kondisi">Pilih Barang (Kode - Lokasi - Kondisi)</label>
            <select name="kode_lokasi_kondisi" id="kode_lokasi_kondisi" class="form-control @error('kode_lokasi_kondisi') is-invalid @enderror" required>
                <option value="" disabled selected>-- Pilih Barang --</option>
            </select>
            @error('kode_lokasi_kondisi') <div class="text-danger">{{ $message }}</div> @enderror
        </div>


        <input type="hidden" name="kode_barang" id="kode_barang">
        <input type="hidden" name="id_lokasi" id="id_lokasi">
        <input type="hidden" name="id_kondisi" id="id_kondisi">


        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" id="nama_barang" class="form-control" readonly>
        </div>


        <div class="mb-3">
            <label>Satuan</label>
            <input type="text" id="satuan" class="form-control" readonly>
        </div>


        <div class="mb-3">
            <label for="stok_tersedia">Stok Tersedia</label>
            <input type="number" class="form-control" id="stok_tersedia" readonly value="0">
        </div>


        <!-- Harga Rata-Rata otomatis -->
        <div class="mb-3">
            <label for="harga_dasar">Harga Rata-Rata (diambil dari Harga Beli)</label>
            <input type="number" id="harga_dasar" class="form-control" readonly value="0">
        </div>


        <div class="mb-3">
            <label for="jumlah_keluar">Jumlah Keluar</label>
            <input type="number" name="jumlah_keluar" id="jumlah_keluar" class="form-control" required>
            <div id="jumlah_warning" class="text-danger mt-1"></div>
        </div>


        <div class="mb-3">
            <label for="harga_jual">Harga Jual / Unit</label>
            <input type="number" name="harga_jual" id="harga_jual" class="form-control" required>
        </div>


        <div class="mb-3">
            <label for="total_harga">Total Harga Jual</label>
            <input type="number" class="form-control" id="total_harga" readonly value="0">
        </div>


        <div class="mb-3">
            <label for="catatan">Catatan</label>
            <select name="catatan" id="catatan" class="form-control" required>
                <option value="" disabled selected>-- Pilih Catatan --</option>
                <option value="Penerimaan Penjualan">Penerimaan Penjualan</option>
                <option value="Penghapusan">Penghapusan</option>
                <option value="Retur">Retur</option>
            </select>
        </div>


        <div class="mb-3">
            <label for="penerima">Penerima</label>
            <input type="text" name="penerima" id="penerima" class="form-control @error('penerima') is-invalid @enderror" required>
            @error('penerima') <div class="text-danger">{{ $message }}</div> @enderror
        </div>


        <div class="mb-3">
            <label for="lokasi_tujuan">Lokasi Tujuan</label>
            <input type="text" name="lokasi_tujuan" id="lokasi_tujuan" class="form-control @error('lokasi_tujuan') is-invalid @enderror" required>
            @error('lokasi_tujuan') <div class="text-danger">{{ $message }}</div> @enderror
        </div>


        <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
    </form>
</div>


<script>
// helper angka
function num(val){
  if(val==null) return 0;
  const n = parseFloat(String(val).replace(/[^\d.-]/g,''));
  return isNaN(n) ? 0 : n;
}


// Ambil harga dari response
function pickHarga(obj){
  if(!obj || typeof obj!=='object') return 0;
  if (obj.harga_dasar  != null) return num(obj.harga_dasar);   // dari API (kita isi = harga_satuan)
  if (obj.harga        != null) return num(obj.harga);
  if (obj.harga_satuan != null) return num(obj.harga_satuan);   // fallback ekstra
  if (obj.item && obj.item.harga_dasar != null) return num(obj.item.harga_dasar);
  return 0;
}


// load opsi dropdown
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
    if (item.stok!=null)  opt.dataset.stok = item.stok;
    const harga = pickHarga(item);
    if (item.harga_dasar!==undefined || item.harga!==undefined || item.harga_satuan!==undefined) {
      opt.dataset.harga = harga; // simpan meski 0
    }
    select.appendChild(opt);
  });
}


// ambil detail untuk isi stok/harga dst
async function fetchDetail(kode,lokasi,kondisi){
  const DETAIL_URL = "{{ route('barang-keluar.detail-barang') }}";
  const url = `${DETAIL_URL}?kode_barang=${encodeURIComponent(kode)}&id_lokasi=${encodeURIComponent(lokasi)}&id_kondisi=${encodeURIComponent(kondisi)}`;
  const res = await fetch(url);
  const data = await res.json();


  document.getElementById('nama_barang').value   = data.nama_barang ?? document.getElementById('nama_barang').value;
  document.getElementById('satuan').value        = data.satuan ?? document.getElementById('satuan').value;
  document.getElementById('stok_tersedia').value = num(data.stok ?? document.getElementById('stok_tersedia').value);
  document.getElementById('harga_dasar').value   = pickHarga(data);
}


document.addEventListener('DOMContentLoaded', function () {
  const select        = document.getElementById('kode_lokasi_kondisi');
  const jumlahKeluar  = document.getElementById('jumlah_keluar');
  const hargaJual     = document.getElementById('harga_jual');
  const totalHarga    = document.getElementById('total_harga');
  const submitBtn     = document.getElementById('submitBtn');


  // setelah opsi dimuat, trigger sekali supaya field keisi
  loadPilihanBarang().then(() => {
    if (select.value) select.dispatchEvent(new Event('change'));
  });


  async function onSelectChange(){
    const val = select.value; if(!val) return;
    const [kode,lokasi,kondisi] = val.split('|');


    document.getElementById('kode_barang').value = kode;
    document.getElementById('id_lokasi').value   = lokasi;
    document.getElementById('id_kondisi').value  = kondisi;


    const opt = select.options[select.selectedIndex];
    if (opt){
      if (opt.dataset.nama)   document.getElementById('nama_barang').value = opt.dataset.nama;
      if (opt.dataset.satuan) document.getElementById('satuan').value      = opt.dataset.satuan;
      if (opt.dataset.stok!=null) document.getElementById('stok_tersedia').value = num(opt.dataset.stok);
      if (opt.dataset.harga !== undefined) document.getElementById('harga_dasar').value = num(opt.dataset.harga);
    }


    await fetchDetail(kode,lokasi,kondisi);
    updateTotal();
  }
  select.addEventListener('change', onSelectChange);


  function updateTotal(){
    totalHarga.value = num(jumlahKeluar.value) * num(hargaJual.value);
  }


  jumlahKeluar.addEventListener('input', function(){
    const jumlahStr = this.value;
    const jumlah = num(jumlahStr);
    const stok   = num(document.getElementById('stok_tersedia').value);
    const warning = document.getElementById('jumlah_warning');


    if (!/^\d+$/.test(jumlahStr)) { warning.textContent="Jumlah harus bilangan bulat positif."; submitBtn.disabled=true; }
    else if (jumlah < 1)          { warning.textContent="Jumlah tidak boleh kurang dari 1.";   submitBtn.disabled=true; }
    else if (jumlah > stok)       { warning.textContent=`Jumlah melebihi stok (${stok}).`;     submitBtn.disabled=true; }
    else                          { warning.textContent="";                                    submitBtn.disabled=false; }
    updateTotal();
  });


  hargaJual.addEventListener('input', function(){
    const harga = num(this.value);
    submitBtn.disabled = (harga < 0 || isNaN(harga));
    updateTotal();
  });
});
</script>
@endsection
