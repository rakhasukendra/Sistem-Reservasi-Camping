@extends('layouts.app')
@section('title', 'Keranjang Sewa')
@section('content')
<div class="wrap cart-page" style="padding-top:100px">
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
        <div class="cart-layout">
            <div>
                <div class="cart-list-head">
                    <h2 class="cart-list-title">Alat yang dipilih ({{ $cartItems->count() }})</h2>
                    <form method="post" action="{{ route('cart.clear') }}" onsubmit="return confirm('Kosongkan keranjang?')">
                        @csrf
                        <button type="submit" class="text-link" style="font-size:14px">Kosongkan keranjang</button>
                    </form>
                </div>

                <div class="stack">
                    @foreach($cartItems as $cartItem)
                        <div class="panel cart-item">
                            <div class="cart-item-body">
                                <div class="cart-item-head">
                                    <span class="pill">{{ $cartItem['item']->category }}</span>
                                    <h3 class="cart-item-title">{{ $cartItem['item']->name }}</h3>
                                </div>
                                <p class="small-note" style="margin:0 0 12px">{{ Str::limit($cartItem['item']->description, 120) }}</p>
                                <p class="cart-item-price">
                                    Rp{{ number_format($cartItem['item']->daily_rate, 0, ',', '.') }}
                                    <span style="font-weight:500;color:var(--muted);font-size:13px">/ hari</span>
                                </p>
                            </div>
                            <div class="cart-item-actions">
                                <form method="post" action="{{ route('cart.update', $cartItem['item']) }}" class="cart-qty-form">
                                    @csrf
                                    <label for="qty-{{ $cartItem['item']->id }}" class="small-note">Jumlah</label>
                                    <input type="number" id="qty-{{ $cartItem['item']->id }}" name="quantity" value="{{ $cartItem['quantity'] }}" min="1" max="100" class="cart-qty-input">
                                    <button type="submit" class="button secondary small">Update</button>
                                </form>
                                <form method="post" action="{{ route('cart.remove', $cartItem['item']) }}">
                                    @csrf
                                    <button type="submit" class="text-link" style="font-size:13px;color:var(--error)">Hapus</button>
                                </form>
                                <p class="small-note cart-item-subtotal">Subtotal: Rp{{ number_format($cartItem['item']->daily_rate * $cartItem['quantity'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel cart-summary">
                <h3 class="cart-summary-title">Ringkasan reservasi</h3>

                <div class="cart-summary-body">
                    <div class="cart-summary-row">
                        <span class="small-note">Total alat</span>
                        <span style="font-weight:600">{{ $cartItems->count() }} jenis</span>
                    </div>
                    <div class="cart-summary-row">
                        <span class="small-note">Total unit</span>
                        <span style="font-weight:600">{{ $cartItems->sum('quantity') }} unit</span>
                    </div>
                    <div class="cart-summary-row">
                        <span class="small-note">Tarif per hari</span>
                        <span class="cart-summary-total">Rp{{ number_format($total, 0, ',', '.') }}</span>
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

@push('head')
<style>
    .cart-layout{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:32px;align-items:start}
    .cart-list-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px}
    .cart-list-head form{margin:0}
    .cart-list-title{font:700 18px Manrope,sans-serif;margin:0;color:var(--forest-dark)}

    .cart-item{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:20px;align-items:center}
    .cart-item-body{min-width:0}
    .cart-item-head{display:flex;align-items:center;flex-wrap:wrap;gap:8px 12px;margin-bottom:8px}
    .cart-item-title{font:700 18px Manrope,sans-serif;margin:0;color:var(--forest-dark);overflow-wrap:anywhere}
    .cart-item-price{font:700 16px Manrope,sans-serif;margin:0;color:var(--forest)}
    .cart-item-actions{display:flex;flex-direction:column;gap:10px;align-items:flex-end}
    .cart-item-actions form{margin:0}
    .cart-qty-form{display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end}
    .cart-qty-input{width:70px;padding:8px;border:2px solid var(--line);border-radius:6px;text-align:center;font-weight:600}
    .cart-item-subtotal{margin:0;font-weight:600;text-align:right}

    .cart-summary{position:sticky;top:96px;background:var(--sand);border:2px solid var(--forest)}
    .cart-summary-title{font:700 18px Manrope,sans-serif;margin:0 0 16px;color:var(--forest-dark)}
    .cart-summary-body{padding:16px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
    .cart-summary-row{display:flex;justify-content:space-between;align-items:baseline;gap:12px;margin-bottom:8px}
    .cart-summary-row:last-child{margin-bottom:0}
    .cart-summary-total{font:700 20px Manrope,sans-serif;color:var(--forest-dark);white-space:nowrap}

    @media(max-width:1024px){
        .cart-layout{grid-template-columns:1fr;gap:24px}
        .cart-summary{position:static}
    }
    @media(max-width:600px){
        .cart-page{padding-top:84px!important}
        .cart-item{grid-template-columns:1fr;gap:16px;align-items:stretch}
        .cart-item-actions{align-items:stretch;border-top:1px solid var(--line);padding-top:14px}
        .cart-qty-form{justify-content:flex-start}
        .cart-qty-form .button.small{flex:1 1 auto}
        .cart-item-subtotal{text-align:left}
        .cart-list-title{font-size:16px}
    }
</style>
@endpush
