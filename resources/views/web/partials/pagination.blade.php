<div class="row" id="is-pagination">
    <div class="col text-left mt-3">
        @if ($items->currentPage() < $items->lastPage())
        <a href="{{ $items->appends(request()->input())->nextPageUrl() }}" rel="nofollow" id="is-next" role="button"
        class="btn btn-outline-secondary text-nowrap" title="{{ trans('icore::pagination.page', ['num' => $items->currentPage()+1]) }}">
            {{ trans('icore::filter.next_items', ['paginate' => ($filter['paginate'] ?? config('database.paginate'))]) }}
            <i class="fas fa-angle-down"></i>
        </a>
        @endif
    </div>
    <div class="col-auto pagination-sm mt-3">
        {{ $items->appends(request()->input())->fragment($fragment ?? '')->links() }}
    </div>
</div>