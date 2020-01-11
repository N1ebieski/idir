<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Report\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;

interface Polymorphic
{
    /**
     * Display all the specified Reports for Dir.
     *
     * @param  Dir  $dir [description]
     * @return JsonResponse          [description]
     */
    public function show(Dir $dir) : JsonResponse;

    /**
     * Clear all Reports for specified Dir.
     *
     * @param  Dir $dir [description]
     * @return JsonResponse         [description]
     */
    public function clear(Dir $dir) : JsonResponse;
}
