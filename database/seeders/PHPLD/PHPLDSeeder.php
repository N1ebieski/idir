<?php

namespace N1ebieski\IDir\Database\Seeders\PHPLD;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\Queue;
use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\IDir\Database\Seeders\Traits\Importable;
use Illuminate\Contracts\Cache\Factory as Cache;

class PHPLDSeeder extends Seeder
{
    use Importable;

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
        $this->userLastId = $this->userLastId();

        DB::disableQueryLog();
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function userLastId(): int
    {
        return (
            User::orderBy('id', 'desc')->first()->id
            -
            DB::connection('import')->table('user')->orderBy('ID', 'desc')->first()->ID
        );
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function fieldLastId(): int
    {
        return (
            (Field::orderBy('id', 'desc')->first()->id ?? 0)
            -
            optional(DB::connection('import')->table('submit_item')->orderBy('ID', 'desc')->first())->ID
        );
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function groupLastId(): int
    {
        return (
            Group::orderBy('id', 'desc')->first()->id
            -
            DB::connection('import')->table('link_type')->orderBy('ID', 'desc')->first()->ID
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
        $this->call(UsersSeeder::class);
        $this->import();
        $this->call(DirsSeeder::class);
        $this->import();
        $this->call(BansSeeder::class);
        $this->call(CommentsSeeder::class);
        $this->import();
    }
}
