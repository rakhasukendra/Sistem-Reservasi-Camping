@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="wrap" style="max-width:540px;padding-top:100px">
    <div class="page-head"><p class="eyebrow">Mulai dari akun penyewa</p><h1 class="section-title">Buat akun</h1><p class="muted">Isi data kontak agar staf dapat mengonfirmasi reservasi.</p></div>
    <form class="panel stack" method="post" action="{{ route('register.store') }}">@csrf
        <div class="field"><label for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name"></div>
        <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"></div>
        <div class="field"><label for="phone">Nomor telepon</label><input id="phone" name="phone" value="{{ old('phone') }}" required maxlength="40" autocomplete="tel"></div>
        <div class="field"><label for="password">Kata sandi, minimal 8 karakter</label><input id="password" name="password" type="password" required autocomplete="new-password"></div>
        <div class="field"><label for="password_confirmation">Ulangi kata sandi</label><input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"></div>
        <button class="button" type="submit">Buat akun penyewa</button>
        <p class="small-note">Sudah punya akun? <a class="text-link" href="{{ route('login') }}">Masuk di sini</a></p>
    </form>
</div>
@endsection