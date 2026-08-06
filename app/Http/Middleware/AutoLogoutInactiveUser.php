<?php


namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class AutoLogoutInactiveUser
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();


            // Cek apakah last_login ada
            if ($user->last_login) {
                $lastLogin = Carbon::parse($user->last_login);
                $now = Carbon::now();


                $diffInDays = $lastLogin->diffInDays($now);


                // Tambahkan pengecualian: kalau user barusan diaktifkan (misalnya 60 menit terakhir), jangan logout
                $wasRecentlyActivated = $user->is_active && $user->updated_at->gt(now()->subMinutes(60));


                // Jika sudah lebih dari 7 hari tidak login dan TIDAK baru diaktifkan manual
                if ($diffInDays >= 7 && !$wasRecentlyActivated) {
                    Auth::logout();


                    Log::info("User {$user->name} dinonaktifkan otomatis karena tidak login sejak {$user->last_login}");


                    return redirect()->route('login')->withErrors([
                        'email' => 'Akun Anda telah logout otomatis karena tidak aktif selama lebih dari 7 hari.'
                    ]);
                }
            }
        }


        return $next($request);
    }
}
