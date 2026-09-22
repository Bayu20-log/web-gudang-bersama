@extends('layouts.app')
@section('content')
<style>
    body { padding-top: 40px; }
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .btn-outline-dark { border: 1px solid #1e293b; color: #1e293b; transition: 0.3s; background: transparent; }
    .btn-outline-dark:hover { background-color: #1e293b; color: #fff; }
    .container-laporan { max-width: 1200px; margin: auto; padding: 30px 20px; font-family: 'Segoe UI', sans-serif; }
    .header { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }

    form.filter-form { background-color: #ffffff; border: none; border-radius: 12px; padding: 20px 25px; margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .filter-form .form-group { display: flex; flex-direction: column; min-width: 250px; flex-grow: 1; }
    .filter-form label { font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #374151;}
    .filter-form input { padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f8fafc; transition: 0.3s; }
    .filter-form input:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); }

    .table-wrapper { width: 100%; overflow-x: auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 16px 20px; text-align: center; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    th { background-color: #1e293b !important; color: #ffffff !important; font-weight: 600; white-space: nowrap; border-bottom: 4px solid #f97316; letter-spacing: 0.5px; }
    tr:hover { background-color: #f8fafc; }

    .btn-action {
         width: 36px; height: 36px; border-radius: 8px; font-size: 14px; cursor: pointer;
         text-decoration: none; display: inline-flex; align-items: center; justify-content: center;
         border: none; transition: 0.2s;
     }
    .btn-action:hover { opacity: 0.85; transform: translateY(-2px); }
</style>

<div class="container-laporan mb-5">
    <div class="header">
        <h4 class="fw-bold" style="color: #1e293b;">Master Data &rsaquo; Daftar Kategori</h4>
        <a href="{{ route('kategori.create') }}" class="btn btn-orange fw-bold px-4 py-2 shadow-sm">+ Tambah Kategori</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

        {{-- Search bar ringkas --}}
    <div class="search-filter-bar">
        <form method="GET" action="{{ route('kategori.index') }}" class="search-bar-form">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
            <input type="text" name="search" placeholder="Cari nama kategori..." value="{{ request('search') }}" onchange="this.form.submit()">
        </form>
        @if(request('search'))
            <a href="{{ route('kategori.index') }}" class="filter-icon-btn" title="Reset pencarian">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M.146.146a.5.5 0 0 1 .708 0L8 7.293 15.146.146a.5.5 0 1 1 .708.708L8.707 8l7.147 7.146a.5.5 0 0 1-.708.708L8 8.707l-7.146 7.147a.5.5 0 0 1-.708-.708L7.293 8 .146.854a.5.5 0 0 1 0-.708z"/></svg>
            </a>
        @endif
    </div>

    {{-- ================= TAMPILAN DESKTOP (tabel) ================= --}}
    <div class="table-wrapper desktop-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Dibuat pada</th>
                    <th>Diperbarui pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategoris as $index => $kategori)
                    <tr>
                        <td class="fw-medium text-secondary">{{ $kategoris->firstItem() + $index }}</td>
                        <td class="fw-bold" style="color: #1e293b;">{{ $kategori->kategori }}</td>
                        <td>{{ $kategori->deskripsi ?? '-' }}</td>
                        <td>{{ optional($kategori->created_at)->format('d-m-Y H:i') }}</td>
                        <td>{{ optional($kategori->updated_at)->format('d-m-Y H:i') }}</td>
                        <td class="td-action">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn-action text-dark shadow-sm" style="background-color: #facc15;" title="Edit Kategori">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>
                                </a>
                                <form id="delete-form-{{ $kategori->id }}" action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" style="margin: 0;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-action text-white shadow-sm" style="background-color: #ef4444;" title="Hapus Kategori" onclick="confirmDelete('{{ $kategori->id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted fw-medium"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="mb-2" style="color: #cbd5e1;" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/></svg><br>Data kategori belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= TAMPILAN HP (kartu + menu titik tiga) ================= --}}
    <div class="mobile-card-list">
        @forelse($kategoris as $kategori)
            <div class="mobile-card-item">
                <div>
                    <div class="mc-title">{{ $kategori->kategori }}</div>
                    <div class="mc-sub">{{ $kategori->deskripsi ?? 'Tanpa deskripsi' }}</div>
                </div>
                <div class="dropdown">
                    <button class="btn-action-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/></svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end action-dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('kategori.edit', $kategori->id) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#f97316" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg> Edit
                            </a>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item text-danger" onclick="confirmDelete('{{ $kategori->id }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/></svg> Hapus
                            </button>
                        </li>
                    </ul>
                </div>
                <form id="delete-form-{{ $kategori->id }}" action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-none">
                    @csrf @method('DELETE')
                </form>
            </div>
        @empty
            <div class="text-center py-5 text-muted fw-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="mb-2" style="color: #cbd5e1;" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/></svg><br>
                Data kategori belum tersedia.
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $kategoris->links('pagination::bootstrap-5') }}
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Kategori?',
            text: "Data kategori ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#1e293b',
            confirmButtonText: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/></svg> Ya, hapus!',
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
