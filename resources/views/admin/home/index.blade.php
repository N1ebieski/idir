@extends(config('idir.layout') . '::admin.layouts.layout')

@section('breadcrumb')
<li class="breadcrumb-item active">
    Overview
</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div style="height:400px;">
            <canvas 
                id="countDirsByStatus"
                data="{{ json_encode($countDirsByStatus) }}"
                data-label="{{ trans('idir::dirs.chart.count_by_status') }}"
            ></canvas>
        </div>
        <div style="width: 100%; overflow-x: auto; overflow-y: hidden;">
            <div style="height: 400px;">
                <canvas
                    id="countDirsByDateAndGroup"
                    data="{{ json_encode($countDirsByDateAndGroup) }}"
                    data-label="{{ trans('idir::dirs.chart.count_by_status') }}"
                ></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
    </div>
</div>
@endsection
