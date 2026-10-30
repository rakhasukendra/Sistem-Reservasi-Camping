@extends('layouts.app')
@section('title', 'Kelola Stok')
@section('content')
<div class="wrap" style="padding-top:100px;padding-bottom:60px">
    <div class="page-head row mobile-stack">
        <div>
            <p class="eyebrow">Operasional Rimba Sewa</p>
            <h1 class="section-title">Panel {{ auth()->user()->role }}</h1>
        </div>
        <span class="sample-tag">Transaksi dan pembayaran dummy</span>
    </div>

    <div style="display:flex;gap:8px;margin:20px 0;border-bottom:2px solid var(--line);overflow-x:auto">
        <a href="{{ route('staff.rentals') }}" style="padding:12px 20px;color:var(--muted);text-decoration:none">Kelola Sewa</a>
        <a href="{{ route('staff.inventory') }}" style="padding:12px 20px;font-weight:600;color:var(--forest);border-bottom:3px solid var(--forest);margin-bottom:-2px;text-decoration:none">Kelola Stok</a>
        <a href="{{ route('staff.articles') }}" style="padding:12px 20px;color:var(--muted);text-decoration:none">Edit Artikel</a>
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
</div>
@endsection
