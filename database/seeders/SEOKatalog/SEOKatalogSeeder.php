<?php

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\Queue;
use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Contracts\Cache\Factory as Cache;
use N1ebieski\IDir\Database\Seeders\Traits\HasImportable;

class SEOKatalogSeeder extends Seeder
{
    use HasImportable;

    /**
     * Undocumented variable
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Undocumented variable
     *
     * @var Queue
     */
    protected $queue;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $groupLastId;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $fieldLastId;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $subLastId;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $userLastId;

    /**
     * Undocumented function
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache, Queue $queue)
    {
        $this->cache = $cache;
        $this->queue = $queue;

        $this->groupLastId = $this->groupLastId();
        $this->fieldLastId = $this->fieldLastId();
        $this->subLastId = $this->subLastId();
        $this->userLastId = $this->userLastId();

        DB::disableQueryLog();
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function userLastId(): int
    {
        return (
            User::orderBy('id', 'desc')->first()->id
            -
            DB::connection('import')->table('users')->orderBy('id', 'desc')->first()->id
        );
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function subLastId(): int
    {
        return DB::connection('import')->table('subcategories')->orderBy('id', 'desc')->first('id')->id;
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function fieldLastId(): int
    {
        return (
            (Field::orderBy('id', 'desc')->first()->id ?? 0)
            -
            optional(DB::connection('import')->table('forms')->where('mod', 0)->orderBy('id', 'desc')->first())->id
        );
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function groupLastId(): int
    {
        return (
            Group::orderBy('id', 'desc')->first()->id
            -
            DB::connection('import')->table('groups')->orderBy('id', 'desc')->first()->id
        );
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->setWorkers(
            (int)$this->cache->store('system')->get('workers', 1)
        );

        $this->call(CategoriesSeeder::class);
        $this->call(GroupsAndPrivilegesSeeder::class);
        $this->call(FieldsSeeder::class);
        $this->call(LinksSeeder::class);
        $this->call(UsersSeeder::class);
        $this->import();
        $this->call(DirsSeeder::class);
        $this->import();
        $this->call(BansSeeder::class);
        $this->call(CommentsSeeder::class);
        $this->import();
    }
}
