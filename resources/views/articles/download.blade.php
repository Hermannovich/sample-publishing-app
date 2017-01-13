<!DOCTYPE HTML>
<!--[if gt IE 8]> <html class="ie9" lang="en"> <![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<title>{{ config('app.name') }} | {{ $article->title }}</title>
<meta name="description" content=" Article : {{ $article->title }}">     
    <style type="text/css">
        body {
            padding-top: 20px;
            padding-bottom: 40px;
        }

        thead:before, thead:after { display: none; }
        tbody:before, tbody:after { display: none; }
    </style>
</head>
<body>

<h1>{{ $article->title }}</h1>
<table style="width: 100%;" >
    <tbody>		
        <tr>
            <td style="text-align: left; width: 100%">
                @if($article->photo)
                    @if(starts_with($article->photo, 'http'))
                        <img src="{{ $article->photo }}" alt="{{ $article->title }}">
                    @else
                        <img src="{{ asset("/articles/$article->photo") }}" alt="{{ $article->title }}">
                    @endif
                @endif
                <p style="margin-top: 50px;">{{ $article->description }}</p>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>
