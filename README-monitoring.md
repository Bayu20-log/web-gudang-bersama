# Modul Smart Monitoring & Notifikasi Stok Adaptif

Modul pengembangan untuk Tugas Akhir — **Pengembangan Sistem Monitoring dan Notifikasi Stok Adaptif Berbasis Threshold dan Early Warning untuk Respons UMKM**, dibangun di atas sistem **RAKSAKTI** (web-gudang-bersama).

- **Developer:** Ratih (NIM 20231064) — Bisnis Digital, Institut Teknologi Kalimantan
- **Branch:** `feature/ratih-monitoring`
- **Repository:** [Bayu20-log/web-gudang-bersama](https://github.com/Bayu20-log/web-gudang-bersama)

---

## 📦 Modul yang Dikerjakan

Modul **Smart Monitoring & Notifikasi Stok Adaptif**, mencakup:
- Adaptive Threshold Engine (perhitungan ADC, ROP, dan batas kritis stok)
- Klasifikasi status stok otomatis (Aman / Rendah / Kritis / Habis)
- Stock Status Monitoring Engine (pemantauan otomatis via scheduler)
- Sistem notifikasi stok kritis & early warning (multi-channel delivery)

---

## 🖥 Persyaratan Perangkat Lunak

| Komponen | Versi |
|---|---|
| PHP | 8.3.32 |
| Laravel | 9.52.20 |
| Composer | 2.10.1 |
| Node.js | v25.8.2 |
| NPM | 11.11.1 |
| Database | MySQL (via DBngin) |
| Git | 2.50.1 |
| Environment | Laravel Herd (PHP diisolasi ke 8.3 karena project tidak kompatibel dengan PHP 8.4) |

> Catatan: seluruh perintah PHP/Composer/Artisan wajib menggunakan awalan `herd` (contoh: `herd php artisan migrate`), karena versi PHP diisolasi lewat Laravel Herd.

---

## 📥 Cara Clone Project

```bash
git clone https://github.com/Bayu20-log/web-gudang-bersama.git
cd web-gudang-bersama
git checkout feature/ratih-monitoring
```

---

## ⚙️ Cara Instalasi

```bash
# Isolasi versi PHP ke 8.3 (khusus pengguna Laravel Herd)
herd isolate 8.3

# Install dependensi PHP
herd composer install

# Install dependensi Node.js
npm install
```

---

## 🔐 Cara Konfigurasi `.env`

```bash
cp .env.example .env
herd php artisan key:generate
```

Kemudian sesuaikan koneksi database lokal di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=web_gudang_bersama
DB_USERNAME=root
DB_PASSWORD=
```

> ⚠️ File `.env`, kredensial database, API key, dan token akses **tidak boleh** diunggah ke repository. Sudah dicakup oleh `.gitignore`.

---

## 🗄 Cara Menjalankan Migration

```bash
herd php artisan migrate
```

Migration akan membuat 4 tabel baru khusus modul ini:

| Tabel | Fungsi |
|---|---|
| `stock_thresholds` | Menyimpan batas stok (Rendah, Kritis) per barang |
| `stock_status_logs` | Mencatat riwayat perubahan status stok |
| `stock_notifications` | Menyimpan notifikasi yang akan dikirim ke pengguna |
| `notification_deliveries` | Mencatat waktu kirim & baca notifikasi |

> Tabel `stock_notifications` dibuat terpisah dari tabel `notifications` bawaan sistem agar tidak mengganggu modul lain.
> Jangan menjalankan `migrate:fresh` pada database bersama tanpa izin.

---

## ▶️ Cara Menjalankan Project

```bash
herd php artisan serve
```

Akses melalui browser di: **http://127.0.0.1:8000**

> Domain `.test` tidak digunakan pada setup ini karena tidak dikenali oleh Herd di environment ini.

Halaman skeleton modul monitoring dapat diakses di:
**http://127.0.0.1:8000/stock-notifications**

---

## 🌿 Nama Branch

```
feature/ratih-monitoring
```

Dibuat dari branch `develop`, mengikuti struktur branch project:
```
main
develop
feature/bayu-ux-redesign
feature/rania-dashboard
feature/ratih-monitoring
feature/rafly-forecasting
```

---

## 📚 Dependensi Tambahan

Belum ada dependensi/library tambahan di luar bawaan Laravel 9 pada tahap ini (skeleton). Akan diperbarui jika `AdaptiveThresholdService` atau fitur notifikasi memerlukan package tambahan (misalnya untuk pengiriman notifikasi multi-channel).

---

## 🐛 Known Issues

| Isu | Status |
|---|---|
| Perhitungan threshold (ADC/ROP) belum diimplementasikan — baru kerangka migration | Direncanakan minggu berikutnya via `AdaptiveThresholdService` |
| `StockNotificationController` baru kerangka awal, belum ada logika penuh | Menunggu implementasi service |
| Command `stock:evaluate-thresholds` baru skeleton alarm, belum ada logika penghitungan | Direncanakan minggu berikutnya |
| Relasi ke tabel `items` harus ditulis manual (`unsignedBigInteger()` + `foreign()->references('kode_barang')`) karena primary key tabel `items` bukan `id` standar Laravel | Sudah ditangani, bukan bug — catatan teknis untuk developer lain |

---

## 🛠 Riwayat Kendala Teknis (Ringkas)

| Kendala | Solusi |
|---|---|
| Composer gagal install (PHP 8.4 vs 8.3) | Gunakan `herd isolate 8.3` |
| Domain `.test` tidak terbuka | Gunakan `php artisan serve` via `127.0.0.1:8000` |
| Migration gagal (kolom `id` tidak ditemukan di tabel `items`) | Tulis relasi manual dengan `unsignedBigInteger()` + `foreign()->references('kode_barang')` |
| Tabel `notifications` existing terlalu sederhana untuk kebutuhan stok | Dibuat tabel baru `stock_notifications` |
| Scheduler tidak terbaca (`Class Schedule not found`) | Daftarkan jadwal di `app/Console/Kernel.php` sesuai pola Laravel 9 (bukan `routes/console.php`) |

---

## 📁 Rencana Pengembangan Selanjutnya

- Membuat Model untuk 4 tabel baru
- Membuat `AdaptiveThresholdService` (logika ADC, ROP, klasifikasi status stok)
- Mengisi logika di dalam command scheduler
- Mengisi logika dasar controller dan halaman notifikasi
