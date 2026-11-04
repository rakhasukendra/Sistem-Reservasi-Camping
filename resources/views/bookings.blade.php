@extends('layouts.app')
@section('title', 'Riwayat Sewa')
@section('content')
<div class="wrap" style="padding-top:100px;padding-bottom:60px;max-width:900px">
<div class="page-head"><p class="eyebrow">Akun penyewa</p><h1 class="section-title">Riwayat Reservasi</h1><p class="muted" style="margin-top:12px">Pantau status semua reservasi yang Anda ajukan.</p></div>

<div class="row mobile-stack" style="margin-bottom:20px">
    <h2 style="font:700 20px Manrope,sans-serif;margin:0">Semua reservasi</h2>
    <a class="button secondary small" href="{{ route('home') }}#alat">Pilih alat</a>
</div>

@if($bookings->isEmpty())
    <div class="panel empty">Belum ada reservasi. Pilih alat dan tanggal perjalanan untuk mengajukan penyewaan.</div>
@else
    <div class="stack">
        @foreach($bookings as $booking)
            <article class="panel" style="border-left:4px solid {{ $booking->status === 'pending' ? 'var(--warning)' : ($booking->status === 'approved' ? 'var(--success)' : 'var(--muted)') }};padding:20px">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px">
                    <div>
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px">
                            <span style="font:700 18px Manrope,sans-serif;color:var(--forest-dark)">{{ $booking->booking_code }}</span>
                            <span class="status {{ $booking->status }}" style="font-size:11px">{{ strtoupper($booking->status) }}</span>
                        </div>
                        <p class="small-note" style="margin:0">{{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}</p>
                    </div>
                    <p style="font:700 20px Manrope,sans-serif;color:var(--forest-dark);margin:0">Rp{{ number_format($booking->total,0,',','.') }}</p>
                </div>
                
                <div style="background:var(--sand);padding:12px;border-radius:8px;margin-bottom:12px">
                    <p class="small-note" style="margin:0 0 8px;font-weight:600">Alat yang disewa:</p>
                    <ul style="padding-left:20px;line-height:1.8;margin:0;font-size:14px">
                        @foreach($booking->items as $item)
                            <li>{{ $item->name }} — {{ $item->pivot->quantity }} unit</li>
                        @endforeach
                    </ul>
                </div>
                
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:12px;font-size:13px">
                    <div>
                        <p class="small-note" style="margin:0">Kontak</p>
                        <p style="margin:0;font-weight:600">{{ $booking->contact }}</p>
                    </div>
                    <div>
                        <p class="small-note" style="margin:0">Verifikasi</p>
                        <p style="margin:0;font-weight:600;color:{{ $booking->contact_verified ? 'var(--success)' : 'var(--warning)' }}">{{ $booking->contact_verified ? '✓ Terverifikasi' : '⏱ Menunggu' }}</p>
                    </div>
                    <div>
                        <p class="small-note" style="margin:0">Status Pengambilan</p>
                        <p style="margin:0;font-weight:600;color:{{ $booking->pickup_verified ? 'var(--success)' : 'var(--muted)' }}">{{ $booking->pickup_verified ? '✓ Sudah diambil' : '— Belum diambil' }}</p>
                    </div>
                </div>
                
                @if($booking->pickup_verified)
                    <div class="panel" style="margin-bottom:12px;background:#f4f1e7;padding:12px;border-radius:8px">
                        <p class="eyebrow" style="margin:0 0 8px">Kartu pengambilan dummy</p>
                        <strong style="font-size:14px">{{ $booking->booking_code }}</strong>
                        <div class="small-note" style="margin:4px 0 0">{{ $booking->contact_name }} · {{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}</div>
                    </div>
                @endif
                
                @if($booking->cancel_reason)
                    <div style="margin:12px 0;padding:14px;background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;border-left:4px solid #f59e0b">
                        <p class="small-note" style="margin:0 0 6px;font-weight:600;color:#92400e">Alasan pembatalan dari Anda:</p>
                        <p style="margin:0;color:#92400e;font-size:14px">{{ $booking->cancel_reason }}</p>
                    </div>
                @endif
                
                @if($booking->cancellation_requested_at && $booking->cancellation_review_note)
                    <div style="margin:12px 0;padding:14px;background:{{ strpos($booking->cancellation_review_note, 'ditolak') ? '#fee2e2' : '#dcfce7' }};border:1px solid {{ strpos($booking->cancellation_review_note, 'ditolak') ? '#fecaca' : '#bbf7d0' }};border-radius:8px;border-left:4px solid {{ strpos($booking->cancellation_review_note, 'ditolak') ? '#dc2626' : '#10b981' }}">
                        <p class="small-note" style="margin:0 0 6px;font-weight:600;color:{{ strpos($booking->cancellation_review_note, 'ditolak') ? '#7f1d1d' : '#065f46' }}">Review staf:</p>
                        <p style="margin:0;color:{{ strpos($booking->cancellation_review_note, 'ditolak') ? '#7f1d1d' : '#065f46' }};font-size:14px">{{ $booking->cancellation_review_note }}</p>
                    </div>
                @elseif($booking->cancellation_requested_at && !$booking->cancellation_review_note)
                    <div style="margin:12px 0;padding:14px;background:#dbeafe;border:1px solid #bfdbfe;border-radius:8px;border-left:4px solid #3b82f6">
                        <p class="small-note" style="margin:0 0 6px;font-weight:600;color:#1e3a8a">Status:</p>
                        <p style="margin:0;color:#1e3a8a;font-size:14px">Ajuan pembatalan menunggu keputusan staf ({{ $booking->cancellation_requested_at->format('d/m/Y H:i') }})</p>
                    </div>
                @endif
                
                @if($booking->refund_note)
                    <p class="small-note" style="margin:8px 0">Pengembalian dana: {{ $booking->refund_note }}</p>
                @endif
                
                @if($booking->deposit_note)
                    <p class="small-note" style="margin:8px 0">Deposit: {{ $booking->deposit_note }}</p>
                @endif
                
                @if($booking->return_condition !== 'not_returned')
                    <p class="small-note" style="margin:8px 0">Kondisi kembali: {{ $booking->return_condition }}. Penggantian: Rp{{ number_format($booking->replacement_amount,0,',','.') }}. Status: {{ $booking->replacement_paid ? '✓ Tercatat' : '— Belum' }}</p>
                @endif
                
                @if(in_array($booking->status,['pending','approved'],true) && !$booking->cancellation_requested_at && !$booking->pickup_verified)
                    <details style="margin-top:14px">
                        <summary>Ajukan pembatalan</summary>
                        <form class="stack" method="post" action="{{ route('bookings.cancel',$booking) }}" style="margin-top:12px">
                            @csrf
                            <div class="field">
                                <label for="reason-{{ $booking->id }}">Alasan pembatalan</label>
                                <textarea id="reason-{{ $booking->id }}" name="cancel_reason" required maxlength="1000"></textarea>
                            </div>
                            <button class="button secondary small" type="submit">Kirim alasan pembatalan</button>
                        </form>
                    </details>
                @endif
            </article>
        @endforeach
    </div>
@endif
</div>
@endsection
