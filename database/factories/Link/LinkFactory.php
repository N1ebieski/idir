<?php

namespace N1ebieski\IDir\Database\Factories\Link;

use N1ebieski\IDir\Models\Link;
use N1ebieski\ICore\Database\Factories\Link\LinkFactory as BaseLinkFactory;

class LinkFactory extends BaseLinkFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Link::class;

    /**
     * Undocumented function
     *
     * @return static
     */
    public function backlink()
    {
        return $this->state(function () {
            return [
                'type' => 'backlink',
            ];
        });
    }
}
