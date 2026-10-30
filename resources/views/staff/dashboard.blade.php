@extends('layouts.app')
@section('title', 'Panel staf')
@section('content')
<div class="wrap" style="padding-top:100px;padding-bottom:60px">
    <div class="page-head row mobile-stack">
        <div>
            <p class="eyebrow">Operasional Rimba Sewa</p>
            <h1 class="section-title">Panel {{ auth()->user()->role }}</h1>
            <p class="muted">Kelola stok alat, tinjau permintaan sewa, dan catat kondisi pengembalian.</p>
        </div>
        <span class="sample-tag">Transaksi dan pembayaran dummy</span>
    </div>

    <section style="padding-bottom:32px">
        <div class="row mobile-stack" style="margin-bottom:20px">
            <div>
                <h2 style="font:700 21px Manrope,sans-serif;margin:0">Stok alat camping</h2>
                <p class="small-note">Kelola ketersediaan dan harga alat sewa</p>
            </div>
            <button type="button" onclick="document.getElementById('add-item-form').style.display='block'" class="button small">+ Tambah Alat</button>
        </div>

        <div id="add-item-form" style="display:none;margin-bottom:20px">
            <div class="panel">
                <h3 style="font:600 18px Manrope,sans-serif;margin:0 0 16px">Tambah alat baru</h3>
                <form class="form-grid" method="post" action="{{ route('staff.items.store') }}">
                    @csrf
                    <div class="field">
                        <label for="new-name">Nama alat</label>
                        <input id="new-name" name="name" required maxlength="120">
                    </div>
                    <div class="field">
                        <label for="new-category">Kategori</label>
                        <input id="new-category" name="category" required maxlength="80">
                    </div>
                    <div class="field">
                        <label for="new-stock">Stok total</label>
                        <input id="new-stock" name="stock_total" type="number" min="0" value="0" required>
                    </div>
                    <div class="field">
                        <label for="new-damaged">Stok rusak</label>
                        <input id="new-damaged" name="stock_damaged" type="number" min="0" value="0" required>
                    </div>
                    <div class="field">
                        <label for="new-rate">Tarif harian</label>
                        <input id="new-rate" name="daily_rate" type="number" min="0" value="0" required>
                    </div>
                    <label>
                        <input type="checkbox" name="is_available" value="1" checked> 
                        Tersedia untuk disewa
                    </label>
                    <div class="field" style="grid-column:1/-1">
                        <label for="new-description">Deskripsi</label>
                        <textarea id="new-description" name="description" required maxlength="2000"></textarea>
                    </div>
                    <div style="grid-column:1/-1;display:flex;gap:10px">
                        <button class="button small" type="submit">Simpan alat</button>
                        <button class="button secondary small" type="button" onclick="document.getElementById('add-item-form').style.display='none'">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        @if($items->isEmpty())
            <div class="panel empty">Belum ada data alat camping.</div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px">
                @foreach($items as $item)
                    @php
                        $availableStock = max(0, $item->stock_total - $item->stock_damaged);
                        $stockPercentage = $item->stock_total > 0 ? ($availableStock / $item->stock_total) * 100 : 0;
                        $stockColor = $stockPercentage > 50 ? 'var(--success)' : ($stockPercentage > 20 ? 'var(--warning)' : 'var(--error)');
                    @endphp
                    <div class="panel" style="padding:20px">
                        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:12px">
                            <div>
                                <h3 style="font:600 17px Manrope,sans-serif;margin:0 0 4px;color:var(--forest-dark)">{{ $item->name }}</h3>
                                <span style="font-size:13px;color:var(--muted)">{{ $item->category }}</span>
                            </div>
                            <span style="display:inline-block;padding:4px 8px;background:{{ $item->is_available ? 'var(--success)' : 'var(--muted)' }};color:white;border-radius:4px;font-size:11px;font-weight:700">
                                {{ $item->is_available ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </div>

                        <div style="margin:16px 0;padding:16px;background:var(--sand);border-radius:8px;border-left:4px solid {{ $stockColor }}">
                            <div style="display:flex;justify-content:space-between;align-items:end;margin-bottom:8px">
                                <span style="font-size:13px;color:var(--muted)">Stok tersedia</span>
                                <span style="font:700 24px Manrope,sans-serif;color:{{ $stockColor }}">{{ $availableStock }}</span>
                            </div>
                            <div style="display:flex;gap:12px;font-size:12px;color:var(--muted)">
                                <span>Total: {{ $item->stock_total }}</span>
                                @if($item->stock_damaged > 0)
                                    <span style="color:var(--error)">Rusak: {{ $item->stock_damaged }}</span>
                                @endif
                            </div>
                        </div>

                        <div style="padding:12px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line);margin-bottom:12px">
                            <span style="font-size:13px;color:var(--muted)">Tarif sewa</span>
                            <p style="font:600 18px Manrope,sans-serif;margin:4px 0 0;color:var(--forest-dark)">Rp{{ number_format($item->daily_rate,0,',','.') }}/hari</p>
                        </div>

                        <a href="{{ route('staff.items.edit', $item) }}" class="button secondary small" style="width:100%;text-align:center;display:block">Edit Detail</a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section style="padding:30px 0;border-top:1px solid var(--line)">
        <div class="row mobile-stack">
            <div>
                <h2 style="font:700 21px Manrope,sans-serif;margin:0">Riwayat sewa &amp; pengembalian</h2>
                <p class="small-note">Reservasi yang disetujui berdasarkan tanggal mulai sewa, termasuk yang sudah dikembalikan.</p>
                <p class="small-note" style="margin:4px 0 0">Reservasi ditemukan: {{ $bookings->count() }}</p>
            </div>
        </div>

        <form method="get" action="{{ route('staff.dashboard') }}" style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;margin:16px 0">
            <div class="field">
                <label for="booking-period">Periode</label>
                <select id="booking-period" name="period" onchange="this.form.submit()">
                    <option value="daily" @selected($period === 'daily')>Harian</option>
                    <option value="monthly" @selected($period === 'monthly')>Bulanan</option>
                </select>
            </div>
            @if($period === 'daily')
                <div class="field">
                    <label for="booking-day">Tanggal mulai sewa</label>
                    <input id="booking-day" name="day" type="date" value="{{ $day }}">
                </div>
            @else
                <div class="field">
                    <label for="booking-month">Bulan mulai sewa</label>
                    <input id="booking-month" name="month" type="month" value="{{ $month }}">
                </div>
            @endif
            <button class="button secondary small" type="submit">Tampilkan</button>
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
                                <p style="font-size:11px;color:var(--muted);margin:0 0 4px">Kontak</p>
                                <span style="display:inline-block;padding:4px 8px;background:{{ $booking->contact_verified ? 'var(--success)' : 'var(--warning)' }};color:white;border-radius:4px;font-size:11px;font-weight:700">
                                    {{ $booking->contact_verified ? '✓ Terverifikasi' : 'Belum' }}
                                </span>
                            </div>
                            <div>
                                <p style="font-size:11px;color:var(--muted);margin:0 0 4px">Pengambilan</p>
                                <span style="display:inline-block;padding:4px 8px;background:{{ $booking->pickup_verified ? 'var(--success)' : 'var(--warning)' }};color:white;border-radius:4px;font-size:11px;font-weight:700">
                                    {{ $booking->pickup_verified ? '✓ Dicatat' : 'Belum' }}
                                </span>
                            </div>
                        </div>

                        <div style="display:flex;gap:8px;flex-wrap:wrap">
                            @if(!$booking->contact_verified)
                                <form method="post" action="{{ route('staff.bookings.verify', $booking) }}">
                                    @csrf
                                    <button class="button small" type="submit" style="font-size:12px;padding:8px 12px">Verifikasi kontak</button>
                                </form>
                            @endif

                            @if(!$booking->pickup_verified)
                                <form method="post" action="{{ route('staff.bookings.pickup', $booking) }}">
                                    @csrf
                                    <button class="button small" type="submit" style="font-size:12px;padding:8px 12px">Catat pengambilan</button>
                                </form>
                            @endif
                        </div>

                        @if($booking->contact_verified || $booking->pickup_verified)
                            @php
                                $verifiedStates = collect([
                                    $booking->contact_verified ? 'Kontak terverifikasi' : null,
                                    $booking->pickup_verified ? 'Pengambilan tercatat' : null,
                                ])->filter()->implode(', ');
                            @endphp
                            <details style="margin-top:12px">
                                <summary style="font-size:13px;cursor:pointer;color:var(--forest);font-weight:600">Edit Status Verifikasi</summary>
                                <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--line)">
                                    <p class="small-note" style="margin-bottom:12px">Status saat ini: {{ $verifiedStates }}</p>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                                        @if($booking->contact_verified)
                                            <form method="post" action="{{ route('staff.bookings.verify', $booking) }}">
                                                @csrf
                                                <input type="hidden" name="reset" value="1">
                                                <button class="button secondary small" type="submit">Reset verifikasi kontak</button>
                                            </form>
                                        @endif
                                        @if($booking->pickup_verified)
                                            <form method="post" action="{{ route('staff.bookings.pickup', $booking) }}">
                                                @csrf
                                                <input type="hidden" name="reset" value="1">
                                                <button class="button secondary small" type="submit">Reset catatan pengambilan</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </details>
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
                                </div>
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

    <section style="padding:30px 0;border-top:1px solid var(--line)">
        <div class="row mobile-stack">
            <div>
                <h2 style="font:700 21px Manrope,sans-serif;margin:0">Kelola reservasi & transaksi</h2>
                <p class="small-note">Tinjau, setujui, dan kelola semua permintaan reservasi.</p>
            </div>
            <div>
                <label for="filter-status" class="small-note" style="display:block;margin-bottom:4px">Filter status</label>
                <select id="filter-status" onchange="filterTransactions(this.value)" style="padding:8px 12px;border:1px solid var(--line);border-radius:6px;min-width:200px">
                    <option value="all">Semua transaksi</option>
                    <option value="pending">Menunggu keputusan</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
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
            <div class="table-wrap panel" style="padding:0;border:1px solid var(--line);border-radius:10px;overflow:hidden;margin-top:16px">
                <table style="border-collapse:collapse;width:100%">
                    <thead>
                        <tr style="background:var(--sand)">
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Kode</th>
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Penyewa</th>
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Tanggal</th>
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Status</th>
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Nilai dummy</th>
                            <th style="padding:18px 20px;text-align:left;font-weight:700;color:var(--forest-dark);border:none;font-size:14px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transactions-table">
                        @foreach($allTransactions as $transaction)
                            <tr data-status="{{ $transaction->status }}" data-pending="{{ $transaction->status === 'pending' || $transaction->cancellation_requested_at ? 'true' : 'false' }}" style="border-top:1px solid var(--line)">
                                <td style="padding:16px 20px;font-weight:600;color:var(--forest-dark);border:none;font-size:15px">{{ $transaction->booking_code }}</td>
                                <td style="padding:16px 20px;color:var(--ink);border:none;font-size:14px">{{ $transaction->user->name }}</td>
                                <td style="padding:16px 20px;color:var(--muted);border:none;font-size:14px">{{ $transaction->start_date->format('d/m/Y') }}<br>sampai {{ $transaction->end_date->format('d/m/Y') }}</td>
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
                                                    <option value="pending" @selected($transaction->status === 'pending')>Pending</option>
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
        @endif
    </section>

    <section style="padding:30px 0;border-top:1px solid var(--line)">
        <div class="row mobile-stack" style="margin-bottom:20px">
            <div>
                <h2 style="font:700 21px Manrope,sans-serif;margin:0">Artikel panduan camping</h2>
                <p class="small-note">Kelola konten panduan untuk penyewa</p>
            </div>
            <button type="button" onclick="document.getElementById('add-article-form').style.display='block'" class="button small">+ Tambah Artikel</button>
        </div>

        <div id="add-article-form" style="display:none;margin-bottom:20px">
            <div class="panel">
                <h3 style="font:600 18px Manrope,sans-serif;margin:0 0 16px">Tambah artikel baru</h3>
                <form class="form-grid" method="post" action="{{ route('staff.articles.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="field" style="grid-column:1/-1">
                        <label for="new-article-title">Judul artikel</label>
                        <input id="new-article-title" name="title" required maxlength="180" placeholder="Tips Berkemah di Musim Hujan">
                    </div>
                    <div class="field" style="grid-column:1/-1">
                        <label for="new-article-excerpt">Ringkasan</label>
                        <textarea id="new-article-excerpt" name="excerpt" required maxlength="300" rows="2" placeholder="Ringkasan singkat untuk preview"></textarea>
                    </div>
                    <div class="field" style="grid-column:1/-1">
                        <label for="new-article-content">Konten lengkap</label>
                        <textarea id="new-article-content" name="content" required maxlength="12000" rows="6" placeholder="Tulis panduan lengkap di sini"></textarea>
                    </div>
                    <div class="field" style="grid-column:1/-1">
                        <label for="new-article-image">Gambar panduan (opsional)</label>
                        <input id="new-article-image" type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
                        <p class="small-note" style="margin:0">Format JPG, PNG, atau WEBP, maksimal 10 MB. Foto besar diperkecil otomatis sebelum diunggah.</p>
                        <p class="small-note image-note" style="margin:0;color:var(--forest)"></p>
                    </div>
                    <div class="field" style="grid-column:1/-1">
                        <label for="new-article-image-url">Atau tempel URL gambar (opsional)</label>
                        <input id="new-article-image-url" type="url" name="image_url" maxlength="2048" placeholder="https://contoh.com/gambar-panduan.jpg">
                    </div>
                    <label style="grid-column:1/-1">
                        <input type="checkbox" name="is_published" value="1" checked> 
                        Publikasikan artikel
                    </label>
                    <div style="grid-column:1/-1;display:flex;gap:10px">
                        <button class="button small" type="submit">Simpan artikel</button>
                        <button class="button secondary small" type="button" onclick="document.getElementById('add-article-form').style.display='none'">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        @if($articles->isEmpty())
            <div class="panel empty">Belum ada artikel panduan.</div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px">
                @foreach($articles as $article)
                    <div class="panel" style="padding:18px">
                        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:10px">
                            <h3 style="font:600 16px Manrope,sans-serif;margin:0;color:var(--forest-dark)">{{ $article->title }}</h3>
                            <span style="display:inline-block;padding:4px 8px;background:{{ $article->is_published ? 'var(--success)' : 'var(--warning)' }};color:white;border-radius:4px;font-size:11px;font-weight:700">
                                {{ $article->is_published ? 'PUBLISH' : 'DRAFT' }}
                            </span>
                        </div>
                        <p style="font-size:13px;color:var(--muted);margin:0 0 12px;line-height:1.5">{{ Str::limit($article->excerpt, 100) }}</p>
                        <button class="button secondary small" type="button" onclick="toggleArticleEdit('article-edit-{{ $article->id }}')" style="width:100%">Edit</button>

                        <div id="article-edit-{{ $article->id }}" style="display:none;margin-top:16px;padding-top:16px;border-top:2px solid var(--line)">
                            <form class="form-grid" method="post" action="{{ route('staff.articles.update', $article) }}" enctype="multipart/form-data">
                                @csrf 
                                @method('PUT')
                                @if($article->imageUrl())
                                    <div style="grid-column:1/-1">
                                        <img src="{{ $article->imageUrl() }}" alt="{{ $article->title }}" style="display:block;width:100%;max-width:260px;height:auto;border-radius:8px">
                                        <p class="small-note" style="margin:6px 0 0">Gambar saat ini. Unggah file baru atau isi URL untuk menggantinya.</p>
                                    </div>
                                @endif
                                <div class="field" style="grid-column:1/-1">
                                    <label for="article-title-{{ $article->id }}">Judul</label>
                                    <input id="article-title-{{ $article->id }}" name="title" value="{{ $article->title }}" required maxlength="180">
                                </div>
                                <div class="field" style="grid-column:1/-1">
                                    <label for="article-excerpt-{{ $article->id }}">Ringkasan</label>
                                    <textarea id="article-excerpt-{{ $article->id }}" name="excerpt" required maxlength="300" rows="2">{{ $article->excerpt }}</textarea>
                                </div>
                                <div class="field" style="grid-column:1/-1">
                                    <label for="article-content-{{ $article->id }}">Konten</label>
                                    <textarea id="article-content-{{ $article->id }}" name="content" required maxlength="12000" rows="6">{{ $article->content }}</textarea>
                                </div>
                                <div class="field" style="grid-column:1/-1">
                                    <label for="article-image-{{ $article->id }}">Ganti gambar panduan (opsional)</label>
                                    <input id="article-image-{{ $article->id }}" type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
                                    <p class="small-note" style="margin:0">Format JPG, PNG, atau WEBP, maksimal 10 MB. Foto besar diperkecil otomatis sebelum diunggah.</p>
                                    <p class="small-note image-note" style="margin:0;color:var(--forest)"></p>
                                </div>
                                <div class="field" style="grid-column:1/-1">
                                    <label for="article-image-url-{{ $article->id }}">Atau ganti dengan URL gambar (opsional)</label>
                                    <input id="article-image-url-{{ $article->id }}" type="url" name="image_url" maxlength="2048" placeholder="https://contoh.com/gambar-panduan.jpg">
                                </div>
                                <label style="grid-column:1/-1">
                                    <input type="checkbox" name="is_published" value="1" @checked($article->is_published)> 
                                    Publikasikan artikel
                                </label>
                                <div style="grid-column:1/-1;display:flex;gap:8px">
                                    <button class="button secondary small" type="submit">Simpan</button>
                                    <button class="button secondary small" type="button" onclick="toggleArticleEdit('article-edit-{{ $article->id }}')">Batal</button>
                                </div>
                            </form>
                            <form method="post" action="{{ route('staff.articles.destroy', $article) }}" onsubmit="return confirm('Hapus artikel ini?')" style="margin-top:12px">
                                @csrf 
                                @method('DELETE')
                                <button class="text-link" type="submit" style="border:0;background:none;color:#8b3b2d;font-size:13px">Hapus artikel</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>

@push('scripts')
<script>
function toggleArticleEdit(elementId) {
    const element = document.getElementById(elementId);
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}

function validateReason(textareaId, message) {
    const textarea = document.getElementById(textareaId);
    if (!textarea.value.trim()) {
        alert(message);
        textarea.focus();
        return false;
    }
    return confirm('Yakin menolak permintaan ini?');
}

function confirmAction(message) {
    return confirm(message);
}

function toggleEditDecision(formId) {
    const form = document.getElementById(formId);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function toggleTransactionEdit(elementId) {
    const element = document.getElementById(elementId);
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}

function filterTransactions(status) {
    const rows = document.querySelectorAll('#transactions-table tr');
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const isPending = row.getAttribute('data-pending') === 'true';
        
        if (status === 'all') {
            row.style.display = '';
        } else if (status === 'pending') {
            row.style.display = isPending ? '' : 'none';
        } else {
            row.style.display = rowStatus === status ? '' : 'none';
        }
    });
}

(function () {
    const MAX_DIMENSION = 1600;
    const RESIZE_ABOVE_BYTES = 1024 * 1024;

    function shrinkImage(file, done) {
        if (!file || !file.type.startsWith('image/') || file.size <= RESIZE_ABOVE_BYTES) {
            done(file);
            return;
        }

        const image = new Image();
        const objectUrl = URL.createObjectURL(file);

        image.onload = function () {
            const scale = Math.min(1, MAX_DIMENSION / Math.max(image.width, image.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(image.width * scale);
            canvas.height = Math.round(image.height * scale);
            const context = canvas.getContext('2d');
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.drawImage(image, 0, 0, canvas.width, canvas.height);
            URL.revokeObjectURL(objectUrl);
            canvas.toBlob(function (blob) {
                if (!blob) {
                    done(file);
                    return;
                }
                const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                done(new File([blob], name, { type: 'image/jpeg' }));
            }, 'image/jpeg', 0.85);
        };

        image.onerror = function () {
            URL.revokeObjectURL(objectUrl);
            done(file);
        };

        image.src = objectUrl;
    }

    document.querySelectorAll('input[name="image_file"]').forEach(function (input) {
        input.addEventListener('change', function () {
            const file = input.files && input.files[0];
            if (!file) {
                return;
            }

            const note = input.parentElement.querySelector('.image-note');
            shrinkImage(file, function (result) {
                if (result === file) {
                    return;
                }
                const transfer = new DataTransfer();
                transfer.items.add(result);
                input.files = transfer.files;
                if (note) {
                    note.textContent = 'Gambar diperkecil otomatis menjadi ' + Math.max(1, Math.round(result.size / 1024)) + ' KB. Nama file diubah menjadi JPG.';
                }
            });
        });
    });
})();
</script>
@endpush
@endsection
