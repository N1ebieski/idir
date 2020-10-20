@extends(config('idir.layout') . '::admin.field.create')

@section('morphs')
@if ($groups->isNotEmpty())
<div class="form-group">
    <label for="morphs">
        {{ trans('idir::fields.groups') }}:
    </label>
    <select multiple class="form-control" id="morphs" name="morphs[]">
        @foreach ($groups as $group)
        <option value="{{ $group->id }}">{{ $group->name }}</option>
        @endforeach
    </select>
</div>
@endif
@endsection
