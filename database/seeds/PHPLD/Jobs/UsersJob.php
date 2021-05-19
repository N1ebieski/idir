<?php

namespace N1ebieski\IDir\Seeders\PHPLD\Jobs;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Undocumented variable
     *
     * @var Collection
     */
    protected $items;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $userLastId;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * Undocumented function
     *
     * @param Collection $item
     */
    public function __construct(Collection $items, int $userLastId)
    {
        $this->items = $items;

        $this->userLastId = $userLastId;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function handle() : void
    {
        $this->items->each(function ($item) {
            if (!$this->verify($item)) {
                return;
            }

            DB::transaction(function () use ($item) {
                $user = User::make();

                $user->id = $this->userLastId + $item->ID;
                $user->email = $item->EMAIL;
                $user->name = $user->firstWhere('name', '=', $item->LOGIN) === null ?
                    $item->LOGIN
                    : 'user-' . Str::uuid();
                $user->password = Str::random(12);
                $user->status = $item->ACTIVE === 0 ?
                    User::INACTIVE
                    : User::ACTIVE;
                $user->created_at = $item->REGISTRATION_DATE;
                $user->updated_at = $item->REGISTRATION_DATE;

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
     * Undocumented function
     *
     * @param object $item
     * @return boolean
     */
    protected function verify(object $item) : bool
    {
        return User::where('id', $this->userLastId + $item->ID)
            ->orWhere('email', $item->EMAIL)->first() === null;
    }
}
