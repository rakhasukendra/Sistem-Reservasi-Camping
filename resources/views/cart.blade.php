@extends('layouts.app')
@section('title', 'Keranjang Sewa')
@section('content')
<div class="wrap" style="padding-top:100px">
<div class="page-head">
    <p class="eyebrow">Rencana penyewaan</p>
    <h1 class="section-title">Keranjang</h1>
    <p class="muted" style="margin-top:12px;max-width:700px">Atur alat yang ingin disewa, tentukan jumlah unit, lalu lanjutkan ke formulir reservasi.</p>
</div>

@if($cartItems->isEmpty())
    <section style="padding:40px 0">
        <div class="panel empty" style="padding:60px 24px;text-align:center">
            <svg style="width:80px;height:80px;margin:0 auto 20px;opacity:0.3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <p style="font-size:18px;font-weight:600;color:var(--forest-dark);margin:0 0 8px">Keranjang masih kosong</p>
            <p style="margin:0 0 24px;color:var(--muted)">Pilih alat camping dari katalog untuk memulai reservasi</p>
            <a href="{{ route('home') }}#alat" class="button">Lihat katalog perlengkapan</a>
        </div>
    </section>
@else
    <section style="padding:32px 0">
        <div style="display:grid;grid-template-columns:1fr 400px;gap:32px;align-items:start">
            <div>
                <div style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center">
                    <h2 style="font:700 18px Manrope,sans-serif;margin:0;color:var(--forest-dark)">Alat yang dipilih ({{ $cartItems->count() }})</h2>
                    <form method="post" action="{{ route('cart.clear') }}" onsubmit="return confirm('Kosongkan keranjang?')">
                        @csrf
                        <button type="submit" class="text-link" style="font-size:14px">Kosongkan keranjang</button>
                    </form>
                </div>

                <div class="stack">
                    @foreach($cartItems as $cartItem)
                        <div class="panel" style="display:grid;grid-template-columns:1fr auto;gap:20px;align-items:center">
                            <div>
                                <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px">
                                    <span class="pill">{{ $cartItem['item']->category }}</span>
                                    <h3 style="font:700 18px Manrope,sans-serif;margin:0;color:var(--forest-dark)">{{ $cartItem['item']->name }}</h3>
                                </div>
                                <p class="small-note" style="margin:0 0 12px">{{ Str::limit($cartItem['item']->description, 120) }}</p>
                                <p style="font:700 16px Manrope,sans-serif;margin:0;color:var(--forest)">
                                    Rp{{ number_format($cartItem['item']->daily_rate, 0, ',', '.') }}
                                    <span style="font-weight:500;color:var(--muted);font-size:13px">/ hari</span>
                                </p>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:10px;align-items:flex-end">
                                <form method="post" action="{{ route('cart.update', $cartItem['item']) }}" style="display:flex;align-items:center;gap:8px">
                                    @csrf
                                    <label for="qty-{{ $cartItem['item']->id }}" class="small-note">Jumlah</label>
                                    <input type="number" id="qty-{{ $cartItem['item']->id }}" name="quantity" value="{{ $cartItem['quantity'] }}" min="1" max="100" style="width:70px;padding:8px;border:2px solid var(--line);border-radius:6px;text-align:center;font-weight:600">
                                    <button type="submit" class="button secondary small">Update</button>
                                </form>
                                <form method="post" action="{{ route('cart.remove', $cartItem['item']) }}">
                                    @csrf
                                    <button type="submit" class="text-link" style="font-size:13px;color:var(--error)">Hapus</button>
                                </form>
                                <p class="small-note" style="margin:0;font-weight:600">Subtotal: Rp{{ number_format($cartItem['item']->daily_rate * $cartItem['quantity'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel" style="position:sticky;top:24px;background:var(--sand);border:2px solid var(--forest)">
                <h3 style="font:700 18px Manrope,sans-serif;margin:0 0 16px;color:var(--forest-dark)">Ringkasan reservasi</h3>
                
                <div style="padding:16px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                        <span class="small-note">Total alat</span>
                        <span style="font-weight:600">{{ $cartItems->count() }} jenis</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                        <span class="small-note">Total unit</span>
                        <span style="font-weight:600">{{ $cartItems->sum('quantity') }} unit</span>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <span class="small-note">Tarif per hari</span>
                        <span style="font:700 20px Manrope,sans-serif;color:var(--forest-dark)">Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <p class="small-note" style="margin:16px 0;line-height:1.6">Total biaya dihitung berdasarkan durasi sewa yang Anda tentukan di formulir reservasi.</p>

                <a href="{{ route('home') }}#alat" class="button secondary" style="width:100%;margin-bottom:10px">Tambah alat lain</a>
                <a href="{{ route('bookings.create') }}" class="button" style="width:100%">Lanjut ke reservasi</a>
            </div>
        </div>
    </section>
@endif

@endsection
