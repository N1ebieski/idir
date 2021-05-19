<?php

namespace N1ebieski\IDir\Seeds\PHPLD;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\Queue;
use N1ebieski\IDir\Models\Field\Field;
use Symfony\Component\Process\Process;
use Illuminate\Contracts\Cache\Factory as Cache;

class PHPLDSeeder extends Seeder
{
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
     * Undocumented variable
     *
     * @var int
     */
    private $workers;

    /**
     * Undocumented function
     *
     * @param Queue $queue
     * @param Cache $cache
     */
    public function __construct(Queue $queue, Cache $cache)
    {
        $this->queue = $queue;
        $this->cache = $cache;

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
    protected static function userLastId() : int
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
    protected static function fieldLastId() : int
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
    protected static function groupLastId() : int
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
        $this->workers = (int)$this->cache->store('system')->get('workers', 1);

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

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function import() : void
    {
        $this->command->getOutput()->writeln("\n");

        $startSize = $this->queue->size('import');
        $importBar = $this->command->getOutput()->createProgressBar($startSize);
        $importBar->start();

        for ($i = 0; $i < $this->workers; $i++) {
            $process[$i] = new Process('php artisan queue:work --daemon --stop-when-empty --queue=import');
            $process[$i]->setTimeout(null);
            $process[$i]->start();
        }

        while (true) {
            sleep(10);

            $currentSize = $this->queue->size('import');
            $j = 0;

            for ($i = 0; $i < $this->workers; $i++) {
                if (!$process[$i]->isRunning()) {
                    $j++;
                }
            }

            if ($j === $this->workers) {
                $importBar->finish();
                break;
            }

            $importBar->advance($startSize - $currentSize);
            $startSize = $currentSize;
        }

        $this->command->getOutput()->writeln("\n");
    }
}
