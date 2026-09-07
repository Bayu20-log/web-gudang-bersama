<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\AdaptiveThresholdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockThresholdController extends Controller
{
    /**
     * Menyimpan konfigurasi manual (Lead Time, Safety Stock, Waktu Respons)
     * untuk satu item, lalu langsung memicu perhitungan ulang threshold
     * (ADC, Threshold Rendah, Threshold Kritis) memakai nilai baru tersebut.
     *
     * Sengaja dipisah dari ItemController@update milik Bayu supaya:
     * - perubahan pada form Info Barang tidak menyenggol logic threshold ini
     * - kalau salah satu form gagal validasi, form yang lain tidak ikut terdampak
     */
    public function update(Request $request, Item $item, AdaptiveThresholdService $thresholdService)
    {
        // Pastikan user hanya bisa mengonfigurasi item miliknya sendiri,
        // konsisten dengan pengecekan yang sama di ItemController.
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $rules = [
            'lead_time_days'     => 'required|integer|min:1|max:365',
            'safety_stock_days'  => 'required|integer|min:0|max:365',
            'response_time_days' => 'required|integer|min:1|max:365',
        ];

        $messages = [
            'lead_time_days.required'     => 'Lead Time wajib diisi.',
            'lead_time_days.min'          => 'Lead Time minimal 1 hari.',
            'safety_stock_days.required'  => 'Safety Stock wajib diisi.',
            'response_time_days.required' => 'Waktu Respons wajib diisi.',
            'response_time_days.min'      => 'Waktu Respons minimal 1 hari.',
        ];

        $validated = $request->validate($rules, $messages);

        // Simpan hanya kolom konfigurasi manual. Kolom hasil hitungan (adc,
        // low_threshold, critical_threshold, calculated_at) SENGAJA tidak
        // disentuh di sini -- updateOrInsert() akan membiarkan kolom lain
        // apa adanya kalau baris sudah ada, sesuai perilaku Laravel.
        DB::table('stock_thresholds')->updateOrInsert(
            ['item_id' => $item->kode_barang],
            [
                'lead_time_days'     => $validated['lead_time_days'],
                'safety_stock_days'  => $validated['safety_stock_days'],
                'response_time_days' => $validated['response_time_days'],
            ]
        );

        // Langsung hitung ulang threshold memakai konfigurasi baru, supaya
        // admin tidak perlu menunggu scheduler berikutnya untuk melihat efeknya.
        $thresholdService->calculateAndSaveThreshold($item->kode_barang);

        return redirect()
            ->to(route('item.edit', $item->kode_barang) . '#konfigurasi-threshold')
            ->with('success_threshold', 'Konfigurasi threshold berhasil diperbarui.');
    }
}