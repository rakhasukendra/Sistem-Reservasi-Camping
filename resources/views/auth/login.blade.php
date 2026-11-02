@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="wrap" style="max-width:480px;padding-top:100px">
    <div class="page-head"><p class="eyebrow">Akun penyewa dan petugas</p><h1 class="section-title">Masuk ke Rimba Sewa</h1><p class="muted">Gunakan akun yang sudah terdaftar untuk mengelola reservasi.</p></div>
    <form class="panel stack" method="post" action="{{ route('login.store') }}">@csrf
        <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"></div>
        <div class="field">
            <label for="password">Kata sandi</label>
            <div style="position:relative">
                <input id="password" name="password" type="password" required autocomplete="current-password" style="width:100%;padding-right:45px">
                <button type="button" onclick="togglePassword()" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:8px;color:var(--muted)">
                    <svg id="eye-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>
        <label style="display:flex;align-items:center;gap:8px;margin-bottom:8px"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        <button class="button" type="submit">Masuk</button>
        <p class="small-note">Belum memiliki akun? <a href="{{ route('register') }}" style="color:#0066cc;font-weight:600">Daftar sebagai penyewa</a></p>
    </form>
</div>

@push('head')
<style>
.navlinks .button{color:#fff!important;background:var(--forest-dark)}
</style>
@endpush

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
</script>
@endpush
@endsection