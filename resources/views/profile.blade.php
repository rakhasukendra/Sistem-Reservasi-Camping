@extends('layouts.app')
@section('title', 'Profil')
@section('content')
<div class="wrap" style="padding-top:100px;padding-bottom:60px;max-width:{{ auth()->user()->role === 'admin' ? '1100px' : '600px' }}">
<div class="page-head"><p class="eyebrow">Akun penyewa</p><h1 class="section-title">Profil Saya</h1><p class="muted" style="margin-top:12px">Perbarui data kontak dan informasi akun Anda.</p></div>

<section class="panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h2 style="font:700 20px Manrope,sans-serif;margin:0">Data pribadi</h2>
        <button type="button" onclick="toggleProfileForm()" class="button secondary small">Edit profil</button>
    </div>
    
    <div id="profile-info">
        <div style="margin-bottom:12px">
            <p class="small-note" style="margin:0 0 4px">Nama lengkap</p>
            <p style="font-weight:600;margin:0">{{ auth()->user()->name }}</p>
        </div>
        <div style="margin-bottom:12px">
            <p class="small-note" style="margin:0 0 4px">Email</p>
            <p style="font-weight:600;margin:0">{{ auth()->user()->email }}</p>
        </div>
        <div style="margin-bottom:12px">
            <p class="small-note" style="margin:0 0 4px">Nomor telepon</p>
            <p style="font-weight:600;margin:0">{{ auth()->user()->phone ?: '-' }}</p>
        </div>
    </div>
    
    <form id="profile-form" class="stack" method="post" action="{{ route('profile.update') }}" style="display:none">
        @csrf 
        @method('PUT')
        <div class="field">
            <label for="name">Nama lengkap</label>
            <input id="name" name="name" value="{{ old('name',auth()->user()->name) }}" required>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email',auth()->user()->email) }}" required>
        </div>
        <div class="field">
            <label for="phone">Nomor telepon</label>
            <input id="phone" name="phone" value="{{ old('phone',auth()->user()->phone) }}">
        </div>
        <div class="field">
            <label for="password">Kata sandi baru (opsional)</label>
            <input id="password" name="password" type="password" autocomplete="new-password">
        </div>
        <div class="field">
            <label for="password_confirmation">Ulangi kata sandi baru</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
        </div>
        <div style="display:flex;gap:10px">
            <button class="button" type="submit">Simpan profil</button>
            <button type="button" onclick="toggleProfileForm()" class="button secondary">Batal</button>
        </div>
    </form>

    <details style="margin-top:24px;padding-top:24px;border-top:1px solid var(--line)">
        <summary style="cursor:pointer;font-size:14px;color:#dc2626;font-weight:600">Hapus akun permanen</summary>
        <div style="margin-top:16px;padding:16px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px">
            <p style="font-size:13px;color:#991b1b;margin:0 0 12px">⚠️ Peringatan: Tindakan ini tidak dapat dibatalkan. Semua data akun Anda akan dihapus permanen.</p>
            <form method="post" action="{{ route('deleteAccount') }}" onsubmit="return confirm('Yakin ingin menghapus akun? Semua data akan hilang permanen dan Anda tidak dapat login kembali.')">
                @csrf
                @method('DELETE')
                <div class="field" style="margin-bottom:12px">
                    <label for="delete-password" style="font-size:13px">Konfirmasi password untuk melanjutkan</label>
                    <input id="delete-password" name="password" type="password" required placeholder="Masukkan password Anda">
                </div>
                <button type="submit" class="button small" style="background:#dc2626;border-color:#dc2626">Hapus Akun Saya</button>
            </form>
        </div>
    </details>
</section>

@if(auth()->user()->role==='admin')
<section style="padding:48px 0;border-top:2px solid var(--line)">
    <div class="page-head">
        <p class="eyebrow">Manajemen sistem</p>
        <h2 class="section-title">Akun pengguna & audit</h2>
        <p class="muted" style="margin-top:12px;max-width:600px">Kelola peran akun staf dan admin. Tampilkan riwayat semua tindakan yang dicatat di sistem.</p>
    </div>

    <div style="margin:36px 0;padding:24px;background:var(--sand);border-radius:10px;border-left:4px solid var(--forest)">
        <p style="font-size:14px;font-weight:600;color:var(--forest-dark);margin:0">Status: <span style="color:var(--muted);font-weight:500">{{ $users->count() }} akun terdaftar</span></p>
    </div>

    <div style="margin:32px 0">
        <details style="background:white;border:2px solid var(--forest);border-radius:10px;overflow:hidden">
            <summary style="cursor:pointer;padding:18px 24px;font-weight:600;display:flex;align-items:center;gap:12px;background:white;transition:background .2s;user-select:none">
                <span style="font-size:22px;line-height:1;color:var(--forest);font-weight:700;display:flex;align-items:center;justify-content:center;width:28px;height:28px;background:var(--sand);border-radius:6px">+</span>
                <span style="color:var(--forest-dark);font-size:16px">Tambah pengguna baru</span>
            </summary>
            <form class="form-grid panel" method="post" action="{{ route('admin.users.store') }}" style="margin:0;border:none;box-shadow:none;border-top:2px solid var(--line);border-radius:0">
                @csrf
                <div class="field">
                    <label for="new-user-name">Nama lengkap</label>
                    <input id="new-user-name" name="name" required maxlength="120" placeholder="Contoh: Budi Santoso">
                </div>
                <div class="field">
                    <label for="new-user-email">Email</label>
                    <input id="new-user-email" name="email" type="email" required maxlength="120" placeholder="budi@example.com">
                </div>
                <div class="field">
                    <label for="new-user-phone">Nomor telepon</label>
                    <input id="new-user-phone" name="phone" maxlength="20" placeholder="+62812345678">
                </div>
                <div class="field">
                    <label for="new-user-role">Peran</label>
                    <select id="new-user-role" name="role" required>
                        <option value="">Pilih peran</option>
                        <option value="user">Pengguna reguler</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="field">
                    <label for="new-user-password">Password (minimum 8 karakter)</label>
                    <input id="new-user-password" name="password" type="password" required minlength="8">
                </div>
                <div class="field">
                    <label for="new-user-password-confirm">Konfirmasi password</label>
                    <input id="new-user-password-confirm" name="password_confirmation" type="password" required minlength="8">
                </div>
                <button class="button" type="submit" style="grid-column:1/-1">Buat akun baru</button>
            </form>
        </details>
    </div>

    <h3 style="font:700 18px Manrope,sans-serif;margin:40px 0 20px;color:var(--forest-dark)">Daftar akun</h3>
    @if($users->isEmpty())
        <div class="panel empty" style="padding:40px 24px">
            <p style="margin:0;color:var(--muted)">Belum ada akun pengguna.</p>
        </div>
    @else
        <div class="stack">
            @foreach($users as $user)
                <div class="panel" style="display:grid;grid-template-columns:1fr auto;gap:24px;align-items:center">
                    <div>
                        <p style="font:700 16px Manrope,sans-serif;margin:0 0 4px;color:var(--forest-dark)">{{ $user->name }}</p>
                        <p class="small-note" style="margin:0">{{ $user->email }}</p>
                        <p class="small-note" style="margin:6px 0 0">Peran saat ini: <span style="font-weight:600;color:var(--forest)">{{ ucfirst($user->role) }}</span></p>
                    </div>
                    @if($user->is(auth()->user()))
                        <div style="padding:8px 16px;background:var(--sand);border-radius:6px">
                            <p style="font-size:13px;color:var(--muted);margin:0">Akun Anda saat ini</p>
                        </div>
                    @else
                        <div style="display:flex;gap:10px;align-items:flex-end">
                            <form method="post" action="{{ route('admin.users.update',$user) }}" style="display:flex;gap:10px;align-items:flex-end">
                                @csrf @method('PUT')
                                <div class="field" style="gap:3px">
                                    <label for="role-{{ $user->id }}" style="font-size:12px">Ubah peran</label>
                                    <select id="role-{{ $user->id }}" name="role" style="padding:8px 12px;font-size:14px;height:42px">
                                        <option value="user" @selected($user->role==='user')>User</option>
                                        <option value="staff" @selected($user->role==='staff')>Staff</option>
                                        <option value="admin" @selected($user->role==='admin')>Admin</option>
                                    </select>
                                </div>
                                <button class="button secondary small" type="submit">Simpan</button>
                            </form>
                            <form method="post" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Hapus akun {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="button small" style="background:#dc2626;border-color:#dc2626;height:42px">Hapus</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>
@endif


</div>

@push('scripts')
<script>
function toggleProfileForm() {
    const info = document.getElementById('profile-info');
    const form = document.getElementById('profile-form');
    if (form.style.display === 'none') {
        info.style.display = 'none';
        form.style.display = 'block';
    } else {
        info.style.display = 'block';
        form.style.display = 'none';
    }
}
</script>
@endpush
@endsection