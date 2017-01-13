@extends('layouts.app')

@section('content')
    <div class="container container-card">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h1>Publish an article</h1>
                    </div>
                    <div class="panel-body">
                        <form enctype="multipart/form-data" class="col-xs-12 col-md-8 col-md-offset-2" role="form" method="POST" action="{{ route('publish.save') }}">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title" class="control-label">Title</label>

                                <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}" required autofocus>

                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="control-label">Description</label>

                                <textarea id="description" class="form-control" required name="description" placeholder="Article's description here..." >{{ old('description') }}</textarea>

                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="form-group{{ $errors->has('summary') ? ' has-error' : '' }}">
                                <label for="summary" class="control-label">Summary</label>

                                <textarea id="summary" row="6" class="form-control" required name="summary" placeholder="Article's summary here..." >{{ old('summary') }}</textarea>
                                <p class="help-block">{{ 'Summary should be less than ' . config('articles.excerpt_content') . ' chars.' }}</p>
                                @if ($errors->has('summary'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('summary') }}</strong>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                                <label for="photo" class="control-label">Photo</label>

                                <input type="file" id="photo" name='photo'>
                                
                                <p class="help-block">Photo accepted should be a PNG, JPG/JPEG/JPE or and GIF.</p>
    
                                @if ($errors->has('photo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('photo') }}</strong>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"> Publish </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script src='{{ asset('/js/jquery-3.1.1.min.js') }}'></script>
    <script src='{{ asset('/js/ckeditor/ckeditor.js') }}'></script>
@endsection

@section('inline_scripts')
<script type="text/javascript">
    jQuery(document).ready(function () {
        // Turn off automatic editor initilization.
        // Used to prevent conflictions with multiple text
        // editors being displayed on the same page.
        CKEDITOR.disableAutoInline = true;
        CKEDITOR.replace('description', {
          height: 300
        });
    });
</script>
@endsection
