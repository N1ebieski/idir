<form data-route="{{ url()->current() }}" id="filter">
    @if (isset($search))
    <input type="hidden" value="{{ $search }}" name="search">
    @endif    
    <div class="d-flex position-relative">
        <div class="form-group ml-auto">
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
                    {{ mb_strtolower(trans('icore::filter.created_at')) }}
                    {{ trans('icore::filter.desc') }}
                </option>
                <option 
                    value="created_at|asc"
                    {{ ($filter['orderby'] == 'created_at|asc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.created_at')) }}
                    {{ trans('icore::filter.asc') }}
                </option>
                <option 
                    value="updated_at|desc"
                    {{ ($filter['orderby'] == 'updated_at|desc') ? 'selected' : '' }}>
                    {{ mb_strtolower(trans('icore::filter.updated_at')) }}
                    {{ trans('icore::filter.desc') }}
                </option>
                <option 
                    value="updated_at|asc"
                    {{ ($filter['orderby'] == 'updated_at|asc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.updated_at')) }}
                    {{ trans('icore::filter.asc') }}
                </option>
                <option 
                    value="title|desc"
                    {{ ($filter['orderby'] == 'title|desc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.title')) }}
                    {{ trans('icore::filter.desc') }}
                </option>
                <option 
                    value="title|asc"
                    {{ ($filter['orderby'] == 'title|asc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.title')) }}
                    {{ trans('icore::filter.asc') }}
                </option>
                <option 
                    value="sum_rating|desc"
                    {{ ($filter['orderby'] == 'sum_rating|desc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.sum_rating')) }}
                    {{ trans('icore::filter.desc') }}
                </option>
                <option 
                    value="sum_rating|asc"
                    {{ ($filter['orderby'] == 'sum_rating|asc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.sum_rating')) }}
                    {{ trans('icore::filter.asc') }}
                </option>
                <option 
                    value="click|desc"
                    {{ ($filter['orderby'] == 'click|desc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.clicks')) }}
                    {{ trans('icore::filter.desc') }}
                </option>
                <option 
                    value="click|asc"
                    {{ ($filter['orderby'] == 'click|asc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.clicks')) }}
                    {{ trans('icore::filter.asc') }}
                </option>
                <option
                    value="view|desc"
                    {{ ($filter['orderby'] == 'view|desc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.views')) }}
                    {{ trans('icore::filter.desc') }}
                </option>
                <option 
                    value="view|asc"
                    {{ ($filter['orderby'] == 'view|asc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.views')) }}
                    {{ trans('icore::filter.asc') }}
                </option>                    
            </select>
        </div>
    </div>
</form>
