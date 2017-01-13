@extends('layouts.app')

@section('content')
    <div class="container container-card">
        @include('includes.flash-error')
        @include('includes.flash-success')
        <div class="row">
            <h1>Vos Articles</h1>
            <div class="col-xs-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Picture</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Publish Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if($articles->count())
                        @foreach($articles as $article)
                        <tr>
                            <td class='thumbnail col-xs-2'>
                                @if($article->photo)
                                    @if(starts_with($article->photo, 'http'))
                                        <img src="{{ $article->photo }}" alt="{{ $article->title }}">
                                    @else
                                        <img src="{{ asset("/articles/$article->photo") }}" alt="{{ $article->title }}">
                                    @endif
                                @endif
                            </td>
                            <td class='col-xs-3 title'>{{ $article->title }}</td>
                            <td class='col-xs-3'>{{ $article->summary() }}</td>
                            <td class='col-xs-2'>{{ $article->publishDate() }}</td>
                            <td class='col-xs-2'>
                                <a class="btn btn-danger pull-right" onclick="return confirm('Do you really want to delete this article?');" href="{{ route('article.delete', ['id'=> $article->id])  }}">
                                    Delete
                                </a>
                                <a class="btn btn-info pull-left" href="{{ route('article.details', $article->slug)  }}">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="4">
                            <div class="text-center no-items">
                                <h2>{{ 'You have no article yet.' }}</h2>
                                <br>
                                <a class="btn btn-primary" href="{{ route('publish') }}">Publish an article</a>
                            </div>
                        </td>
                    </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            @if($articles->count())
            <div class="col-lg-12">
                <div class="text-center">
                    {!! $articles->setPath('')->appends(request()->query())->render() !!}      
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
