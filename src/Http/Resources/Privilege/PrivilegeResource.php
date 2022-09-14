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

namespace N1ebieski\IDir\Http\Resources\Privilege;

use N1ebieski\IDir\Models\Privilege;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Privilege
 */
class PrivilegeResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Privilege $privilege
     */
    public function __construct(Privilege $privilege)
    {
        parent::__construct($privilege);
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => __($this->name),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
