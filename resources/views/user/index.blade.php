@extends('layouts.app')

@section('content')
<style>
    .container-laporan {
        max-width: 1200px;
        margin: auto;
        padding: 30px 20px;
        font-family: 'Segoe UI', sans-serif;
    }

    .header {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    form.filter-form {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 25px;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-end;
    }

    .filter-form .form-group {
        display: flex;
        flex-direction: column;
        min-width: 160px;
    }

    .filter-form label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .filter-form input,
    .filter-form select {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .filter-form button {
        padding: 8px 16px;
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .filter-form button:hover {
        background-color: #2563eb;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    th, td {
        padding: 12px;
        background-color: white;
        text-align: center;
    }

    th {
        background-color: #f3f4f6;
        font-weight: 600;
        border-bottom: 1px solid #ddd;
    }

    tr {
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
    }

     @media(max-width: 768px) {
        .filter-form {
            flex-direction: column;
            gap: 12px;
        }


        .filter-form .form-group {
            width: 100%;
        }


        .filter-form .form-group > div {
            flex-wrap: wrap;
            justify-content: flex-start;
        }


        .filter-form .form-group > div a.btn.btn-primary {
            margin-left: 0 !important;
            width: 100%;
            margin-top: 8px;
        }


        .header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
    }

        /* Mobile Responsive */
   @media(max-width: 768px) {
    /* Header & Filter */
    .header { 
        flex-direction: column; 
        align-items: flex-start; 
    }
    .filter-form { 
        flex-direction: column; 
    }
    .filter-form .form-group { 
        width: 100%; 
    }
    .filter-form button, .btn-secondary { 
        width: 100%; 
    }

    /* Table jadi Card View */
    table, thead, tbody, th, td, tr {
        display: block;
        width: 100%;
    }
    thead { 
        display: none; 
    }

    tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 12px;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }

    td {
        border: none !important;
        text-align: left;
        padding: 8px 12px 8px 45%; /* ✅ kasih ruang lebih */
        position: relative;
        word-wrap: break-word;     /* ✅ biar isi text turun ke bawah kalau panjang */
        white-space: normal;       /* ✅ teks bisa wrap */
    }

td:before {
    position: absolute;
    top: 8px;
    left: 12px;
    width: 40%;                /* ✅ label lebih lebar */
    font-weight: bold;
    color: #333;
    white-space: normal;       /* ✅ biar label juga bisa turun ke bawah */
}


    /* Label Kolom */
    td:nth-of-type(1):before { content: "Foto"; }
    td:nth-of-type(2):before { content: "Nama"; }
    td:nth-of-type(3):before { content: "Username"; }
    td:nth-of-type(4):before { content: "Email"; }
    td:nth-of-type(5):before { content: "Role"; }
    td:nth-of-type(6):before { content: "Posisi"; }
    td:nth-of-type(7):before { content: "Telepon"; }
    td:nth-of-type(8):before { content: "Created At"; }
    td:nth-of-type(9):before { content: "Last login"; }
    td:nth-of-type(10):before { content: "Status"; }
    td:nth-of-type(11):before { content: "Aksi"; }

    /* Tombol Aksi di Card */
    .td-action {
        flex-direction: column;
        align-items: stretch;
        gap: 5px;
    }
    .td-action a, .td-action button {
        width: 100%;
    }}
</style>

<div class="container-laporan">
    <div class="header">
        <h2>Daftar User</h2>
    </div>

    <!-- Filter & Tambah -->
    <form method="GET" action="{{ route('user.index') }}" class="filter-form">
        <div class="form-group">
            <label>Cari:</label>
            <input type="text" name="search" placeholder="Nama/username/email/role" value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <label>Status:</label>
            <select name="is_active">
                <option value="" {{ request('is_active') === null || request('is_active') === '' ? 'selected' : '' }}>-- Semua Status --</option>
                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        <div class="form-group" style="flex-grow: 1;">
            <label>&nbsp;</label>
            <div style="display: flex; gap: 10px;">
                <button type="submit">Cari</button>
                <a href="{{ route('user.index') }}" class="btn btn-outline-secondary" style="padding: 8px 16px; border: 1px solid #ccc; border-radius: 6px;">Reset</a>
                <a href="{{ route('user.create') }}" class="btn btn-primary" style="margin-left: auto; padding: 8px 16px; border-radius: 6px;">+ Tambah User</a>
            </div>
        </div>
    </form>

    <!-- Alert -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel -->
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Posisi</th>
                    <th>Telepon</th>
                    <th>Created at</th>
                    <th>Last login</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            @if ($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto {{ $user->name }}" class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                            @else
                                <span class="text-muted fst-italic">(Tidak ada foto)</span>
                            @endif
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="text-capitalize">{{ $user->role }}</td>
                        <td>{{ $user->position ?? '-' }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>{{ $user->last_login }}</td>
                        <td>
                            @if ($user->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('user.toggleStatus', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengubah status user ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-warning' : 'btn-info' }}">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Navigasi Halaman -->
    <div class="mt-3">
        {{ $users->appends(request()->query())->links() }}
    </div>

    @if ($users->total() > 0)
        <p class="text-muted">{{ $users->total() }} hasil ditemukan. Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}.</p>
    @else
        <p class="text-danger">Tidak ada hasil yang ditemukan.</p>
    @endif
</div>
@endsection
