<?php
namespace App\Http\Controllers;


use App\Models\Notification;
use Illuminate\Http\Request;




class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id()) // 🔹 Tambah filter
            ->latest()
            ->get();


        return view('notifications.index', compact('notifications'));
    }


    public function markRead()
    {
        Notification::where('user_id', auth()->id()) // 🔹 Tambah filter
            ->where('is_read', false)
            ->update(['is_read' => true]);


        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }


    public function markSingleRead($id)
    {
        $notif = Notification::where('user_id', auth()->id()) // 🔹 Tambah filter
            ->findOrFail($id);


        $notif->update(['is_read' => true]);


        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
    }


}
