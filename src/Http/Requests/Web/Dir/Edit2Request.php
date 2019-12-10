<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Models\Category\Dir\Category;

class Edit2Request extends FormRequest
{
    /**
     * [private description]
     * @var Category
     */
    protected $category;

    /**
     * [__construct description]
     * @param Category $category [description]
     */
    public function __construct(Category $category)
    {
        parent::__construct();

        $this->category = $category;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $check = $this->group->isPublic();

        return $this->dir->isGroup($this->group->id) ?
            $check : $check && $this->group->isAvailable();
    }

    /**
     * [prepareForValidation description]
     */
    public function prepareForValidation() : void
    {
        $this->prepareCategoriesCollectionOldAttribute();

        $this->prepareContentHtmlOldAttribute();
    }

    /**
     * [prepareCategoriesCollectionOldAttribute description]
     */
    protected function prepareCategoriesCollectionOldAttribute() : void
    {
        // Brzyki hook, ale nie mam innego pomyslu. Request dla kategorii zwraca tylko IDki
        // a w widoku edycji wpisu potrzebujemy calej kolekcji, co w przypadku wstawiania
        // danych z helpera old() stanowi problem
        if ($this->old('categories') || $this->session()->get("dirId.{$this->dir->id}.categories")) {
            session()->put('_old_input.categories_collection',
                $this->category->makeRepo()->getByIds(
                    $this->old('categories') ?? $this->session()->get("dirId.{$this->dir->id}.categories")
                )
            );
        } else {
            session()->forget('_old_input.categories_collection');
        }
    }

    /**
     * [prepareContentHtmlOldAttribute description]
     */
    protected function prepareContentHtmlOldAttribute() : void
    {
        if ($this->old('content_html')) {
            if (!$this->group->privileges->contains('name', 'additional options for editing content')) {
                session()->put('_old_input.content_html', strip_tags($this->old('content_html')));
            }
        }
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
