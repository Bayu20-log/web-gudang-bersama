<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $namaBarang          Nama barang, misal "Charcoal Powder 16oz"
     * @param  string  $kodeBarang          Kode/ID barang
     * @param  string  $level               'info' | 'warning' | 'critical'
     * @param  string  $type                'initial' | 'escalation' | 'resolution' | 'reminder'
     * @param  string  $title               Judul notifikasi (sudah ada di stock_notifications)
     * @param  string  $message             Isi pesan (sudah ada di stock_notifications)
     * @param  float   $stokAktual          Stok saat ini
     * @param  float|null  $lowThreshold    Threshold Rendah (opsional, untuk konteks tambahan)
     * @param  float|null  $criticalThreshold  Threshold Kritis (opsional, untuk konteks tambahan)
     */
    public function __construct(
        public string $namaBarang,
        public string $kodeBarang,
        public string $level,
        public string $type,
        public string $title,
        public string $pesanNotifikasi,
        public float $stokAktual,
        public ?float $lowThreshold = null,
        public ?float $criticalThreshold = null,
    ) {}

    /**
     * Amplop email: subjeknya pakai title notifikasi yang sudah dibuat
     * di generateNotification() / checkAndSendReminders().
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    /**
     * Isi email pakai view Blade yang kita buat di langkah berikutnya.
     * Semua public property di atas otomatis tersedia sebagai variabel
     * di dalam view (karena fitur "public property" Mailable Laravel).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.stock-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}