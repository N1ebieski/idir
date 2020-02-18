<?php

namespace N1ebieski\IDir\Seeds;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;

class SEOKatalogSeeder extends Seeder
{
    /**
     * Undocumented variable
     *
     * @var int
     */
    public int $group_last_id;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public int $field_last_id;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public int $sub_last_id;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public int $user_last_id;    

    /**
     * Undocumented function
     */
    public function __construct()
    {
        $this->group_last_id = $this->makeGroupLastId(); 
        $this->field_last_id = $this->makeFieldLastId();
        $this->sub_last_id = $this->makeSubLastId();
        $this->user_last_id = $this->makeUserLastId();

        // ini_set('memory_limit', '512M');

        DB::disableQueryLog();
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function makeUserLastId() : int
    {
        return (User::orderBy('id', 'desc')->first()->id - 
            DB::connection('import')->table('users')->orderBy('id', 'desc')->first()->id);        
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function makeSubLastId() : int
    {
        return DB::connection('import')->table('subcategories')->orderBy('id', 'desc')->first('id')->id;
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function makeFieldLastId() : int
    {
        return ((Field::orderBy('id', 'desc')->first()->id ?? 0) - 
            DB::connection('import')->table('forms')->where('mod', 0)->orderBy('id', 'desc')->first()->id);
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function makeGroupLastId() : int
    {
        return (Group::orderBy('id', 'desc')->first()->id - 
            DB::connection('import')->table('groups')->orderBy('id', 'desc')->first()->id);
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SEOKatalog\CategoriesSeeder::class);
        $this->call(SEOKatalog\GroupsAndPrivilegesSeeder::class);
        $this->call(SEOKatalog\FieldsSeeder::class);
        $this->call(SEOKatalog\LinksSeeder::class);
        $this->call(SEOKatalog\UsersSeeder::class);
        $this->call(SEOKatalog\DirsSeeder::class);
        $this->call(SEOKatalog\BansSeeder::class);
        $this->call(SEOKatalog\CommentsSeeder::class);
    }
}
