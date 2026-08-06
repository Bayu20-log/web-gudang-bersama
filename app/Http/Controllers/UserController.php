<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    public function index(Request $request)
    {
        // Log sementara untuk memastikan data masuk
        Log::info('Query Params', $request->all());

        $query = User::query();

        // Cek apakah ada pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('username', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        // Cek apakah ada filter status
        if ($request->filled('is_active')) {
            $isActive = $request->input('is_active');
            if ($isActive === '0' || $isActive === '1') {
                $query->where('is_active', (int)$isActive);
            }
        }

        // Ambil data
        $users = $query->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->appends($request->query());

        // DEBUG LANGSUNG
        Log::info('Final SQL', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:superadmin,gudang,viewer',
            'position' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'status' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'note' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('user_photos', 'public');
        }

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'] ?? null,
            'photo' => $photoPath,
            'note' => $validated['note'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:superadmin,gudang,viewer',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'photo' => 'nullable|image|max:2048',
        ]);


        // Pastikan is_active tetap dipertahankan
        $validated['is_active'] = $user->is_active;


        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('user_photos', 'public');
            $user->photo = $photoPath;
        }


        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }


        $user->fill(array_filter($validated, fn($key) => $key !== 'password' && $key !== 'photo', ARRAY_FILTER_USE_KEY));
        $user->save();


        return redirect()->route('user.index')->with('success', 'Data user berhasil diperbarui.');
    }


    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;


        // Jika user diaktifkan kembali, update juga last_login supaya tidak auto logout
        if ($user->is_active) {
            $user->last_login = now(); // ✅ "reset umur akun"
        }


        $user->save();


        return back()->with('success', 'Status user berhasil diperbarui.');
    }

      public function dashboardSuperadmin()
    {
        // Total user
        $totalUsers = \App\Models\User::count();


        // Jumlah user per role
        $roles = ['superadmin', 'gudang', 'viewer'];
        $usersPerRole = [];
        foreach ($roles as $role) {
            $usersPerRole[$role] = \App\Models\User::where('role', $role)->count();
        }


        // Jumlah user aktif dan nonaktif per role
        $statusPerRole = [];
        foreach ($roles as $role) {
            $statusPerRole[$role]['aktif'] = \App\Models\User::where('role', $role)->where('is_active', true)->count();
            $statusPerRole[$role]['nonaktif'] = \App\Models\User::where('role', $role)->where('is_active', false)->count();
        }


        return view('dashboard.superadmin', compact('totalUsers', 'usersPerRole', 'statusPerRole'));



}}