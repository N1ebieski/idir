<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Models\Category\Dir\Category;

class CreateFormRequest extends FormRequest
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
        $this->category = $category;
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

    public function prepareForValidation()
    {
        // Brzyki hook, ale nie mam innego pomyslu. Request dla kategorii zwraca tylko IDki
        // a w widoku edycji wpisu potrzebujemy calej kolekcji, co w przypadku wstawiania
        // danych z helpera old() stanowi problem
        if ($this->old('categories') || $this->session()->get('dir.categories')) {
            session()->flash('_old_input.categories_collection',
                $this->category->getRepo()->getByIds(
                    $this->old('categories') ?? $this->session()->get('dir.categories')
                )
            );
        }

        if ($this->old('content_html')) {
            if (!$this->group_dir_available->privileges->contains('name', 'additional options for editing content')) {
                session()->flash('_old_input.content_html', strip_tags($this->old('content_html')));
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
