<h5 class="mt-4 mt-sm-0 mb-2">
    {{ trans('icore::stats.stats') }}:
</h5>
<div class="list-group list-group-flush text-left">
    <div class="list-group-item d-flex justify-content-between">
        <div>
            {{ trans('icore::categories.route.index') }}:
        </div>
        <div class="text-right">
            {{ $countCategories->count ?? 0 }}
        </div>
    </div>
    @if ($countDirs->isNotEmpty())
    <div class="list-group-item">
        <div>
            {{ trans('icore::stats.dir.label') }}:
        </div>
        <div class="d-flex justify-content-between">
            <div>
                - {{ trans("idir::stats.dir.status.1") }}:
            </div>
            <div class="text-right">
                {{ $countDirs->firstWhere('status', 1)->count ?? 0 }}
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <div>
                - {{ trans("idir::stats.dir.status.0") }}:
            </div>
            <div class="text-right">
                {{ $countDirs->firstWhere('status', 0)->count ?? 0 }}
            </div>
        </div>    
    </div>
    @endif
    <div class="list-group-item d-flex justify-content-between">
        <div>
            {{ trans('icore::comments.route.index') }}:
        </div>
        <div class="text-right">
            {{ $countComments->sum('count') ?? 0 }}
        </div>
    </div>   
    @if ($lastActivity)
    <div class="list-group-item d-flex justify-content-between">
        <div>
            {{ trans('icore::stats.last_activity') }}:
        </div>
        <div class="text-right">
            {{ now()->parse($lastActivity)->diffForHumans() }}
        </div>
    </div>
    @endif
    @if ($countUsers)
    <div class="list-group-item">
        <div>
            {{ trans('icore::stats.user.label') }}:
        </div>
        <div class="d-flex justify-content-between">
            <div>
                - {{ trans("icore::stats.user.type.user") }}:
            </div>
            <div class="text-right">
                {{ $countUsers->firstWhere('type', 'user')->count ?? 0 }}
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <div>
                - {{ trans("icore::stats.user.type.guest") }}:
            </div>
            <div class="text-right">
                {{ $countUsers->firstWhere('type', 'guest')->count ?? 0 }}
            </div>
        </div>        
    </div>
    @endif
</div>