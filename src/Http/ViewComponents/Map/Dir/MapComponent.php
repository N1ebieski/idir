<?php

namespace N1ebieski\IDir\Http\ViewComponents\Map\Dir;

use N1ebieski\ICore\Http\ViewComponents\Map\MapComponent as BaseMapComponent;
use N1ebieski\IDir\Models\Dir;
use Illuminate\View\View;
use Illuminate\Support\Collection as Collect;

/**
 * [MapComponent description]
 */
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
     * @var array
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
    protected $coords_marker_pattern;

    /**
     * Undocumented variable
     *
     * @var array
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
        Collect $collect,
        string $selector = 'map',
        string $container_class = 'map', 
        int $zoom = 15, 
        array $address_marker = null,
        array $address_marker_pattern = null,
        array $coords_marker = null,
        array $coords_marker_pattern = null        
    )
    {
        parent::__construct($container_class, $zoom, $address_marker);

        $this->dir = $dir;
        $this->collect = $collect;

        $this->selector = $selector;
        $this->address_marker = $collect->make((array)$address_marker);
        $this->address_marker_pattern = $address_marker_pattern;
        $this->coords_marker = $collect->make((array)$coords_marker);
        $this->coords_marker_pattern = $coords_marker_pattern;        
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        // if ($this->coords_marker_pattern !== null) {
        //     foreach ($this->coords_marker_pattern as $row) {
        //         if ($row['lat'] !== null && $row['long'] !== null) {
        //             $this->coords_marker->push([$row['lat'], $row['long']]);
        //         }
        //     }

        //     $marker = [
        //         'coordsMarker' => $this->coords_marker->isNotEmpty() ? 
        //             json_encode($this->coords_marker->toArray()) : null
        //     ];
        // } 
        // else 
        if ($this->address_marker_pattern !== null) {
            $check = $this->dir->group->fields->contains(function($item) {
                return in_array(
                    $item->id, 
                    $this->collect->make($this->address_marker_pattern)->flatten()->toArray()
                );
            });

            if ($check === true) {
                foreach ($this->address_marker_pattern as $row) {
                    $address = '';

                    foreach ($row as $col) {
                        $address .= optional($this->dir->fields->where('id', $col)->first())->decode_value . ' ';
                    }

                    $this->address_marker->push(trim($address));
                }

                $marker = [
                    'addressMarker' => $this->address_marker->isNotEmpty() ?
                        json_encode($this->address_marker->toArray()) : null
                ];
            }
        }

        return view('idir::web.components.map.dir.map', [
            'selector' => $this->selector,
            'containerClass' => $this->container_class,
            'zoom' => $this->zoom
        ] + ($marker ?? []));
    }
}
