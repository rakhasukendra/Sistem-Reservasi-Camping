@extends('layouts.app')
@section('title', 'Edit Artikel')
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
        <a href="{{ route('staff.inventory') }}" style="padding:12px 20px;color:var(--muted);text-decoration:none">Kelola Stok</a>
        <a href="{{ route('staff.articles') }}" style="padding:12px 20px;font-weight:600;color:var(--forest);border-bottom:3px solid var(--forest);margin-bottom:-2px;text-decoration:none">Edit Artikel</a>
    </div>

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
            if (!context) {
                URL.revokeObjectURL(objectUrl);
                done(file);
                return;
            }

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
