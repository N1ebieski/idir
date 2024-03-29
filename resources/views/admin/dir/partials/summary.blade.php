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
        <span>{!! $group->hasEditorPrivilege() ? $value['content_html'] : nl2br(e($value['content_html'])) !!}</span>
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
                <span>
                    {{ $field->title }}@if (!$field->type->isSwitch()):@endif
                </span>
                <br>
                <span>
                @switch($field->type)
                    @case(Field\Type::INPUT)
                    @case(Field\Type::TEXTAREA)
                    @case(Field\Type::SELECT)
                        {{ $value['field'][$field->id] }}
                        @break;

                    @case(Field\Type::MULTISELECT)
                    @case(Field\Type::CHECKBOX)
                        {{ implode(', ', $value['field'][$field->id]) }}
                        @break;

                    @case(Field\Type::REGIONS)
                        {{ implode(', ', $regions->whereIn('id', $value['field'][$field->id])->pluck('name')->toArray()) }}
                        @break;

                    @case(Field\Type::MAP)
                        <x-idir::map.dir.map-component
                            :coords_marker="[[$value['field'][$field->id][0]['lat'], $value['field'][$field->id][0]['long']]]"
                        />
                        @break;                        

                    @case(Field\Type::IMAGE)                    
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
