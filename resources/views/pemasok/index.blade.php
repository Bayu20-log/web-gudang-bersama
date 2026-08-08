@extends('layouts.app')
@section('content')

<style>
    body { padding-top: 40px; }
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .btn-outline-dark { border: 1px solid #1e293b; color: #1e293b; transition: 0.3s; background: transparent; }
    .btn-outline-dark:hover { background-color: #1e293b; color: #fff; }

    .container-laporan { max-width: 1300px; margin: auto; padding: 30px 20px; font-family: 'Segoe UI', sans-serif; }
    .header { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }

    form.filter-form { background-color: #ffffff; border: none; border-radius: 12px; padding: 20px 25px; margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .filter-form .form-group { display: flex; flex-direction: column; min-width: 250px; flex-grow: 1; }
    .filter-form label { font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #374151;}
    .filter-form input { padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f8fafc; transition: 0.3s; }
    .filter-form input:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); }
    
    .table-wrapper { width: 100%; overflow-x: auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
    table { width: 100%; border-collapse: collapse; min-width: 1000px;}
    th, td { padding: 16px 20px; text-align: center; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    th { background-color: #1e293b !important; color: #ffffff !important; font-weight: 600; white-space: nowrap; border-bottom: 4px solid #f97316; letter-spacing: 0.5px; }
    tr:hover { background-color: #f8fafc; }

    .btn-action { padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px; white-space: nowrap; border: none; transition: 0.2s; }
    .btn-action:hover { opacity: 0.85; transform: translateY(-2px); }

    @media(max-width: 768px) {
        .header { flex-direction: column; align-items: flex-start; }
        .filter-form { flex-direction: column; }
        .filter-form .form-group, .btn-action-group { width: 100%; }
        .btn-action-group { display: flex; gap: 10px; }
        .btn-action-group button, .btn-action-group a { flex: 1; text-align: center; justify-content: center; }
        
        table { min-width: 100%; }
        table, thead, tbody, th, td, tr { display: block; width: 100%; }
        thead { display: none; }
        tr { margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; background-color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        td { border: none !important; text-align: left; padding: 8px 0 8px 45%; position: relative; }
        td:before { position: absolute; top: 8px; left: 0; width: 40%; white-space: nowrap; font-weight: 600; color: #4b5563; }
        
        td:nth-of-type(1):before { content: "No"; }
        td:nth-of-type(2):before { content: "Pemasok"; }
        td:nth-of-type(3):before { content: "Email"; }
        td:nth-of-type(4):before { content: "Jenis"; }
        td:nth-of-type(5):before { content: "Alamat"; }
        td:nth-of-type(6):before { content: "No. Telepon"; }
        td:nth-of-type(7):before { content: "Nama PIC"; }
        td:nth-of-type(8):before { content: "Bergabung Sejak"; }
        td:nth-of-type(9):before { content: "Diperbarui pada"; }
        td:nth-of-type(10):before { content: "Aksi"; }
        .td-action { justify-content: flex-start; flex-wrap: wrap; gap: 8px;}
    }
</style>

<div class="container-laporan mb-5">
    <div class="header">
        <h4 class="fw-bold" style="color: #1e293b;">Master Data &rsaquo; Daftar Pemasok</h4>
        <a href="{{ route('pemasok.create') }}" class="btn btn-orange fw-bold px-4 py-2 shadow-sm">+ Tambah Pemasok</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('pemasok.index') }}" class="filter-form">
        <div class="form-group">
            <label for="search">Cari Pemasok</label>
            <input type="text" name="search" id="search" placeholder="Ketik nama pemasok, email, PIC, atau jenis..." value="{{ request('search') }}">
        </div>
        <div class="btn-action-group" style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-orange fw-bold px-4">Filter</button>
            <a href="{{ route('pemasok.index') }}" class="btn btn-outline-dark fw-bold px-4" style="display: inline-flex; align-items: center; justify-content: center;">Reset</a>
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pemasok</th>
                    <th>Email</th>
                    <th>Jenis</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Nama PIC</th>
                    <th>Bergabung Sejak</th>
                    <th>Diperbarui pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemasoks as $pemasok)
                    <tr>
                        <td class="fw-medium text-secondary">{{ ($pemasoks->currentPage() - 1) * $pemasoks->perPage() + $loop->iteration }}</td>
                        <td class="fw-bold" style="color: #1e293b;">{{ $pemasok->nama_pemasok }}</td>
                        <td>{{ $pemasok->email ?? '-' }}</td>
                        <td>{{ $pemasok->jenis ?? '-' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($pemasok->alamat ?? '-', 20) }}</td>
                        <td>{{ $pemasok->no_telepon ?? '-' }}</td>
                        <td>{{ $pemasok->nama_pic ?? '-' }}</td>
                        <td>{{ $pemasok->bergabung_sejak ? \Carbon\Carbon::parse($pemasok->bergabung_sejak)->format('d-m-Y') : '-' }}</td>
                        <td>{{ optional($pemasok->updated_at)->format('d-m-Y H:i') }}</td>
                        <td class="td-action">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="{{ route('pemasok.edit', $pemasok->id) }}" class="btn-action text-dark shadow-sm" style="background-color: #facc15;">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <form id="delete-form-{{ $pemasok->id }}" action="{{ route('pemasok.destroy', $pemasok->id) }}" method="POST" style="margin: 0;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-action text-white shadow-sm" style="background-color: #ef4444;" onclick="confirmDelete('{{ $pemasok->id }}')">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center py-5 text-muted fw-medium"><i class="fa-solid fa-folder-open fs-2 mb-2 d-block text-light"></i>Data pemasok belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $pemasoks->links('pagination::bootstrap-5') }}
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Pemasok?',
            text: "Data pemasok ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#1e293b',
            confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection