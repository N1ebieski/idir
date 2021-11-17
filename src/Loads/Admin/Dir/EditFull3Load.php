<?php

namespace N1ebieski\IDir\Loads\Admin\Dir;

use Illuminate\Http\Request;

class EditFull3Load
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
