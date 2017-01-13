@extends('layouts.app')

@section('inline_style')
<style>
    html, body {
        height: 100%;
    }

    body {
        margin: 0;
        padding: 0;
        width: 100%;
        color: #B0BEC5;
        display: table;
        font-weight: 100;
        font-family: 'Lato', sans-serif;
    }

    .container {
        text-align: center;
        display: table-cell;
        vertical-align: middle;
    }
    
    .container-error-message{
        width: 100%;
    }

    .content {
        text-align: center;
        display: inline-block;
    }

    .title {
        font-size: 72px;
        margin-bottom: 40px;
    }
</style>    
@endsection

@section('content')
    <div class="container container-error-message">
        <div class="content">
            <div class="title">{{ $message or 'Something bad happen. Please go back home.' }} </div>
        </div>
    </div>
@endsection