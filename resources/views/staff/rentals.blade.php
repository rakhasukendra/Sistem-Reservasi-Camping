@extends('layouts.app')
@section('title', 'Kelola Sewa')

@push('head')
<style>
    .verify-dropdown summary{list-style:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer}
    .verify-dropdown summary::-webkit-details-marker{display:none}
    .verify-dropdown summary svg{color:var(--forest);transition:transform .2s}
    .verify-dropdown[open] summary svg{transform:rotate(180deg)}
    .verify-dropdown-menu{margin-top:8px;padding:10px;background:var(--sand);border:1px solid var(--line);border-radius:6px}
    .verify-dropdown-menu form{margin:0}
    .verify-dropdown-menu .button{width:100%;display:flex;justify-content:center;padding:8px 10px;font-size:12px}
</style>
@endpush
@section('content')
<div class="wrap" style="padding-top:100px;padding-bottom:60px">
    <div class="page-head row mobile-stack">
        <div>
            <p class="eyebrow">Operasional Rimba Sewa</p>
            <h1 class="section-title">Panel {{ auth()->user()->role }}</h1>
        </div>

    </div>

    <div style="display:flex;gap:8px;margin:20px 0;border-bottom:2px solid var(--line);overflow-x:auto;-webkit-overflow-scrolling:touch">
        <a href="{{ route('staff.rentals') }}" style="padding:12px 20px;font-weight:600;color:var(--forest);border-bottom:3px solid var(--forest);margin-bottom:-2px;text-decoration:none;white-space:nowrap;flex-shrink:0">Kelola Sewa</a>
        <a href="{{ route('staff.inventory') }}" style="padding:12px 20px;color:var(--muted);text-decoration:none;white-space:nowrap;flex-shrink:0">Kelola Stok</a>
        <a href="{{ route('staff.articles') }}" style="padding:12px 20px;color:var(--muted);text-decoration:none;white-space:nowrap;flex-shrink:0">Edit Artikel</a>
    </div>

    <section style="padding:30px 0;border-top:1px solid var(--line)">
        <div class="row mobile-stack">
            <div>
                <h2 style="font:700 21px Manrope,sans-serif;margin:0">Kelola reservasi & transaksi</h2>
                <p class="small-note">Tinjau, setujui, dan kelola semua permintaan reservasi.</p>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
                <div>
                    <label for="search-booking" class="small-note" style="display:block;margin-bottom:4px">Cari kode / penyewa</label>
                    <input id="search-booking" type="search" placeholder="Contoh: CAMP-XXXXXXXX" autocomplete="off" oninput="filterTransactions()" style="padding:8px 12px;border:1px solid var(--line);border-radius:6px;min-width:220px">
                </div>
                <div>
                    <label for="filter-status" class="small-note" style="display:block;margin-bottom:4px">Filter status</label>
                    <select id="filter-status" onchange="filterTransactions()" style="padding:8px 12px;border:1px solid var(--line);border-radius:6px;min-width:200px">
                        <option value="all">Semua transaksi</option>
                        <option value="pending">Menunggu keputusan</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
            </div>
        </div>

        @php
            $allTransactions = $allBookings->sortByDesc('created_at');
        @endphp

        @if($allTransactions->isEmpty())
            <div class="panel empty" style="padding:40px 24px;margin-top:16px">
                <p style="margin:0;color:var(--muted)">Transaksi reservasi akan tampil di sini.</p>
            </div>
        @else
            <div class="table-wrap panel" style="padding:0;border:1px solid var(--line);border-radius:10px;margin-top:16px">
                <table style="border-collapse:collapse;width:100%;min-width:max-content">
                    <thead>
                        <tr style="background:var(--sand)">
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Kode</th>
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Penyewa</th>
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Tanggal</th>
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Status</th>
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Nilai</th>
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transactions-table">
                        @foreach($allTransactions as $transaction)
                            <tr data-status="{{ $transaction->status }}" data-pending="{{ $transaction->status === 'pending' || $transaction->cancellation_requested_at ? 'true' : 'false' }}" data-search="{{ strtolower($transaction->booking_code.' '.$transaction->user->name) }}" style="border-top:1px solid var(--line)">
                                <td style="padding:16px 20px;font-weight:600;color:var(--forest-dark);border:none;font-size:15px">{{ $transaction->booking_code }}</td>
                                <td style="padding:16px 20px;color:var(--ink);border:none;font-size:14px">{{ $transaction->user->name }}</td>
                                <td style="padding:16px 20px;color:var(--muted);border:none;font-size:14px">
                                    {{ $transaction->start_date->format('d/m/Y') }}<br>sampai {{ $transaction->end_date->format('d/m/Y') }}
                                    @if($transaction->payment_proof)
                                        <br><a href="{{ route('bookings.payment-proof.show', $transaction) }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;padding:6px 12px;background:#dcfce7;color:#065f46;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600;border:1px solid #bbf7d0">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Lihat Bukti Transfer
                                        </a>
                                    @else
                                        <br><span style="display:inline-block;margin-top:8px;padding:6px 12px;background:#fee2e2;color:#7f1d1d;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #fecaca">Belum upload</span>
                                    @endif
                                </td>
                                <td style="padding:16px 20px;border:none">
                                    <span class="status {{ $transaction->status }}" style="display:inline-block;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:700;text-transform:uppercase">
                                        {{ $transaction->cancellation_requested_at ? 'pembatalan diminta' : $transaction->status }}
                                    </span>
                                </td>
                                <td style="padding:16px 20px;font-weight:600;color:var(--forest-dark);border:none;font-size:15px">Rp{{ number_format($transaction->total,0,',','.') }}</td>
                                <td style="padding:16px 20px;border:none">
                                    <button class="button secondary small" type="button" onclick="toggleTransactionEdit('transaction-{{ $transaction->id }}')">Edit</button>
                                    <div id="transaction-{{ $transaction->id }}" style="display:none;margin-top:12px;padding:12px;background:var(--sand);border-radius:6px">
                                        <form method="post" action="{{ route('staff.bookings.decide', $transaction) }}">
                                            @csrf
                                            <div class="field" style="margin-bottom:10px">
                                                <label for="status-{{ $transaction->id }}" style="font-size:13px;font-weight:600">Status</label>
                                                <select id="status-{{ $transaction->id }}" name="decision" style="padding:8px;border:1px solid var(--line);border-radius:4px;width:100%">
                                                    <option value="approved" @selected($transaction->status === 'approved')>Approved</option>
                                                    <option value="rejected" @selected($transaction->status === 'rejected')>Rejected</option>
                                                </select>
                                            </div>
                                            <div class="field" style="margin-bottom:10px">
                                                <label for="note-{{ $transaction->id }}" style="font-size:13px;font-weight:600">Alasan/Catatan</label>
                                                <textarea id="note-{{ $transaction->id }}" name="reason" maxlength="1000" style="padding:8px;border:1px solid var(--line);border-radius:4px;width:100%;min-height:60px" placeholder="Catatan perubahan status">{{ $transaction->cancel_reason ?? $transaction->cancellation_review_note }}</textarea>
                                            </div>
                                            <div style="display:flex;gap:8px">
                                                <button class="button small" type="submit">Simpan</button>
                                                <button class="button secondary small" type="button" onclick="toggleTransactionEdit('transaction-{{ $transaction->id }}')">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="panel empty" id="transactions-empty" style="margin-top:16px" hidden>Tidak ada transaksi yang cocok dengan pencarian atau filter.</p>
        @endif
    </section>

    <section style="padding:30px 0;border-top:1px solid var(--line)">
        <div class="row mobile-stack">
            <div>
                <h2 style="font:700 21px Manrope,sans-serif;margin:0">Riwayat sewa & pengembalian</h2>
                <p class="small-note">Reservasi yang disetujui berdasarkan tanggal mulai sewa, termasuk yang sudah dikembalikan.</p>
                <p class="small-note" style="margin:4px 0 0">Reservasi ditemukan: {{ $bookings->count() }}</p>
            </div>
        </div>

        <form method="get" action="{{ route('staff.rentals') }}" style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;margin:16px 0">
            <div class="field">
                <label for="booking-condition">Kondisi barang</label>
                <select id="booking-condition" name="condition" onchange="this.form.submit()">
                    <option value="all" @selected($condition === 'all')>Semua</option>
                    <option value="not_returned" @selected($condition === 'not_returned')>Belum dikembalikan</option>
                    <option value="good" @selected($condition === 'good')>Baik</option>
                    <option value="damaged" @selected($condition === 'damaged')>Rusak</option>
                    <option value="lost" @selected($condition === 'lost')>Hilang</option>
                </select>
            </div>
            <div class="field">
                <label for="booking-month">Tanggal mulai sewa</label>
                <input id="booking-month" name="month" type="month" value="{{ $month }}" onchange="this.form.submit()">
            </div>
        </form>

        @if($bookings->isEmpty())
            <div class="panel empty">Tidak ada reservasi disetujui pada periode ini.</div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(380px,1fr));gap:16px;margin-top:16px">
                @foreach($bookings as $booking)
                    <div class="panel" style="padding:18px">
                        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:12px">
                            <div>
                                <h3 style="font:600 16px Manrope,sans-serif;margin:0 0 4px;color:var(--forest-dark)">{{ $booking->booking_code }}</h3>
                                <p style="font-size:13px;color:var(--muted);margin:0">{{ $booking->user->name }} · {{ $booking->contact }}</p>
                            </div>
                        </div>

                        <div style="padding:12px;background:var(--sand);border-radius:6px;margin-bottom:12px">
                            <p style="font-size:12px;color:var(--muted);margin:0 0 6px">Periode sewa</p>
                            <p style="font-weight:600;margin:0;font-size:14px">{{ $booking->start_date->format('d M') }} - {{ $booking->end_date->format('d M Y') }}</p>
                        </div>

                        <div style="margin-bottom:12px">
                            <p style="font-size:12px;color:var(--muted);margin:0 0 8px">Alat yang disewa:</p>
                            @foreach($booking->items as $item)
                                <span style="display:inline-block;padding:4px 10px;background:white;border:1px solid var(--line);border-radius:4px;font-size:12px;margin:0 4px 4px 0">
                                    {{ $item->name }} ({{ $item->pivot->quantity }}×)
                                </span>
                            @endforeach
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;padding:12px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line);margin-bottom:12px">
                            <div>
                                <p style="font-size:11px;color:var(--muted);margin:0 0 6px">Kontak</p>
                                <details class="verify-dropdown">
                                    <summary>
                                        <span style="display:inline-block;padding:4px 8px;background:{{ $booking->contact_verified ? 'var(--success)' : 'var(--warning)' }};color:white;border-radius:4px;font-size:11px;font-weight:700">
                                            {{ $booking->contact_verified ? '✓ Terverifikasi' : 'Belum' }}
                                        </span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                    </summary>
                                    <div class="verify-dropdown-menu">
                                        @if(!$booking->contact_verified)
                                            <form method="post" action="{{ route('staff.bookings.verify', $booking) }}">
                                                @csrf
                                                <button class="button small" type="submit">Verifikasi kontak</button>
                                            </form>
                                        @else
                                            <form method="post" action="{{ route('staff.bookings.verify', $booking) }}">
                                                @csrf
                                                <input type="hidden" name="reset" value="1">
                                                <button class="button small" type="submit" style="background:#dc2626;border-color:#dc2626">Reset verifikasi kontak</button>
                                            </form>
                                        @endif
                                    </div>
                                </details>
                            </div>
                            <div>
                                <p style="font-size:11px;color:var(--muted);margin:0 0 6px">Pengambilan</p>
                                <details class="verify-dropdown">
                                    <summary>
                                        <span style="display:inline-block;padding:4px 8px;background:{{ $booking->pickup_verified ? 'var(--success)' : 'var(--warning)' }};color:white;border-radius:4px;font-size:11px;font-weight:700">
                                            {{ $booking->pickup_verified ? '✓ Dicatat' : 'Belum' }}
                                        </span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                    </summary>
                                    <div class="verify-dropdown-menu">
                                        @if(!$booking->pickup_verified)
                                            <form method="post" action="{{ route('staff.bookings.pickup', $booking) }}">
                                                @csrf
                                                <button class="button small" type="submit">Catat pengambilan</button>
                                            </form>
                                        @else
                                            <form method="post" action="{{ route('staff.bookings.pickup', $booking) }}">
                                                @csrf
                                                <input type="hidden" name="reset" value="1">
                                                <button class="button small" type="submit" style="background:#dc2626;border-color:#dc2626">Reset catatan pengambilan</button>
                                            </form>
                                        @endif
                                    </div>
                                </details>
                            </div>
                        </div>

                        @if($booking->payment_proof)
                            <div style="margin-bottom:12px;padding:12px;background:#dcfce7;border:1px solid #bbf7d0;border-radius:6px">
                                <p style="font-size:11px;color:#065f46;margin:0 0 8px;font-weight:600">✓ Bukti Transfer Tersedia</p>
                                <a href="{{ route('bookings.payment-proof.show', $booking) }}" target="_blank" rel="noopener" class="button secondary small" style="width:100%;justify-content:center;display:flex;align-items:center;gap:6px;font-size:12px">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat Bukti Transfer
                                </a>
                            </div>
                        @elseif($booking->status === 'pending')
                            <div style="margin-bottom:12px;padding:12px;background:#fee2e2;border:1px solid #fecaca;border-radius:6px">
                                <p style="font-size:11px;color:#7f1d1d;margin:0;font-weight:600">⚠ Belum upload bukti transfer</p>
                            </div>
                        @endif

                        @if($booking->contact_verified && $booking->pickup_verified)
                            @if($booking->return_condition === 'not_returned')
                                <details style="margin-top:12px">
                                    <summary style="font-size:13px;cursor:pointer;color:var(--forest);font-weight:600">Catat kondisi pengembalian</summary>
                                    <form class="form-grid" method="post" action="{{ route('staff.bookings.condition', $booking) }}" style="margin-top:12px;padding-top:12px;border-top:1px solid var(--line)">
                                        @csrf
                                        <div class="field">
                                            <label for="condition-{{ $booking->id }}">Kondisi</label>
                                            <select id="condition-{{ $booking->id }}" name="condition">
                                                <option value="good">Baik</option>
                                                <option value="damaged">Rusak</option>
                                                <option value="lost">Hilang</option>
                                            </select>
                                        </div>
                                        <div class="field">
                                            <label for="amount-{{ $booking->id }}">Nominal penggantian (Rp)</label>
                                            <input id="amount-{{ $booking->id }}" type="number" min="0" name="replacement_amount" value="0">
                                        </div>
                                        <label style="grid-column:1/-1">
                                            <input type="checkbox" name="replacement_paid" value="1"> 
                                            Penggantian sudah lunas
                                        </label>
                                        <button class="button secondary small" type="submit" style="grid-column:1/-1">Simpan kondisi</button>
                                    </form>
                                </details>
                            @else
                                <div style="margin-top:12px;padding:12px;background:var(--sand);border-radius:6px">
                                    <p style="font-size:12px;color:var(--muted);margin:0 0 4px">Kondisi pengembalian</p>
                                    <p style="font-weight:600;margin:0">
                                        @if($booking->return_condition === 'good') ✓ Baik
                                        @elseif($booking->return_condition === 'damaged') ⚠ Rusak
                                        @elseif($booking->return_condition === 'lost') ✗ Hilang
                                        @endif
                                    </p>
                                    @if($booking->replacement_amount > 0)
                                        <p style="font-size:12px;color:var(--muted);margin:8px 0 0">Penggantian: Rp{{ number_format($booking->replacement_amount,0,',','.') }} {{ $booking->replacement_paid ? '(Lunas)' : '(Belum bayar)' }}</p>
                                    @endif
                                </div>
                                <details style="margin-top:12px">
                                    <summary style="font-size:13px;cursor:pointer;color:var(--forest);font-weight:600">Ubah kondisi pengembalian</summary>
                                    <form class="form-grid" method="post" action="{{ route('staff.bookings.condition', $booking) }}" style="margin-top:12px;padding-top:12px;border-top:1px solid var(--line)">
                                        @csrf
                                        <div class="field">
                                            <label for="condition-{{ $booking->id }}">Kondisi</label>
                                            <select id="condition-{{ $booking->id }}" name="condition">
                                                <option value="good" @selected($booking->return_condition === 'good')>Baik</option>
                                                <option value="damaged" @selected($booking->return_condition === 'damaged')>Rusak</option>
                                                <option value="lost" @selected($booking->return_condition === 'lost')>Hilang</option>
                                            </select>
                                        </div>
                                        <div class="field">
                                            <label for="amount-{{ $booking->id }}">Nominal penggantian (Rp)</label>
                                            <input id="amount-{{ $booking->id }}" type="number" min="0" name="replacement_amount" value="{{ $booking->replacement_amount ?? 0 }}">
                                        </div>
                                        <label style="grid-column:1/-1">
                                            <input type="checkbox" name="replacement_paid" value="1" @checked($booking->replacement_paid)> 
                                            Penggantian sudah lunas
                                        </label>
                                        <button class="button secondary small" type="submit" style="grid-column:1/-1">Simpan kondisi</button>
                                    </form>
                                </details>
                            @endif
                        @else
                            <details style="margin-top:12px">
                                <summary style="font-size:13px;cursor:pointer;color:var(--muted);font-weight:600">Catat kondisi pengembalian</summary>
                                <p class="small-note" style="margin-top:12px;padding-top:12px;border-top:1px solid var(--line)">
                                    Verifikasi kontak dan catat pengambilan terlebih dahulu sebelum mencatat kondisi pengembalian.
                                </p>
                            </details>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>

@push('scripts')
<script>
function toggleTransactionEdit(elementId) {
    const element = document.getElementById(elementId);
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}

function updateStatusBadge(formElement, type) {
    const bookingCard = formElement.closest('[data-booking-id]');
    const isReset = formElement.querySelector('input[name="reset"]');
    
    if (isReset) {
        if (type === 'contact') {
            const badge = bookingCard.querySelector('[data-badge="contact"]');
            if (badge) {
                badge.style.background = 'var(--warning)';
                badge.textContent = 'Belum';
            }
        } else if (type === 'pickup') {
            const badge = bookingCard.querySelector('[data-badge="pickup"]');
            if (badge) {
                badge.style.background = 'var(--warning)';
                badge.textContent = '✓ Belum';
            }
        }
    }
}

function filterTransactions() {
    const statusField = document.getElementById('filter-status');
    const searchField = document.getElementById('search-booking');
    const status = statusField ? statusField.value : 'all';
    const term = (searchField ? searchField.value : '').trim().toLowerCase();
    let visible = 0;

    document.querySelectorAll('#transactions-table tr').forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const isPending = row.getAttribute('data-pending') === 'true';
        const statusMatch = status === 'all' || (status === 'pending' ? isPending : rowStatus === status);
        const searchMatch = !term || (row.getAttribute('data-search') || '').includes(term);
        const show = statusMatch && searchMatch;

        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const empty = document.getElementById('transactions-empty');
    if (empty) empty.hidden = visible > 0;
}
</script>
@endpush
@endsection
