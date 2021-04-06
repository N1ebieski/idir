<form 
    data-route="{{ route("admin.price.index") }}" 
    id="filter"
>
    <div class="d-flex flex-wrap position-relative">
        <div class="mb-3 mr-auto">
            <span class="badge badge-primary">
                {{ trans('icore::filter.items') }}: {{ $prices->total() }}
            </span>
            @if ($filter['search'] !== null)
            <span>
                <a 
                    href="#" 
                    class="badge badge-primary filterOption" 
                    data-name="filter[search]"
                >
                    <span>{{ trans('icore::filter.search.label') }}: {{ $filter['search'] }}</span>
                    <span aria-hidden="true">&times;</span>
                </a>
            </span>
            @endif
            @if ($filter['group'] !== null)
            <span>
                <a 
                    href="#" 
                    class="badge badge-primary filterOption" 
                    data-name="filter[group]"
                >
                    <span>{{ trans('idir::filter.group') }}: {{ $filter['group']->name }}</span>
                    <span aria-hidden="true">&times;</span>
                </a>
            </span>
            @endif
            @if ($filter['type'] !== null)
            <span>
                <a 
                    href="#" 
                    class="badge badge-primary filterOption" 
                    data-name="filter[type]"
                >
                    <span>{{ trans('icore::filter.type') }}: {{ $filter['type'] }}</span>
                    <span aria-hidden="true">&times;</span>
                </a>
            </span>
            @endif
            @if (array_filter($filter))
            <span>
                <a 
                    href="{{ route("admin.price.index") }}" 
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
                        data-target="#filterModal"
                    >
                        <i class="fas fa-sort-amount-up"></i>
                    </button>
                </div>
                <div class="form-group col-xs-4 mr-2">
                    <label class="sr-only" for="filterOrderBy">
                        {{ trans('icore::filter.order') }}
                    </label>
                    <select 
                        class="form-control custom-select filter" 
                        name="filter[orderby]" 
                        id="filterOrderBy"
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
                            value="price|desc"
                            {{ ($filter['orderby'] == 'price|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('idir::prices.price')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="price|asc"
                            {{ ($filter['orderby'] == 'price|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('idir::prices.price')) }} {{ trans('icore::filter.asc') }}
                        </option>
                        <option 
                            value="days|desc"
                            {{ ($filter['orderby'] == 'days|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('idir::prices.days')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="days|asc"
                            {{ ($filter['orderby'] == 'days|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('idir::prices.days')) }} {{ trans('icore::filter.asc') }}
                        </option>
                    </select>
                </div>
                <div class="form-group col-xs-4">
                    <label class="sr-only" for="filterPaginate">
                        {{ trans('icore::filter.paginate') }}
                    </label>
                    <select 
                        class="form-control custom-select filter" 
                        name="filter[paginate]" 
                        id="filterPaginate"
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
    @include('idir::admin.price.partials.filter_filter')
</form>
