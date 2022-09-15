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

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog\Jobs;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use InvalidArgumentException;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UsersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     *
     * @param Collection $items
     * @param int $userLastId
     * @return void
     */
    public function __construct(
        protected Collection $items,
        protected int $userLastId
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function handle(): void
    {
        $this->items->each(function ($item) {
            if (!$this->verify($item)) {
                return;
            }

            DB::transaction(function () use ($item) {
                $user = new User();

                $user->id = $this->userLastId + $item->id;
                $user->email = $item->email;
                $user->name = $user->firstWhere('name', $item->nick) === null ?
                    $item->nick
                    : 'user-' . Str::uuid();
                $user->password = Str::random(12);
                $user->status = $item->active;

                $user->save();

                $user->assignRole('user');
            });
        });
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        //
    }

    /**
     *
     * @param mixed $item
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function verify($item): bool
    {
        return User::where('id', $this->userLastId + $item->id)
            ->orWhere('email', $item->email)->first() === null;
    }
}
