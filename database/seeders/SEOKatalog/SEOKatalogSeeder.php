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

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use InvalidArgumentException;
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
     *
     * @param Cache $cache
     * @param Queue $queue
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected Cache $cache,
        protected Queue $queue
    ) {
        $this->groupLastId = $this->getGroupLastId();
        $this->fieldLastId = $this->getFieldLastId();
        $this->subLastId = $this->subLastId();
        $this->userLastId = $this->getUserLastId();

        DB::disableQueryLog();
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function getUserLastId(): int
    {
        /** @var User */
        $user = User::orderBy('id', 'desc')->first();

        return (
            $user->id
            -
            // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
        return DB::connection('import')->table('subcategories')->orderBy('id', 'desc')->first('id')->id;
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function getFieldLastId(): int
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
    protected function getGroupLastId(): int
    {
        /** @var Group */
        $group = Group::orderBy('id', 'desc')->first();

        return (
            $group->id
            -
            // @phpstan-ignore-next-line
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
