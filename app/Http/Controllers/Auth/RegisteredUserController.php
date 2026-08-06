<?php


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;


class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman register.
     */
    public function create(): View
    {
        return view('auth.register');
    }


    /**
     * Proses submit register.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi (tanpa mewajibkan position, status opsional)
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => 'required|string',
            'role'     => 'required|string',
            // 'position' tidak wajib di form
            // 'status' tidak wajib di form, nanti kita set default di bawah
            'photo'    => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'note'     => 'nullable|string',
            'password' => 'required|string|confirmed|min:8',
        ]);


        // Simpan foto jika ada
        $photoPath = $request->file('photo')
            ? $request->file('photo')->store('photos', 'public')
            : null;


        // ===== Default otomatis =====
        // Position: dari role (fallback 'viewer')
        $role      = strtolower($validated['role']);
        $position  = strtolower($request->input('position', $role));


        // Status: default 'active' kalau tidak diisi
        // (ganti ke 'pending' / 'inactive' jika kebijakanmu berbeda)
        $status    = strtolower($request->input('status', 'active'));


        // Buat user
        $user = User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'role'     => $role,
            'position' => $position,   // <-- tidak akan NULL
            'status'   => $status,     // <-- tidak akan NULL
            'photo'    => $photoPath,
            'note'     => $validated['note'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);


        event(new Registered($user));
        Auth::login($user);


        return redirect(RouteServiceProvider::redirectByRole());
    }
}
