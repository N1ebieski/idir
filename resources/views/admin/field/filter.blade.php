<form data-route="{{ route("admin.field.{$field->poli}.index") }}" id="filter">
    <div class="row position-relative">
        <div class="col mb-3">
            <span class="badge badge-primary">{{ trans('icore::filter.items') }}: {{ $fields->total() }}</span>&nbsp;
            @if ($filter['search'] !== null)
            <a href="#" class="badge badge-primary filterOption" data-name="filter[search]">
                {{ trans('icore::filter.search') }}: {{ $filter['search'] }}
                <span aria-hidden="true">&times;</span>
            </a>&nbsp;
            @endif
            @if ($filter['visible'] !== null)
            <a href="#" class="badge badge-primary filterOption" data-name="filter[visible]">
                {{ trans('idir::groups.visible') }}: {{ trans('idir::fields.visible_'.$filter['visible']) }}
                <span aria-hidden="true">&times;</span>
            </a>&nbsp;
            @endif
            @if ($filter['type'] !== null)
            <a href="#" class="badge badge-primary filterOption" data-name="filter[type]">
                {{ trans('icore::filter.type') }}: {{ $filter['type'] }}
                <span aria-hidden="true">&times;</span>
            </a>&nbsp;
            @endif
            @yield('filter-morph-option')
            @if (array_filter($filter))
            <a href="{{ route("admin.field.{$field->poli}.index") }}" class="badge badge-dark">{{ trans('icore::default.clear') }}</a>&nbsp;
            @endif
        </div>
        <div class="col-xs-3 text-right mx-3">
            <div class="form-inline">
                <div class="form-group col-xs-4">
                    <button class="btn border mr-2" href="#" type="button" data-toggle="modal"
                    data-target="#filterModal">
                        <i class="fas fa-sort-amount-up"></i>
                    </button>
                </div>
                <div class="form-group col-xs-4 mr-2">
                    <label class="sr-only" for="filterOrderBy">{{ trans('icore::filter.order') }}</label>
                    <select class="form-control custom-select filter" name="filter[orderby]" id="filterOrderBy">
                        <option value="">{{ trans('icore::filter.order') }} {{ trans('icore::filter.default') }}</option>
                        <option value="created_at|desc"
                        {{ ($filter['orderby'] == 'created_at|desc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.created_at')) }}
                            {{ trans('icore::filter.desc') }}</option>
                        <option value="created_at|asc"
                        {{ ($filter['orderby'] == 'created_at|asc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.created_at')) }}
                            {{ trans('icore::filter.asc') }}</option>
                        <option value="updated_at|desc"
                        {{ ($filter['orderby'] == 'updated_at|desc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.updated_at')) }}
                            {{ trans('icore::filter.desc') }}</option>
                        <option value="updated_at|asc"
                        {{ ($filter['orderby'] == 'updated_at|asc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.updated_at')) }}
                            {{ trans('icore::filter.asc') }}</option>
                        <option value="title|desc"
                        {{ ($filter['orderby'] == 'title|desc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.title')) }}
                            {{ trans('icore::filter.desc') }}</option>
                        <option value="title|asc"
                        {{ ($filter['orderby'] == 'title|asc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.title')) }}
                            {{ trans('icore::filter.asc') }}</option>
                        <option value="position|desc"
                        {{ ($filter['orderby'] == 'position|desc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.position')) }}
                            {{ trans('icore::filter.desc') }}</option>
                        <option value="position|asc"
                        {{ ($filter['orderby'] == 'position|asc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.position')) }}
                            {{ trans('icore::filter.asc') }}</option>
                    </select>
                </div>
                <div class="form-group col-xs-4">
                    <label class="sr-only" for="filterPaginate">{{ trans('icore::filter.paginate') }}</label>
                    <select class="form-control custom-select filter" name="filter[paginate]" id="filterPaginate">
                        <option value="{{ $paginate }}" {{ ($filter['paginate'] == $paginate) ? 'selected' : '' }}>{{ $paginate }}</option>
                        <option value="{{ ($paginate*2) }}" {{ ($filter['paginate'] == ($paginate*2)) ? 'selected' : '' }}>{{ ($paginate*2) }}</option>
                        <option value="{{ ($paginate*4) }}" {{ ($filter['paginate'] == ($paginate*4)) ? 'selected' : '' }}>{{ ($paginate*4) }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    @include('idir::admin.field.filter_filter')
</form>
