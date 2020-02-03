<div>
    @if (isset($value['title']) && $value['title'] !== null)
    <p>
        {{ trans('idir::dirs.title') }}:<br>
        <span>{{ $value['title'] }}</span>
    </p>
    @endif
    @if (isset($value['content_html']) && $value['content_html'] !== null)
    <p>
        {{ trans('idir::dirs.content') }}:<br>
        <span>{!! $value['content_html'] !!}</span>
    </p>
    @endif
    @if (isset($value['notes']) && $value['notes'] !== null)
    <p>
        {{ trans('idir::dirs.notes') }}:<br>
        <span>{{ $value['notes'] }}</span>
    </p>
    @endif
    @if (isset($value['tags']) && $value['tags'] !== null)
    <p>
        {{ trans('idir::dirs.tags') }}:<br>
        <span>{{ implode(', ', $value['tags']) }}</span>
    </p>
    @endif
    @if (isset($value['url']) && $value['url'] !== null)
    <p>
        {{ trans('idir::dirs.url') }}:<br>
        <span><a href="{{ $value['url'] }}" target="_blank">{{ $value['url'] }}</a></span>
    </p>
    @endif
    @if ($categories->isNotEmpty())
    <div>
        {{ trans('idir::dirs.categories') }}:<br>
        <ul class="pl-3">
        @foreach ($categories as $category)
            <li>
            @if ($category->ancestors->count() > 0)
                @foreach ($category->ancestors as $ancestor)
                    {{ $ancestor->name }} &raquo;
                @endforeach
            @endif
                <strong>{{ $category->name }}</strong>
            </li>
        @endforeach
        </ul>
    </div>
    @endif
    @if ($group->fields->isNotEmpty())
    <div>
    @foreach ($group->fields as $field)
        @if (isset($value['field'][$field->id]) && !empty($value['field'][$field->id]))
            <p>
                {{ $field->title }}:<br>
                <span>
                @if (in_array($field->type, ['input', 'textarea', 'select']))
                    {{ $value['field'][$field->id] }}
                @elseif (in_array($field->type, ['multiselect', 'checkbox']))
                    {{ implode(', ', $value['field'][$field->id]) }}
                @elseif ($field->type === 'regions')
                    {{ implode(', ', $regions->whereIn('id', $value['field'][$field->id])->pluck('name')->toArray())) }}                    
                @else
                    <img class="img-fluid" src="{{ Storage::url($value['field'][$field->id]) }}">
                @endif
                </span>
            </p>
        @endif
    @endforeach
    </div>
    @endif
</div>
