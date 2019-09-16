@if (session()->has('success'))
<div class="alert alert-success alert-time" role="alert">
    {{ session()->get('success') }}
</div>
@endif

@if (session()->has('warning'))
<div class="alert alert-warning alert-time" role="alert">
    {{ session()->get('warning') }}
</div>
@endif

@if (session()->has('danger'))
<div class="alert alert-danger alert-time" role="alert">
    {{ session()->get('danger') }}
</div>
@endif

@if (session()->has('alertErrors') && $errors->any())
<div class="alert alert-danger alert-time" role="alert">
    <ul class="list-unstyled mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
