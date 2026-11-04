@extends('layouts.app')
@section('title', 'Form Reservasi')
@section('content')
<div class="wrap" style="padding-top:100px;padding-bottom:60px">
<div class="page-head">
    <p class="eyebrow">Lengkapi data</p>
    <h1 class="section-title">Form Reservasi</h1>
    <p class="muted" style="margin-top:12px;max-width:700px">Tentukan tanggal sewa dan informasi kontak untuk melanjutkan.</p>
</div>

<section style="padding:32px 0">
    <div style="display:grid;grid-template-columns:1fr 400px;gap:32px;align-items:start">
        <form id="booking-form" method="post" action="{{ route('bookings.store') }}" class="panel">
            @csrf
            <h3 style="font:700 18px Manrope,sans-serif;margin:0 0 20px;color:var(--forest-dark)">Detail Reservasi</h3>
            
            <div class="form-grid">
                <div class="field">
                    <label for="start_date">Tanggal mulai sewa</label>
                    <input id="start_date" name="start_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('start_date', now()->addDay()->toDateString()) }}" required>
                </div>
                <div class="field">
                    <label for="end_date">Tanggal selesai sewa</label>
                    <input id="end_date" name="end_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('end_date', now()->addDays(3)->toDateString()) }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="contact_name">Nama pemesan</label>
                    <input id="contact_name" name="contact_name" maxlength="100" value="{{ old('contact_name', auth()->user()->name) }}" required placeholder="Nama lengkap">
                </div>
                <div class="field">
                    <label for="contact">Email atau nomor telepon</label>
                    <input id="contact" name="contact" maxlength="120" value="{{ old('contact', auth()->user()->phone ?? auth()->user()->email) }}" required placeholder="Kontak yang bisa dihubungi">
                </div>
            </div>

            @foreach($cartItems as $index => $cartItem)
                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $cartItem['item']->id }}">
                <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $cartItem['quantity'] }}">
            @endforeach

            <p id="availability-message" class="small-note" role="status" aria-live="polite" style="margin:16px 0;padding:12px;background:var(--sand);border-radius:6px">Pilih tanggal untuk memeriksa ketersediaan stok.</p>

            <div style="display:flex;gap:12px;margin-top:20px">
                <button class="button" type="submit" style="flex:1">Kirim permintaan reservasi</button>
                <a href="{{ route('cart') }}" class="button secondary">Kembali ke keranjang</a>
            </div>

            <p class="small-note" style="margin-top:16px;text-align:center">Status awal menunggu konfirmasi. Pembayaran di aplikasi ini hanya simulasi.</p>
        </form>

        <div class="panel" style="position:sticky;top:100px;background:var(--sand);border:2px solid var(--forest)">
            <h3 style="font:700 18px Manrope,sans-serif;margin:0 0 16px;color:var(--forest-dark)">Ringkasan Pesanan</h3>
            
            <div style="margin-bottom:16px">
                <h4 style="font:600 14px Manrope,sans-serif;margin:0 0 12px;color:var(--muted)">Alat yang disewa:</h4>
                @foreach($cartItems as $cartItem)
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid var(--line)">
                        <div>
                            <p style="font-weight:600;margin:0;font-size:14px">{{ $cartItem['item']->name }}</p>
                            <p class="small-note" style="margin:0">{{ $cartItem['quantity'] }} unit × Rp{{ number_format($cartItem['item']->daily_rate, 0, ',', '.') }}/hari</p>
                        </div>
                        <p style="font-weight:700;margin:0;color:var(--forest)">Rp{{ number_format($cartItem['item']->daily_rate * $cartItem['quantity'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>

            <div style="padding:16px 0;border-top:2px solid var(--line);border-bottom:2px solid var(--line)">
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

            <p class="small-note" style="margin:16px 0 0;line-height:1.6">Total biaya final akan dihitung berdasarkan durasi sewa yang Anda pilih di formulir.</p>
        </div>
    </div>
</section>
</div>

@push('scripts')
<script>
const form = document.getElementById('booking-form');
const status = document.getElementById('availability-message');
const refresh = async () => {
    const start = form.elements.start_date.value;
    const end = form.elements.end_date.value;
    if (!start || !end) {
        status.textContent = 'Pilih tanggal sewa dan pengembalian untuk memeriksa stok.';
        status.style.background = 'var(--sand)';
        return;
    }
    status.textContent = 'Memeriksa ketersediaan stok...';
    status.style.background = 'var(--sand)';
    try {
        const items = @json($cartItems->map(fn($item) => ['id' => $item['item']->id, 'quantity' => $item['quantity']])->values());
        const response = await fetch(@json(route('bookings.availability')), {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': form.querySelector('[name=_token]').value},
            body: JSON.stringify({start_date: start, end_date: end, items})
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Stok belum dapat diperiksa.');
        const unavailable = result.items.filter(item => !item.available);
        if (unavailable.length) {
            status.textContent = unavailable.map(item => `${item.name}: ${item.message}`).join(' · ');
            status.style.background = '#fee2e2';
            status.style.color = 'var(--error)';
        } else {
            status.textContent = '✓ Semua alat tersedia untuk tanggal yang dipilih.';
            status.style.background = '#d1fae5';
            status.style.color = 'var(--success)';
        }
    } catch (error) {
        status.textContent = error.message || 'Koneksi gagal. Silakan periksa kembali.';
        status.style.background = '#fee2e2';
        status.style.color = 'var(--error)';
    }
};
form.elements.start_date.addEventListener('change', refresh);
form.elements.end_date.addEventListener('change', refresh);
refresh();
</script>
@endpush

@push('head')
<style>
@media(max-width:1024px){
    section>div{
        grid-template-columns:1fr!important;
    }
    .panel[style*="position:sticky"]{
        position:static!important;
        order:-1;
    }
}
</style>
@endpush
@endsection
