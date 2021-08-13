<?php

namespace N1ebieski\IDir\View\ViewModels\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Price;
use Spatie\ViewModels\ViewModel;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Config\Repository as Config;

class EditRenewViewModel extends ViewModel
{
    /**
     * [$dir description]
     *
     * @var Dir
     */
    public $dir;

    /**
     * [$category description]
     *
     * @var Category
     */
    protected $category;

    /**
     * [$config description]
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Config $config
     * @param Request $request
     */
    public function __construct(
        Dir $dir,
        Config $config,
        Request $request
    ) {
        $this->dir = $dir;

        $this->config = $config;
        $this->request = $request;
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @return string
     */
    public function driverByType(string $type): string
    {
        return $this->config->get("idir.payment.{$type}.driver");
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function paymentType(): ?string
    {
        if (!$this->request->old('payment_type') && $this->dir->group->prices->isNotEmpty()) {
            return $this->dir->group->prices
                ->sortBy(function ($item) {
                    return array_search($item->type, Price::AVAILABLE);
                })
                ->first()
                ->type;
        }

        return null;
    }

    /**
     * [paymentCodeSmsSelection description]
     *
     * @return  Price|null  [return description]
     */
    public function paymentCodeSmsSelection(): ?Price
    {
        if ($this->request->old('payment_code_sms') && $this->dir->group->prices->isNotEmpty()) {
            return $this->dir->group->prices->where('id', $this->request->old('payment_code_sms'))->first();
        }

        return $this->pricesByType('code_sms')->first();
    }

    /**
     * [paymentCodeTransferSelection description]
     *
     * @return  Price|null  [return description]
     */
    public function paymentCodeTransferSelection(): ?Price
    {
        if ($this->request->old('payment_code_transfer') && $this->dir->group->prices->isNotEmpty()) {
            return $this->dir->group->prices->where('id', $this->request->old('payment_code_transfer'))->first();
        }

        return $this->pricesByType('code_transfer')->first();
    }

    /**
     * [pricesByType description]
     *
     * @param   string      $type  [$type description]
     *
     * @return  Collection         [return description]
     */
    public function pricesByType(string $type): Collection
    {
        return $this->dir->group->prices->where('type', $type)->sortBy('price');
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getAttributesAsValues(): array
    {
        return $this->dir->setAttribute(
            'field',
            $this->dir->fields
                ->keyBy('id')
                ->map(function ($item) {
                    return $item->decode_value;
                })
                ->toArray()
        )
        ->getAttributes();
    }
}
