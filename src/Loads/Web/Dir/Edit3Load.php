<?php

namespace N1ebieski\IDir\Loads\Web\Dir;

use Illuminate\Http\Request;

/**
 * [Edit3Load description]
 */
class Edit3Load
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('group')
            ->loadCount(['dirs', 'dirsToday'])
            ->load([
                'privileges',
                'fields' => function ($query) {
                    $query->orderBy('position', 'asc');
                }
            ]);

        $request->route('dir')->load('backlink');
    }
}
