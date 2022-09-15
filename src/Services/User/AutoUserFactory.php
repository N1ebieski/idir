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

namespace N1ebieski\IDir\Services\User;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use N1ebieski\IDir\Models\User;

class AutoUserFactory
{
    /**
     * Undocumented function
     *
     * @param User $user
     * @param Str $str
     */
    public function __construct(
        protected User $user,
        protected Str $str,
        protected Request $request
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param string $email
     * @return User
     */
    public function makeUser(string $email): User
    {
        /** @var User */
        $user = $this->user->makeService()
            ->create([
                'email' => $email,
                'name' => 'user-' . $this->str->uuid(),
                'password' => $this->str->random(12)
            ]);

        $user->update([
            'ip' => $this->request->ip()
        ]);

        $user->assignRole('user');

        return $user;
    }
}
