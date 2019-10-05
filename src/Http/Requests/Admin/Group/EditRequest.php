<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Group;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Models\Price;

class EditRequest extends FormRequest
{
    /**
     * [private description]
     * @var Price
     */
    private $price;

    /**
     * [__construct description]
     * @param Price $price [description]
     */
    public function __construct(Price $price)
    {
        $this->price = $price;
    }

    public function prepareForValidation()
    {
        foreach (['transfer', 'auto_sms'] as $type) {
            session()->flash("_old_input.prices_collection.{$type}",
                is_array($this->old("prices.{$type}")) ?
                    $this->price->hydrate(array_merge(
                        collect($this->old("prices.{$type}"))->filter(function($item) {
                                return isset($item['select']) && $item['price'] !== null;
                            })->toArray(), [['type' => $type]]
                    )) : $this->group->getRepo()->getPricesByType($type)->add($this->price->make(['type' => $type]))
            );
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
