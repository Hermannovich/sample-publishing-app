@extends('layouts.app')

@section('content')
    <div class="container container-card">
        <div class="row">
        @if($articles && $articles->count())
            @foreach($articles as $key => $article)
                <a href="{{ route('article.details', $article->slug) }}" title="{{ $article->title }}">
                  <div class="card thumbnail col-xs-2">
                    @if($article->photo)
                        @if(starts_with($article->photo, 'http'))
                            <img src="{{ $article->photo }}" alt="{{ $article->title }}">
                        @else
                            <img src="{{ asset("/articles/$article->photo") }}" alt="{{ $article->title }}">
                        @endif
                    @endif
                    <div class="content">
                      <h4><b>{{ $article->title }}</b></h4>
                      <p class="article-meta">By {{ $article->user->name }}</p>
                      <p>{{ $article->summary() }}</p>
                      <p class="article-meta pull-right"> {{ $article->publishDate()->diffForHumans() }}</p>
                    </div>
                  </div>  
                </a>
              @if(($key+1) % 5 == 0)
              <div class='clearfix'></div>
              @endif
            @endforeach
        @else
            <p class="text-center"> No Article yet. </p>
        @endif
        </div>
    </div>
@endsection
