<form data-route="{{ route("web.profile.dirs") }}" id="filter">
    <div class="d-flex flex-wrap position-relative">
        <div class="mb-3 mr-auto">
            <span class="badge badge-primary">
                {{ trans('icore::filter.items') }}: {{ $dirs->total() }}
            </span>
            @if ($filter['search'] !== null)
            <span>
                <a 
                    href="#" 
                    class="badge badge-primary filter-option" 
                    data-name="filter[search]"
                >
                    <span>{{ trans('icore::filter.search.label') }}: {{ $filter['search'] }}</span>
                    <span aria-hidden="true">&times;</span>
                </a>
            </span>
            @endif
            @if ($filter['status'] !== null)
            <span>
                <a 
                    href="#" 
                    class="badge badge-primary filter-option" 
                    data-name="filter[status]"
                >
                    <span>{{ trans('icore::filter.status.label') }}: {{ trans("idir::dirs.status.{$filter['status']}") }}</span>
                    <span aria-hidden="true">&times;</span>
                </a>
            </span>
            @endif
            @if ($filter['group'] !== null)
            <span>
                <a 
                    href="#" 
                    class="badge badge-primary filter-option" 
                    data-name="filter[group]"
                >
                    <span>{{ trans('idir::filter.group') }}: {{ $filter['group']->name }}</span>
                    <span aria-hidden="true">&times;</span>
                </a>
            </span>
            @endif
            @if (array_filter($filter))
            <span>
                <a 
                    href="{{ route("web.profile.dirs") }}" 
                    class="badge badge-dark"
                >
                    {{ trans('icore::default.clear') }}
                </a>
            </span>
            @endif
        </div>
        <div class="ml-sm-auto">
            <div class="form-inline d-flex flex-nowrap">
                <div class="form-group col-xs-4">
                    <button 
                        class="btn border mr-2" 
                        href="#" 
                        type="button" 
                        data-toggle="modal"
                        data-target="#filter-modal"
                    >
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
                <div class="form-group col-xs-4 mr-2">
                    <label class="sr-only" for="filter-orderby">
                        {{ trans('icore::filter.order') }}:
                    </label>
                    <select 
                        class="form-control custom-select filter" 
                        name="filter[orderby]" 
                        id="filter-orderby"
                    >
                        <option value="">
                            {{ trans('icore::filter.order') }} {{ trans('icore::filter.default') }}
                        </option>
                        <option 
                            value="created_at|desc"
                            {{ ($filter['orderby'] == 'created_at|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.created_at')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="created_at|asc"
                            {{ ($filter['orderby'] == 'created_at|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.created_at')) }} {{ trans('icore::filter.asc') }}
                        </option>
                        <option 
                            value="updated_at|desc"
                            {{ ($filter['orderby'] == 'updated_at|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.updated_at')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="updated_at|asc"
                            {{ ($filter['orderby'] == 'updated_at|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.updated_at')) }} {{ trans('icore::filter.asc') }}
                        </option>
                        <option 
                            value="title|desc"
                            {{ ($filter['orderby'] == 'title|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.title')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="title|asc"
                            {{ ($filter['orderby'] == 'title|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.title')) }} {{ trans('icore::filter.asc') }}
                        </option>
                        <option 
                            value="sum_rating|desc"
                            {{ ($filter['orderby'] == 'sum_rating|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.sum_rating')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="sum_rating|asc"
                            {{ ($filter['orderby'] == 'sum_rating|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.sum_rating')) }} {{ trans('icore::filter.asc') }}
                        </option>
                        <option 
                            value="click|desc"
                            {{ ($filter['orderby'] == 'click|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.clicks')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="click|asc"
                            {{ ($filter['orderby'] == 'click|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.clicks')) }} {{ trans('icore::filter.asc') }}
                        </option>
                        <option 
                            value="view|desc"
                            {{ ($filter['orderby'] == 'view|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.views')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="view|asc"
                            {{ ($filter['orderby'] == 'view|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.views')) }} {{ trans('icore::filter.asc') }}
                        </option>
                    </select>
                </div>
                <div class="form-group col-xs-4">
                    <label class="sr-only" for="filter-paginate">
                        {{ trans('icore::filter.paginate') }}:
                    </label>
                    <select 
                        class="form-control custom-select filter" 
                        name="filter[paginate]" 
                        id="filter-paginate"
                    >
                        <option 
                            value="{{ $paginate }}" 
                            {{ ($filter['paginate'] == $paginate) ? 'selected' : '' }}
                        >
                            {{ $paginate }}
                        </option>
                        <option 
                            value="{{ ($paginate*2) }}" 
                            {{ ($filter['paginate'] == ($paginate*2)) ? 'selected' : '' }}
                        >
                            {{ ($paginate*2) }}
                        </option>
                        <option 
                            value="{{ ($paginate*4) }}" 
                            {{ ($filter['paginate'] == ($paginate*4)) ? 'selected' : '' }}
                        >
                            {{ ($paginate*4) }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    @include('idir::web.profile.partials.dir.filter_filter')
</form>
