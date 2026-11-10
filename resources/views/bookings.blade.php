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
            <article id="booking-{{ $booking->id }}" class="panel" style="border-left:4px solid {{ $booking->status === 'pending' ? 'var(--warning)' : ($booking->status === 'approved' ? 'var(--success)' : 'var(--muted)') }};padding:20px">
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

                @if($booking->status === 'pending' && $booking->payment_due_at)
                    @if(!$booking->payment_proof && now()->lt($booking->payment_due_at))
                        <div style="margin:12px 0;padding:14px;background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;border-left:4px solid #f59e0b">
                            <p class="small-note" style="margin:0 0 6px;font-weight:600;color:#92400e">⏱ Waktu pembayaran tersisa:</p>
                            <p style="margin:0;color:#92400e;font-size:18px;font-weight:700" data-countdown="{{ $booking->payment_due_at->toIso8601String() }}">Menghitung...</p>
                            <p class="small-note" style="margin:6px 0 0;color:#92400e">Upload bukti transfer sebelum waktu habis atau pesanan otomatis dibatalkan.</p>
                        </div>
                    @elseif(!$booking->payment_proof && now()->gte($booking->payment_due_at))
                        <div style="margin:12px 0;padding:14px;background:#fee2e2;border:1px solid #fecaca;border-radius:8px;border-left:4px solid #dc2626">
                            <p class="small-note" style="margin:0 0 6px;font-weight:600;color:#7f1d1d">⚠ Waktu pembayaran habis</p>
                            <p style="margin:0;color:#7f1d1d;font-size:14px">Pesanan akan dibatalkan otomatis oleh sistem.</p>
                        </div>
                    @endif
                @endif

                @if($booking->status === 'pending' && !$booking->payment_proof && $booking->payment_due_at && now()->lt($booking->payment_due_at))
                    <details style="margin-top:14px;border:2px solid var(--forest);border-radius:8px;overflow:hidden">
                        <summary style="cursor:pointer;padding:12px 16px;font-weight:600;background:var(--sand);color:var(--forest-dark)">Upload Bukti Transfer</summary>
                        <form class="stack" method="post" action="{{ route('bookings.payment-proof',$booking) }}" enctype="multipart/form-data" style="margin:0;padding:16px;background:white">
                            @csrf
                            <div class="field">
                                <label for="payment-{{ $booking->id }}">File bukti transfer (JPG, PNG, PDF max 5MB)</label>
                                <input id="payment-{{ $booking->id }}" name="payment_proof" type="file" accept="image/jpeg,image/png,application/pdf" required>
                            </div>
                            <button class="button" type="submit">Upload Bukti Transfer</button>
                        </form>
                    </details>
                @endif

                @if($booking->payment_proof)
                    <div style="margin:12px 0;padding:14px;background:#dcfce7;border:1px solid #bbf7d0;border-radius:8px;border-left:4px solid #10b981">
                        <p class="small-note" style="margin:0 0 6px;font-weight:600;color:#065f46">✓ Bukti transfer sudah diupload</p>
                        <a href="{{ asset('storage/'.$booking->payment_proof) }}" target="_blank" style="color:#065f46;font-size:14px;text-decoration:underline">Lihat bukti transfer</a>
                    </div>
                @endif
                
                @if($booking->pickup_verified)
                    <div class="panel" style="margin-bottom:12px;background:#f4f1e7;padding:12px;border-radius:8px">
                        <p class="eyebrow" style="margin:0 0 8px">Kartu pengambilan</p>
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
@if(session('show_payment_reminder'))
    @php
        $latestPendingBooking = $bookings->firstWhere('status', 'pending');
    @endphp
    @if($latestPendingBooking && !$latestPendingBooking->payment_proof)
        <div id="payment-reminder" style="position:fixed;bottom:24px;right:24px;background:#fef3c7;border:2px solid #f59e0b;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.1);padding:20px;max-width:360px;z-index:9999;animation:slideIn 0.3s ease">
            <div style="display:flex;justify-content:space-between;align-items:start">
                <div>
                    <p style="margin:0 0 8px;font-weight:700;color:#92400e">📝 Silakan Upload Bukti Transfer</p>
                    <p style="margin:0 0 12px;font-size:13px;color:#92400e">Upload bukti transfer untuk memverifikasi pembayaran Anda. Waktu tersisa: <strong id="countdown-text" style="font-size:16px">10:00</strong></p>
                    <a href="{{ route('bookings.index') }}#booking-{{ $latestPendingBooking->id }}" style="display:inline-block;padding:8px 16px;background:#f59e0b;color:white;border-radius:6px;text-decoration:none;font-size:13px;font-weight:600">Upload Sekarang</a>
                </div>
                <button type="button" onclick="closePaymentReminder()" style="background:none;border:none;cursor:pointer;color:#92400e;font-size:20px;line-height:1">✕</button>
            </div>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid #fcd34d">
                <p style="margin:0;font-size:12px;color:#92400e">Booking: <strong>{{ $latestPendingBooking->booking_code }}</strong></p>
            </div>
        </div>
        <style>
            @keyframes slideIn {
                from { transform: translateY(100px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
        </style>
        <script>
        function closePaymentReminder() {
            document.getElementById('payment-reminder').style.display = 'none';
            // Mark popup as closed in localStorage
            localStorage.setItem('paymentReminderClosed', 'true');
        }
        
        // Don't show popup if closed
        if (localStorage.getItem('paymentReminderClosed') === 'true') {
            document.getElementById('payment-reminder').style.display = 'none';
        }
        
        // Clear on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                localStorage.removeItem('paymentReminderClosed');
            }, 5000);
        });
        
        function updatePaymentCountdown() {
            const timer = document.getElementById('countdown-text');
            if (!timer) return;
            
            let minutes = 9;
            let seconds = 59;
            
            function tick() {
                if (seconds <= 0) {
                    if (minutes <= 0) {
                        timer.textContent = '00:00';
                        return;
                    }
                    minutes--;
                    seconds = 59;
                } else {
                    seconds--;
                }
                timer.textContent = minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
                setTimeout(tick, 1000);
            }
            
            tick();
        }
        
        document.addEventListener('DOMContentLoaded', updatePaymentCountdown);
        </script>
    @endif
@endif

@push('scripts')
<script>
function updateCountdown(element) {
    const deadline = new Date(element.getAttribute('data-countdown')).getTime();
    
    function tick() {
        const now = new Date().getTime();
        const distance = deadline - now;
        
        if (distance < 0) {
            element.textContent = 'Waktu habis';
            return;
        }
        
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        element.textContent = minutes + 'm ' + seconds + 's';
        setTimeout(tick, 1000);
    }
    
    tick();
}

document.querySelectorAll('[data-countdown]').forEach(el => updateCountdown(el));
</script>
@endpush
@endsection
