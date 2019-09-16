@if (session()->has('success'))
<div class="container alert-absolute">
    <div class="alert alert-success alert-time" role="alert">
      {{ session()->get('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
</div>
@endif
