<h5 class="mt-4 mt-sm-0 mb-2">
    {{ trans('icore::stats.stats') }}:
</h5>
<div class="list-group list-group-flush text-left">
    @if ($countCategories)
    <div class="list-group-item d-flex justify-content-between">
        <div>
            {{ trans('icore::categories.route.index') }}:
        </div>
        <div class="text-right">
            {{ $countCategories->count }}
        </div>
    </div>
    @endif
    @if ($countDirs->isNotEmpty())
    <div class="list-group-item">
        <div>
            {{ trans('icore::stats.dir.label') }}:
        </div>
        @if ($activeDirs = $countDirs->firstWhere('status', 1))
        <div class="d-flex justify-content-between">
            <div>
                - {{ trans("idir::stats.dir.status.{$activeDirs->status}") }}:
            </div>
            <div class="text-right">
                {{ $activeDirs->count }}
            </div>
        </div>
        @endif
        @if ($inactiveDirs = $countDirs->firstWhere('status', 0))
        <div class="d-flex justify-content-between">
            <div>
                - {{ trans("idir::stats.dir.status.{$inactiveDirs->status}") }}:
            </div>
            <div class="text-right">
                {{ $inactiveDirs->count }}
            </div>
        </div>
        @endif        
    </div>
    @endif
    @if ($countComments)
    <div class="list-group-item d-flex justify-content-between">
        <div>
            {{ trans('icore::comments.route.index') }}:
        </div>
        <div class="text-right">
            {{ $countComments }}
        </div>
    </div>
    @endif    
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
        @foreach ($countUsers as $count)
        <div class="d-flex justify-content-between">
            <div>
                - {{ trans("icore::stats.user.type.{$count->type}") }}:
            </div>
            <div class="text-right">
                {{ $count->count }}
            </div>
        </div>
        @endforeach
    </div>
    @endif     
</div>