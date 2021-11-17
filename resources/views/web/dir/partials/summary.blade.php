<div>
    @if (isset($value['title']) && $value['title'] !== null)
    <p>
        <span>{{ trans('idir::dirs.title') }}:</span><br>
        <span>{{ $value['title'] }}</span>
    </p>
    @endif
    @if (isset($value['content_html']) && $value['content_html'] !== null)
    <p>
        <span>{{ trans('idir::dirs.content') }}:</span><br>
        <span>
            {!! $group->hasEditorPrivilege() ? 
                $value['content_html'] 
                : nl2br(e($value['content_html'])) 
            !!}
        </span>
    </p>
    @endif
    @if (isset($value['notes']) && $value['notes'] !== null)
    <p>
        <span>{{ trans('idir::dirs.notes') }}:</span><br>
        <span>{{ $value['notes'] }}</span>
    </p>
    @endif
    @if (isset($value['tags']) && $value['tags'] !== null)
    <p>
        <span>{{ trans('idir::dirs.tags.label') }}:</span><br>
        <span>{{ implode(', ', $value['tags']) }}</span>
    </p>
    @endif
    @if (isset($value['url']) && $value['url'] !== null)
    <p>
        <span>{{ trans('idir::dirs.url') }}:</span><br>
        <span>
            <a 
                href="{{ $value['url'] }}" 
                target="_blank"
                rel="noopener"
            >
                {{ $value['url'] }}
            </a>
        </span>
    </p>
    @endif
    @if ($categories->isNotEmpty())
    <div>
        <span>{{ trans('idir::dirs.categories') }}:</span><br>
        <ul class="pl-3">
        @foreach ($categories as $category)
            <li>
                @if ($category->ancestors->count() > 0)
                @foreach ($category->ancestors as $ancestor)
                <span>{{ $ancestor->name }} &raquo;</span>
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
                <span>{{ $field->title }}:</span><br>
                <span>
                @switch($field->type)
                    @case('input')
                    @case('textarea')
                    @case('select')
                        {{ $value['field'][$field->id] }}
                        @break;

                    @case('multiselect')
                    @case('checkbox')
                        {{ implode(', ', $value['field'][$field->id]) }}
                        @break;

                    @case('regions')
                        {{ implode(', ', $regions->whereIn('id', $value['field'][$field->id])->pluck('name')->toArray()) }}
                        @break;

                    @case('map')
                        @render('idir::map.dir.mapComponent', [
                            'coords_marker' => [
                                [$value['field'][$field->id][0]['lat'], $value['field'][$field->id][0]['long']]
                            ]
                        ])
                        @break;                        

                    @case('image')
                        <img 
                            class="img-fluid" 
                            src="{{ app('filesystem')->url($value['field'][$field->id]) }}"
                        >
                        @break
                @endswitch
                </span>
            </p>
        @endif
    @endforeach
    </div>
    @endif
</div>
