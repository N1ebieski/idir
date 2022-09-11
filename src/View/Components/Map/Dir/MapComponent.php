<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\View\Components\Map\Dir;

use Illuminate\View\Component;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\View\Factory as ViewFactory;

class MapComponent extends Component
{
    /**
     *
     * @var Collect
     */
    protected $addressMarker;

    /**
     *
     * @var Collect
     */
    protected $coordsMarker;

    /**
     *
     * @param Dir $dir
     * @param Collect $collect
     * @param ViewFactory $view
     * @param string $containerClass
     * @param int $zoom
     * @param string $selector
     * @param null|array $addressMarkerPattern
     * @param array $coords
     * @param array $addressMarker
     * @param array $coordsMarker
     * @return void
     */
    public function __construct(
        protected Dir $dir,
        protected Collect $collect,
        protected ViewFactory $view,
        protected string $containerClass = 'map',
        protected int $zoom = 13,
        protected string $selector = 'map',
        protected ?array $addressMarkerPattern = null,
        protected array $coords = [52.15, 21.00],
        array $addressMarker = [],
        array $coordsMarker = []
    ) {
        $this->addressMarker = $collect->make($addressMarker);
        $this->coordsMarker = $collect->make($coordsMarker);
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
                    $this->coordsMarker->push([$row->lat, $row->long]);
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

        if ($this->coordsMarker->isNotEmpty()) {
            $this->addressMarker = $this->collect->make([]);

            return;
        }

        if ($this->addressMarkerPattern !== null) {
            $check = $this->dir->group->fields->contains(function ($item) {
                return in_array(
                    $item->id,
                    $this->collect->make($this->addressMarkerPattern)->flatten()->toArray()
                );
            });

            if ($check === true) {
                foreach ($this->addressMarkerPattern as $row) {
                    $address = '';

                    foreach ($row as $col) {
                        if (is_string($value = optional($this->dir->fields->where('id', $col)->first())->decode_value)) {
                            $address .= $value . ' ';
                        }
                    }

                    if (!empty($address)) {
                        $this->addressMarker->push(trim($address));
                    }
                }
            }
        }
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        $this->prepareCoordsMarker();

        $this->prepareAddressMarker();

        return $this->view->make('idir::web.components.map.dir.map', [
            'selector' => $this->selector,
            'containerClass' => $this->containerClass,
            'coords' => json_encode($this->coords),
            'zoom' => $this->zoom,
            'addressMarker' => $this->addressMarker->isNotEmpty() ?
                json_encode($this->addressMarker->toArray()) : null,
            'coordsMarker' => $this->coordsMarker->isNotEmpty() ?
                json_encode($this->coordsMarker->toArray()) : null
        ]);
    }
}
