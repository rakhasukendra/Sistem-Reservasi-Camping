@extends('layouts.app')
@section('title', 'Edit Alat - ' . $item->name)
@section('content')
<div class="wrap" style="padding-top:100px;padding-bottom:60px">
    <div class="page-head row mobile-stack">
        <div>
            <p class="eyebrow">Operasional Rimba Sewa</p>
            <h1 class="section-title">Edit Alat Camping</h1>
            <p class="muted">Perbarui detail dan stok alat: {{ $item->name }}</p>
        </div>
    </div>

    <div class="panel" style="max-width:600px;margin:20px auto 0;padding:24px">
        <form class="form-grid" method="post" action="{{ route('staff.items.update', $item) }}">
            @csrf 
            @method('PUT')

            <div class="field">
                <label for="name">Nama alat</label>
                <input id="name" name="name" value="{{ old('name', $item->name) }}" required maxlength="120">
            </div>

            <div class="field">
                <label for="category">Kategori</label>
                <input id="category" name="category" value="{{ old('category', $item->category) }}" required maxlength="80">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="field">
                    <label for="stock_total">Stok total</label>
                    <input id="stock_total" type="number" min="0" name="stock_total" value="{{ old('stock_total', $item->stock_total) }}" required>
                </div>

                <div class="field">
                    <label for="stock_damaged">Stok rusak</label>
                    <input id="stock_damaged" type="number" min="0" name="stock_damaged" value="{{ old('stock_damaged', $item->stock_damaged) }}" required>
                </div>
            </div>

            <div class="field">
                <label for="daily_rate">Tarif harian (Rp)</label>
                <input id="daily_rate" type="number" min="0" name="daily_rate" value="{{ old('daily_rate', $item->daily_rate) }}" required>
            </div>

            <div class="field">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" required rows="5" maxlength="2000">{{ old('description', $item->description) }}</textarea>
            </div>

            <label style="display:flex;align-items:center;gap:8px;font-size:14px">
                <input type="checkbox" name="is_available" value="1" @checked($item->is_available)> 
                Tersedia untuk disewa
            </label>

            <div style="grid-column:1/-1;display:flex;gap:10px;margin-top:16px">
                <button class="button small" type="submit">Simpan perubahan</button>
                <a href="{{ route('staff.inventory') }}" class="button secondary small" style="text-align:center">Batal</a>
            </div>
        </form>

        <form method="post" action="{{ route('staff.items.destroy', $item) }}" onsubmit="return confirm('Hapus alat ini? Semua riwayat reservasi akan tetap tersimpan.')" style="margin-top:20px">
            @csrf 
            @method('DELETE')
            <button class="text-link" type="submit" style="border:0;background:none;color:#8b3b2d;font-size:13px;cursor:pointer">Hapus alat ini</button>
        </form>
    </div>
</div>
@endsection
