<?php

namespace N1ebieski\IDir\View\Components\Map\Dir;

use Illuminate\View\View;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\View\Factory as ViewFactory;
use N1ebieski\ICore\View\Components\Map\MapComponent as BaseMapComponent;

class MapComponent extends BaseMapComponent
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $selector;

    /**
     * Undocumented variable
     *
     * @var array|null
     */
    protected $address_marker_pattern;

    /**
     * Undocumented variable
     *
     * @var Collect
     */
    protected $address_marker;

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $coords;

    /**
     * Undocumented variable
     *
     * @var Collect
     */
    protected $coords_marker;

    /**
     * Undocumented variable
     *
     * @var Collect
     */
    protected $collect;

    /**
     * Undocumented variable
     *
     * @var Dir
     */
    protected $dir;

    public function __construct(
        Dir $dir,
        ViewFactory $view,
        Collect $collect,
        string $selector = 'map',
        string $container_class = 'map',
        int $zoom = 13,
        array $address_marker = [],
        array $address_marker_pattern = null,
        array $coords_marker = [],
        array $coords = [52.15, 21.00]
    ) {
        parent::__construct($view, $container_class, $zoom, $address_marker);

        $this->dir = $dir;

        $this->collect = $collect;

        $this->selector = $selector;
        $this->coords = $coords;
        $this->address_marker = $collect->make($address_marker);
        $this->address_marker_pattern = $address_marker_pattern;
        $this->coords_marker = $collect->make($coords_marker);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function prepareCoordsMarker(): void
    {
        if (!$this->dir->relationLoaded('fields')) {
            return;
        }

        if ($this->dir->group->fields->contains('type', 'map')) {
            if (is_array($value = optional($this->dir->fields->where('type', 'map')->first())->decode_value)) {
                foreach ($value as $row) {
                    $this->coords_marker->push([$row->lat, $row->long]);
                }
            }
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function prepareAddressMarker(): void
    {
        if (!$this->dir->relationLoaded('fields')) {
            return;
        }

        if ($this->coords_marker->isNotEmpty()) {
            $this->address_marker = $this->collect->make([]);
            
            return;
        }

        if ($this->address_marker_pattern !== null) {
            $check = $this->dir->group->fields->contains(function ($item) {
                return in_array(
                    $item->id,
                    $this->collect->make($this->address_marker_pattern)->flatten()->toArray()
                );
            });

            if ($check === true) {
                foreach ($this->address_marker_pattern as $row) {
                    $address = '';

                    foreach ($row as $col) {
                        if (is_string($value = optional($this->dir->fields->where('id', $col)->first())->decode_value)) {
                            $address .= $value . ' ';
                        }
                    }

                    if (!empty($address)) {
                        $this->address_marker->push(trim($address));
                    }
                }
            }
        }
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml(): View
    {
        $this->prepareCoordsMarker();

        $this->prepareAddressMarker();

        return $this->view->make('idir::web.components.map.dir.map', [
            'selector' => $this->selector,
            'containerClass' => $this->container_class,
            'coords' => json_encode($this->coords),
            'zoom' => $this->zoom,
            'addressMarker' => $this->address_marker->isNotEmpty() ?
                json_encode($this->address_marker->toArray()) : null,
            'coordsMarker' => $this->coords_marker->isNotEmpty() ?
                json_encode($this->coords_marker->toArray()) : null
        ]);
    }
}
