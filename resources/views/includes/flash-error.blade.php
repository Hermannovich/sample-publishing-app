@if (session('error'))
<div class="row">
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
</div>
@endif
