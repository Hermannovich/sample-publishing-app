@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h1>{{ $article->title }}</h1>
            <div class="col-xs-12 col-md-6 thumbnail">
                @if($article->photo)
                    @if(starts_with($article->photo, 'http'))
                        <img src="{{ $article->photo }}" alt="{{ $article->title }}">
                    @else
                        <img src="{{ asset("/articles/$article->photo") }}" alt="{{ $article->title }}">
                    @endif
                @endif
            </div>
            <div class='col-xs-12 col-md-5 article-detail-content'>
                {!! $article->description !!}
                <div class="row article-meta-infos">
                    <div class="col-xs-12 col-md-8 article-meta">By {{ $article->user->name }}</div>
                    <div class="col-xs-12 col-md-4 article-meta publish-date"> {{ $article->publishDate()->diffForHumans() }} </div>
                </div>
                <div class='action'>
                    <a class="btn btn-info pull-right" href='{{ route('article.download', $article->slug) }}'>Download</a>
                </div>
            </div>
        </div>
    </div>
@endsection
