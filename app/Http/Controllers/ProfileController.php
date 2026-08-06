<?php


namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;




class ProfileController extends Controller
{
    // Tampilkan halaman edit profile
   
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }


    public function edit(User $user)
    {
        return view('profile.update', compact('user'));
    }


    // Simpan perubahan profile user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'phone'     => 'required|string|max:20',
            'position'  => 'required|string|max:255',
            'note'      => 'nullable|string',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',


            // Tambahan validasi password
            'current_password' => 'nullable|string',
            'new_password'     => 'nullable|string|min:6',
        ]);


        // jika upload foto baru
        if ($request->hasFile('photo')) {
            // Hapus foto lama (kecuali default)
            if ($user->photo && $user->photo !== 'default.jpg') {
                Storage::delete($user->photo);
            }


            // Simpan foto baru
            $validated['photo'] = $request->file('photo')->store('user_photos', 'public');
        }


        // Jika user mengisi salah satu kolom password
        if ($request->filled('current_password') || $request->filled('new_password')) {
            // Cek apakah password lama cocok
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Password lama salah.');
            }


            // Simpan password baru
            $user->password = Hash::make($request->new_password);
        }


        // Update user
        $user->update($validated);


        return back()->with('success', 'Profile berhasil diperbarui!');
    }
}
