<form data-route="{{ route("admin.dir.index") }}" id="filter">
    <div class="d-flex flex-wrap position-relative">
        <div class="mb-3 mr-auto">
            <span class="badge badge-primary">{{ trans('icore::filter.items') }}: {{ $dirs->total() }}</span>&nbsp;
            @if ($filter['search'] !== null)
            <a href="#" class="badge badge-primary filterOption" data-name="filter[search]">
                {{ trans('icore::filter.search.label') }}: {{ $filter['search'] }}
                <span aria-hidden="true">&times;</span>
            </a>&nbsp;
            @endif
            @if ($filter['status'] !== null)
            <a href="#" class="badge badge-primary filterOption" data-name="filter[status]">
                {{ trans('icore::filter.status.label') }}: {{ trans("idir::dirs.status.{$filter['status']}") }}
                <span aria-hidden="true">&times;</span>
            </a>&nbsp;
            @endif
            @if ($filter['report'] !== null)
            <a href="#" class="badge badge-primary filterOption" data-name="filter[report]">
                {{ trans('icore::filter.report.label') }}: {{ trans('icore::filter.report.'.$filter['report']) }}
                <span aria-hidden="true">&times;</span>
            </a>&nbsp;
            @endif        
            @if ($filter['group'] !== null)
            <a href="#" class="badge badge-primary filterOption" data-name="filter[group]">
                {{ trans('idir::filter.group') }}: {{ $filter['group']->name }}
                <span aria-hidden="true">&times;</span>
            </a>&nbsp;
            @endif
            @if ($filter['category'] !== null)
            <a href="#" class="badge badge-primary filterOption" data-name="filter[category]">
                {{ trans('icore::filter.category') }}: {{ $filter['category']->name }}
                <span aria-hidden="true">&times;</span>
            </a>&nbsp;
            @endif
            @if ($filter['author'] !== null)
            <a href="#" class="badge badge-primary filterOption" data-name="filter[author]">
                {{ trans('icore::filter.author') }}: {{ $filter['author']->name }}
                <input type="hidden" name="filter[author]" value="{{ $filter['author']->id }}">
                <span aria-hidden="true">&times;</span>
            </a>&nbsp;
            @endif
            @if (array_filter($filter))
            <a href="{{ route("admin.dir.index") }}" class="badge badge-dark">{{ trans('icore::default.clear') }}</a>&nbsp;
            @endif
        </div>
        <div class="ml-sm-auto">
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
                        <option value="sum_rating|desc"
                        {{ ($filter['orderby'] == 'sum_rating|desc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.sum_rating')) }}
                            {{ trans('icore::filter.desc') }}</option>
                        <option value="sum_rating|asc"
                        {{ ($filter['orderby'] == 'sum_rating|asc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.sum_rating')) }}
                            {{ trans('icore::filter.asc') }}</option>
                        <option value="click|desc"
                        {{ ($filter['orderby'] == 'click|desc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.clicks')) }}
                            {{ trans('icore::filter.desc') }}</option>
                        <option value="click|asc"
                        {{ ($filter['orderby'] == 'click|asc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.clicks')) }}
                            {{ trans('icore::filter.asc') }}</option>
                        <option value="view|desc"
                        {{ ($filter['orderby'] == 'view|desc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.views')) }}
                            {{ trans('icore::filter.desc') }}</option>
                        <option value="view|asc"
                        {{ ($filter['orderby'] == 'view|asc') ? 'selected' : '' }}>{{ mb_strtolower(trans('icore::filter.views')) }}
                            {{ trans('icore::filter.asc') }}</option>
                    </select>
                </div>
                <div class="form-group col-xs-4">
                    <label class="sr-only" for="filterPaginate">{{ trans('icore::filter.paginate') }}</label>
                    <select class="form-control custom-select filter" name="filter[paginate]" id="filterPaginate">
                        <option value="{{ $paginate = config('database.paginate') }}" {{ ($filter['paginate'] == $paginate) ? 'selected' : '' }}>{{ $paginate }}</option>
                        <option value="{{ ($paginate*2) }}" {{ ($filter['paginate'] == ($paginate*2)) ? 'selected' : '' }}>{{ ($paginate*2) }}</option>
                        <option value="{{ ($paginate*4) }}" {{ ($filter['paginate'] == ($paginate*4)) ? 'selected' : '' }}>{{ ($paginate*4) }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    @include('idir::admin.dir.partials.filter_filter')
</form>
