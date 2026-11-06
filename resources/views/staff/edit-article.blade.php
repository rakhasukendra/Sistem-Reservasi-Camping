@extends('layouts.app')
@section('title', 'Edit Artikel - ' . $article->title)
@section('content')
<div class="wrap" style="padding-top:100px;padding-bottom:60px">
    <div class="page-head row mobile-stack">
        <div>
            <p class="eyebrow">Operasional Rimba Sewa</p>
            <h1 class="section-title">Edit Artikel Panduan</h1>
            <p class="muted">Perbarui artikel: {{ $article->title }}</p>
        </div>
    </div>

    <div class="panel" style="max-width:700px;margin:20px auto 0;padding:24px">
        <form class="form-grid" method="post" action="{{ route('staff.articles.update', $article) }}" enctype="multipart/form-data">
            @csrf 
            @method('PUT')

            @if($article->imageUrl())
                <div style="grid-column:1/-1">
                    <img src="{{ $article->imageUrl() }}" alt="{{ $article->title }}" style="display:block;width:100%;max-width:400px;height:auto;border-radius:8px;margin-bottom:12px">
                    <p class="small-note" style="margin:0">Gambar saat ini. Unggah file baru atau isi URL untuk menggantinya.</p>
                </div>
            @endif

            <div class="field" style="grid-column:1/-1">
                <label for="title">Judul artikel</label>
                <input id="title" name="title" value="{{ old('title', $article->title) }}" required maxlength="180" placeholder="Tips Berkemah di Musim Hujan">
            </div>

            <div class="field" style="grid-column:1/-1">
                <label for="excerpt">Ringkasan</label>
                <textarea id="excerpt" name="excerpt" required maxlength="300" rows="2" placeholder="Ringkasan singkat untuk preview">{{ old('excerpt', $article->excerpt) }}</textarea>
            </div>

            <div class="field" style="grid-column:1/-1">
                <label for="content">Konten lengkap</label>
                <textarea id="content" name="content" required maxlength="12000" rows="8" placeholder="Tulis panduan lengkap di sini">{{ old('content', $article->content) }}</textarea>
            </div>

            <div class="field" style="grid-column:1/-1">
                <label for="image_file">Ganti gambar panduan (opsional)</label>
                <input id="image_file" type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
                <p class="small-note" style="margin:4px 0 0">Format JPG, PNG, atau WEBP, maksimal 10 MB. Foto besar diperkecil otomatis sebelum diunggah.</p>
                <p class="small-note image-note" style="margin:4px 0 0;color:var(--forest)"></p>
            </div>

            <div class="field" style="grid-column:1/-1">
                <label for="image_url">Atau ganti dengan URL gambar (opsional)</label>
                <input id="image_url" type="url" name="image_url" maxlength="2048" placeholder="https://contoh.com/gambar-panduan.jpg">
            </div>

            <label style="grid-column:1/-1;display:flex;align-items:center;gap:8px;font-size:14px">
                <input type="checkbox" name="is_published" value="1" @checked($article->is_published)> 
                Publikasikan artikel
            </label>

            <div style="grid-column:1/-1;display:flex;gap:10px;margin-top:16px">
                <button class="button small" type="submit">Simpan perubahan</button>
                <a href="{{ route('staff.articles') }}" class="button secondary small" style="text-align:center">Batal</a>
            </div>
        </form>

        <form method="post" action="{{ route('staff.articles.destroy', $article) }}" onsubmit="return confirm('Hapus artikel ini?')" style="margin-top:20px">
            @csrf 
            @method('DELETE')
            <button class="text-link" type="submit" style="border:0;background:none;color:#8b3b2d;font-size:13px;cursor:pointer">Hapus artikel ini</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
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

    const input = document.querySelector('input[name="image_file"]');
    if (input) {
        input.addEventListener('change', function () {
            const file = input.files && input.files[0];
            if (!file) {
                return;
            }

            const note = document.querySelector('.image-note');
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
    }
})();
</script>
@endpush
@endsection
