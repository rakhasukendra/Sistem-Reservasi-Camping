@extends('layouts.app')
@section('title', $article->title)
@section('content')
<div class="wrap" style="padding-top:100px">
<article class="panel" style="max-width:820px;margin:10px auto;padding:clamp(22px,5vw,52px)">
    <p class="eyebrow">Panduan camping</p><h1 class="section-title" style="font-size:clamp(30px,5vw,44px)">{{ $article->title }}</h1><p class="muted">{{ $article->excerpt }}</p>
    <div class="article-copy">{{ $article->content }}</div>
    <p style="margin-top:30px"><a class="button secondary" href="{{ route('home') }}#panduan">Kembali ke panduan</a></p>
</article>
</div>
@endsection