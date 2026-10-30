@extends('layouts.app')
@section('title', 'Rimba Sewa')
@section('content')

<section class="hero-section">
    <div class="hero-overlay-black"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="wrap">
        <div class="hero-text">
            <p class="hero-eyebrow">Siap Menjelajah, Tanpa Ribet.</p>
            <h1 class="hero-title">Sewa Gear Outdoor<br>Kini Lebih Mudah!</h1>
            <div class="hero-features">
                <div class="hero-feature">
                    <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Stok Real-Time & Lengkap</span>
                </div>
                <div class="hero-feature">
                    <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Praktis & Cepat</span>
                </div>
                <div class="hero-feature">
                    <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Harga Terjangkau</span>
                </div>
            </div>
            <div class="hero-cta">
                <a href="#alat" class="button" style="font-size:16px;padding:12px 32px">Lihat Katalog</a>
            </div>
        </div>
        </div>
    </div>
</section>

<div class="wrap">
<section id="alat" style="scroll-margin-top:100px;padding:60px 0 40px">
    <div style="text-align:center;margin-bottom:40px">
        <h2 class="section-title">Pilihan Terbaik Untukmu!</h2>
        <p class="muted" style="margin:12px 0 0">Gear pilihan untuk petualangan outdoor impianmu</p>
    </div>

    @if($items->isEmpty())
        <div class="panel empty">Belum ada perlengkapan aktif.</div>
    @else
        <div class="product-grid">
            @foreach($items as $item)
                @php
                    $name = strtolower($item->name . ' ' . $item->category);
                    $itemImage = asset('images/alat-camping.jpg');
                    
                    if(strpos($name, 'tenda') !== false || strpos($name, 'tent') !== false) {
                        $itemImage = asset('images/alat-tenda.jpg');
                    }
                    elseif(strpos($name, 'carrier') !== false || strpos($name, 'backpack') !== false || strpos($name, 'tas') !== false) {
                        $itemImage = asset('images/alat-carrier.jpg');
                    }
                    elseif(strpos($name, 'kompor') !== false || strpos($name, 'stove') !== false || strpos($name, 'masak') !== false || strpos($name, 'portable') !== false) {
                        $itemImage = asset('images/alat-kompor.jpg');
                    }
                    elseif(strpos($name, 'matras') !== false || strpos($name, 'mattress') !== false) {
                        $itemImage = asset('images/alat-matras.jpg');
                    }
                    elseif(strpos($name, 'sleeping') !== false || strpos($name, 'tidur') !== false || strpos($name, 'bag') !== false || strpos($name, 'hangat') !== false) {
                        $itemImage = asset('images/alat-sleeping-bag.jpg');
                    }
                    elseif(strpos($name, 'lampu') !== false || strpos($name, 'senter') !== false || strpos($name, 'headlamp') !== false || strpos($name, 'flashlight') !== false) {
                        $itemImage = asset('images/alat-headlamp.jpg');
                    }
                    elseif(strpos($name, 'camping') !== false) {
                        $itemImage = asset('images/alat-camping.jpg');
                    }
                    elseif(strpos($name, 'sepatu') !== false || strpos($name, 'boots') !== false || strpos($name, 'shoes') !== false) {
                        $itemImage = asset('images/alat-sepatu.jpg');
                    }
                    elseif(strpos($name, 'jas hujan') !== false || strpos($name, 'raincoat') !== false || strpos($name, 'poncho') !== false || strpos($name, 'rain jacket') !== false || strpos($name, 'jaket') !== false || strpos($name, 'jacket') !== false) {
                        $itemImage = asset('images/alat-jas-hujan.jpg');
                    }
                    elseif(strpos($name, 'pole') !== false || strpos($name, 'trekking') !== false || strpos($name, 'tongkat') !== false) {
                        $itemImage = asset('images/alat-trekking-pole.jpg');
                    }
                @endphp
                <div class="panel product-card">
                    <div class="product-image">
                        <img src="{{ $itemImage }}" alt="{{ $item->name }}" width="800" height="640" loading="lazy" decoding="async" onerror="this.parentElement.style.background='var(--sand)';this.style.display='none'">
                    </div>
                    <div class="product-badge">{{ $item->category }}</div>
                    <div class="product-body">
                        <h3 class="product-title">{{ $item->name }}</h3>
                        <p class="product-desc">{{ Str::limit($item->description, 75) }}</p>
                        <div class="product-footer">
                            <div class="product-price">Rp{{ number_format($item->daily_rate,0,',','.') }}/hari</div>
                            <div class="product-stock">{{ max(0,$item->stock_total-$item->stock_damaged) }} unit</div>
                        </div>
                        @auth
                            @if (! in_array(auth()->user()->role, ['admin', 'staff'], true))
                                <div class="product-actions">
                                    <form method="post" action="{{ route('cart.add', $item) }}" style="flex:1">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="button small" type="submit" style="width:100%;font-size:13px">+ Keranjang</button>
                                    </form>
                                </div>
                            @endif
                        @else
                            <div class="product-actions">
                                <a class="button secondary" href="{{ route('login') }}" style="width:100%;text-align:center;font-size:13px">Pesan Sekarang</a>
                            </div>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<section style="padding:60px 0">
    <div style="text-align:center;margin-bottom:40px">
        <h2 class="section-title">Keunggulan Rimba Sewa</h2>
    </div>
    <div class="features-grid">
        <div class="feature-item">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068"/>
                </svg>
            </div>
            <h3>Stok Terupdate</h3>
            <p>Data real-time memastikan ketersediaan terjamin</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0"/>
                </svg>
            </div>
            <h3>Proses Cepat</h3>
            <p>Konfirmasi dalam hitungan jam, bukan hari</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon" style="font-size:24px;font-weight:800;font-family:Manrope,sans-serif">
                Rp
            </div>
            <h3>Harga Kompetitif</h3>
            <p>Tarif terbaik dengan kualitas terjamin</p>
        </div>
    </div>
</section>

@if (! auth()->check() || ! in_array(auth()->user()->role, ['admin', 'staff'], true))
<section id="cara-pesan" class="booking-guide" style="scroll-margin-top:100px">
    <div class="booking-guide-heading">
        <h2 class="section-title">Cara Memesan</h2>
        <p class="muted">Ikuti langkah ini untuk mengajukan penyewaan perlengkapan.</p>
    </div>
    <ol class="booking-steps">
        <li>
            <span class="booking-step-number">01</span>
            <div><h3>Masuk atau buat akun</h3><p>Masuk ke akun penyewa. Jika belum punya akun, daftar terlebih dahulu.</p></div>
        </li>
        <li>
            <span class="booking-step-number">02</span>
            <div><h3>Pilih perlengkapan</h3><p>Di katalog, tekan "+ Keranjang" atau "Sewa" pada alat yang ingin Anda pinjam.</p></div>
        </li>
        <li>
            <span class="booking-step-number">03</span>
            <div><h3>Periksa keranjang</h3><p>Sesuaikan jumlah unit atau hapus alat yang tidak diperlukan, lalu lanjutkan ke reservasi.</p></div>
        </li>
        <li>
            <span class="booking-step-number">04</span>
            <div><h3>Isi tanggal dan kontak</h3><p>Tentukan tanggal mulai dan selesai, lengkapi nama serta kontak, lalu kirim permintaan. Ketersediaan stok akan diperiksa untuk tanggal tersebut.</p></div>
        </li>
        <li>
            <span class="booking-step-number">05</span>
            <div><h3>Tunggu konfirmasi staf</h3><p>Pantau status permintaan di halaman Profil &amp; sewa. Reservasi baru berstatus menunggu konfirmasi.</p></div>
        </li>
    </ol>
    <div class="booking-guide-action">
        <a href="#alat" class="button">Pilih perlengkapan</a>
        <span class="small-note">Belum punya akun? <a href="{{ route('register') }}" class="text-link">Daftar sebagai penyewa</a></span>
    </div>
</section>
@endif

@if($articles->isNotEmpty())
<section id="panduan" style="scroll-margin-top:100px;padding:60px 0">
    <div style="text-align:center;margin-bottom:40px">
        <h2 class="section-title">Panduan Camping</h2>
        <p class="muted" style="margin:12px 0 0">Tips dan panduan dari tim Rimba Sewa sebelum Anda berangkat</p>
    </div>
    <div class="articles-grid">
        @foreach($articles as $article)
            @php
                $articleTopic = strtolower($article->title.' '.$article->excerpt);
                $articleImageMap = [
                    'tenda' => asset('images/panduan-tenda.jpg'),
                    'tidur' => asset('images/panduan-tidur.jpg'),
                    'masak' => asset('images/panduan-masak.jpg'),
                    'kompor' => asset('images/panduan-masak.jpg'),
                    'makan' => asset('images/panduan-masak.jpg'),
                    'checklist' => asset('images/panduan-checklist.jpg'),
                    'perlengkapan' => asset('images/panduan-checklist.jpg'),
                    'packing' => asset('images/panduan-checklist.jpg'),
                    'hujan' => asset('images/panduan-hujan.jpg'),
                    'cuaca' => asset('images/panduan-hujan.jpg'),
                    'jalur' => asset('images/panduan-hiking.jpg'),
                    'hiking' => asset('images/panduan-hiking.jpg'),
                    'pendakian' => asset('images/panduan-hiking.jpg'),
                    'glamping' => asset('images/panduan-glamping.jpg'),
                    'minimalis' => asset('images/panduan-glamping.jpg'),
                    'malam' => asset('images/panduan-malam.jpg'),
                    'lampu' => asset('images/panduan-malam.jpg'),
                ];
                $articleImage = $article->imageUrl();
                if (! $articleImage) {
                    foreach ($articleImageMap as $keyword => $imageUrl) {
                        if (str_contains($articleTopic, $keyword)) {
                            $articleImage = $imageUrl;
                            break;
                        }
                    }
                }
                $articleFallbackImages = [
                    asset('images/panduan-alam.jpg'),
                    asset('images/panduan-malam.jpg'),
                    asset('images/panduan-hutan.jpg'),
                    asset('images/panduan-glamping.jpg'),
                ];
                $articleImage ??= $articleFallbackImages[$article->id % count($articleFallbackImages)];
            @endphp
            <article class="article-card">
                <div class="article-image">
                    <img src="{{ $articleImage }}" alt="{{ $article->title }}" width="800" height="640" loading="lazy" decoding="async" onerror="this.parentElement.style.background='var(--sand)';this.style.display='none'">
                </div>
                <div class="article-body">
                    <p class="eyebrow" style="margin-bottom:8px">Panduan</p>
                    <h3>{{ $article->title }}</h3>
                    <p>{{ $article->excerpt }}</p>
                    <a class="read-more" href="{{ route('articles.show', $article) }}">Baca panduan →</a>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif

<section style="padding:60px 0">
    <div style="text-align:center;margin-bottom:40px">
        <h2 class="section-title">Kepuasan Pelanggan</h2>
    </div>
    <div class="testimonials-grid">
        <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p>"Peralatan berkualitas dengan harga sangat terjangkau. Pelayanan admin super ramah!"</p>
            <strong>- Lucene</strong>
        </div>
        <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p>"Proses booking cepat, barang bagus, dan staf sangat membantu memberikan rekomendasi."</p>
            <strong>- Kaname</strong>
        </div>
        <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p>"Sudah langganan berkali-kali. Kualitas barang konsisten dan puas dengan layanannya!"</p>
            <strong>- Aditya</strong>
        </div>
    </div>
</section>
</div>

@push('head')
<link rel="preload" as="image" href="{{ asset('images/hero-camping.webp') }}" type="image/webp" fetchpriority="high">
<style>
body.home-page .main{padding-top:0}
body.home-page .topbar{background:transparent!important;border:none!important;box-shadow:none!important}
body.home-page .topbar.scrolled{background:rgba(255,255,255,0.98)!important;border-bottom:1px solid var(--line)!important;box-shadow:0 2px 8px rgba(0,0,0,0.08)!important}
body.home-page .topbar.scrolled{background:rgba(255,255,255,0.98)!important;border-bottom:1px solid var(--line)!important;box-shadow:0 2px 8px rgba(0,0,0,0.08)!important}
body.home-page .topbar .brand,
body.home-page .topbar .navlinks a:not(.button),
body.home-page .topbar .nav-form button{color:#fff;text-shadow:0 1px 5px rgba(0,0,0,0.85);transition:all 0.3s ease}
body.home-page .topbar.scrolled .brand,
body.home-page .topbar.scrolled .navlinks a:not(.button),
body.home-page .topbar.scrolled .nav-form button{color:var(--ink);text-shadow:none}
body.home-page .topbar .brand small{color:rgba(255,255,255,0.94);transition:all 0.3s ease}
body.home-page .topbar.scrolled .brand small{color:var(--muted)}
body.home-page .topbar .nav-account{color:#fff!important;transition:all 0.3s ease}
body.home-page .topbar.scrolled .nav-account{color:var(--ink)!important}
body.home-page .topbar .nav-account:hover{background:rgba(255,255,255,0.14)}
body.home-page .topbar.scrolled .nav-account:hover{background:var(--sand)}
body.home-page .topbar .nav-account-copy small{color:rgba(255,255,255,0.94);transition:all 0.3s ease}
body.home-page .topbar.scrolled .nav-account-copy small{color:var(--muted)}
body.home-page .topbar .nav-avatar{background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.4);transition:all 0.3s ease}
body.home-page .topbar.scrolled .nav-avatar{background:var(--forest);border:none}
body.home-page .topbar .navlinks a:not(.button):not(.nav-account)::after{background:#fff}
body.home-page .topbar.scrolled .navlinks a:not(.button):not(.nav-account)::after{background:var(--forest)}
body.home-page .topbar .navlinks .button{color:#fff!important;background:var(--forest-dark);text-shadow:none}
.hero-section{position:relative;height:100vh;display:flex;align-items:center;justify-content:center;background:url('{{ asset('images/hero-camping.jpg') }}') center/cover;background-image:image-set(url('{{ asset('images/hero-camping.webp') }}') type('image/webp'),url('{{ asset('images/hero-camping.jpg') }}') type('image/jpeg'));color:white;padding:0;margin:0;width:100vw;margin-left:calc(-50vw + 50%);padding-top:80px}
.hero-overlay-black{position:absolute;inset:0;background:rgba(0,0,0,0.35);z-index:0}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(9,17,14,0.5) 0%,rgba(9,17,14,0.15) 45%,rgba(9,17,14,0.4) 100%);z-index:0}
.hero-content{position:relative;z-index:1;display:flex;align-items:center;justify-content:center;height:100%;width:100%}
.hero-content .wrap{width:100%;display:flex;align-items:center;justify-content:center}
.hero-text{max-width:700px;text-align:center}
.hero-eyebrow{color:#fff;font-size:13px;font-weight:700;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;opacity:1;text-shadow:0 2px 10px rgba(0,0,0,0.7)}
.hero-title{font:700 56px Manrope,sans-serif;line-height:1.1;margin:0 0 32px;word-spacing:-0.05em;text-shadow:0 3px 15px rgba(0,0,0,0.8),0 1px 3px rgba(0,0,0,0.9)}
.hero-features{display:flex;flex-direction:column;gap:16px;margin:32px 0}
.hero-feature{display:flex;align-items:center;gap:12px;font-size:15px;font-weight:600;opacity:1;text-shadow:0 2px 8px rgba(0,0,0,0.7);color:#eafff0}
.check-icon{width:26px;height:26px;color:#98f0a0;flex-shrink:0;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.5))}
.hero-cta{margin-top:40px}
.hero-cta .button{background:#2d5f3f;color:#fff;border:2px solid #3a7a52;font-weight:700;font-size:17px;padding:14px 40px;box-shadow:0 4px 15px rgba(0,0,0,0.5);transition:all 0.3s}
.hero-cta .button:hover{background:#3a7a52;border-color:#4a9a65;transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.6)}
.product-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));align-items:stretch;gap:24px}
.product-card{display:flex;flex-direction:column;min-width:0;padding:0;overflow:hidden;position:relative;border-radius:12px;transition:box-shadow 0.2s,transform 0.2s}
.product-card:hover{box-shadow:0 8px 20px rgba(0,0,0,0.1);transform:translateY(-2px)}
.product-image{height:220px;flex-shrink:0;overflow:hidden;background:var(--sand)}
.product-image img{width:100%;height:100%;object-fit:cover;transition:transform 0.3s}
.product-card:hover .product-image img{transform:scale(1.03)}
.product-badge{position:absolute;top:14px;right:14px;max-width:calc(100% - 28px);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;background:var(--accent);color:white;padding:6px 14px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;z-index:2}
.product-body{padding:20px;flex:1;display:flex;flex-direction:column;background:white}
.product-title{font:600 18px Manrope,sans-serif;min-height:2.8em;margin:0 0 8px;color:var(--ink);line-height:1.4}
.product-desc{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:3;overflow:hidden;min-height:4.5em;font-size:13px;color:var(--muted);margin:0 0 14px;line-height:1.5}
.product-footer{display:flex;justify-content:space-between;align-items:baseline;gap:10px;padding-bottom:12px;border-bottom:1px solid var(--line);margin-top:auto;margin-bottom:12px}
.product-price{font:700 16px Manrope,sans-serif;color:var(--forest-dark)}
.product-stock{flex-shrink:0;font-size:12px;color:var(--muted);font-weight:500}
.product-actions{display:flex;gap:10px}
.product-actions .button{min-height:44px;padding:9px 12px}
.features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:40px;background:linear-gradient(135deg,#f5f5f4 0%,var(--sand) 100%);padding:40px;border-radius:16px}
.booking-guide{margin:16px 0 40px;padding:36px 40px;background:var(--sand);border-radius:12px}
.booking-guide-heading{text-align:center}
.booking-guide-heading .muted{margin:10px 0 0}
.booking-steps{max-width:850px;margin:28px auto 0;padding:0;list-style:none}
.booking-steps li{display:grid;grid-template-columns:42px minmax(0,1fr);gap:18px;padding:18px 0;border-bottom:1px solid var(--line)}
.booking-steps li:last-child{border-bottom:0}
.booking-step-number{font:700 15px Manrope,sans-serif;color:var(--forest);padding-top:2px}
.booking-steps h3{font:700 16px Manrope,sans-serif;color:var(--ink);margin:0 0 4px}
.booking-steps p{font-size:14px;line-height:1.6;color:var(--muted);margin:0}
.booking-guide-action{display:flex;align-items:center;justify-content:center;gap:18px;flex-wrap:wrap;margin-top:22px}
.feature-item{text-align:center}
.feature-item:hover{transform:none}
.feature-icon{width:80px;height:80px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;box-shadow:0 4px 12px rgba(0,0,0,0.08);stroke:var(--forest);stroke-width:2}
.feature-item:hover .feature-icon{background:var(--forest);color:white}
.feature-item h3{font:700 18px Manrope,sans-serif;margin:0 0 10px;color:var(--ink)}
.feature-item p{font-size:13px;color:var(--muted);margin:0;line-height:1.6}
.articles-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:24px}
.article-card{overflow:hidden;border-radius:12px;background:white;box-shadow:var(--shadow);transition:box-shadow 0.2s}
.article-card:hover{box-shadow:0 8px 20px rgba(0,0,0,0.1)}
.article-image{height:180px;overflow:hidden;background:var(--sand)}
.article-image img{width:100%;height:100%;object-fit:cover}
.article-body{padding:24px}
.article-body h3{font:600 18px Manrope,sans-serif;margin:0 0 12px;color:var(--ink)}
.article-body p{font-size:13px;color:var(--muted);margin:0 0 16px;line-height:1.6}
.read-more{color:var(--accent);font-weight:600;transition:color 0.2s}
.read-more:hover{color:var(--accent-hover)}
.testimonials-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px}
.testimonial-card{background:var(--sand);padding:28px;border-radius:12px;transition:box-shadow 0.2s}
.testimonial-card:hover{box-shadow:0 6px 16px rgba(0,0,0,0.08)}
.testimonial-card .stars{font-size:14px;color:var(--accent);margin-bottom:12px;letter-spacing:2px}
.testimonial-card p{font-size:14px;color:var(--muted);margin:0 0 14px;font-style:italic;line-height:1.7}
.testimonial-card strong{font-size:13px;color:var(--forest-dark)}
@media(max-width:1024px){.hero-section{padding-top:60px}.hero-title{font-size:44px}.product-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:768px){.hero-section{padding-top:60px}.hero-title{font-size:32px!important}.hero-features{flex-direction:column;gap:12px}.product-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.product-image{height:180px}.product-body{padding:16px}.product-title{font-size:16px}.product-desc{font-size:12px}.features-grid{padding:30px 20px;gap:30px}.booking-guide{padding:28px 22px}.booking-steps li{grid-template-columns:34px minmax(0,1fr);gap:12px}.feature-icon{width:64px;height:64px}.articles-grid{grid-template-columns:1fr!important}.testimonials-grid{grid-template-columns:1fr!important}}
@media(max-width:480px){.hero-section{padding-top:60px}.hero-title{font-size:28px!important}.hero-eyebrow{font-size:11px}.product-grid{grid-template-columns:1fr}.product-image{height:210px}.product-body{padding:18px}.product-title{font-size:17px}.product-desc{font-size:13px}.features-grid{padding:20px 16px}.feature-item h3{font-size:14px}}
</style>
@endpush

@endsection
