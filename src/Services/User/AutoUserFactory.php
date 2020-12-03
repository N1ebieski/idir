<?php

namespace N1ebieski\IDir\Services\User;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use N1ebieski\IDir\Models\User;

class AutoUserFactory
{
    /**
     * Undocumented variable
     *
     * @var User
     */
    protected $user;

    /**
     * Undocumented variable
     *
     * @var Str
     */
    protected $str;

    /**
     * Undocumented variable
     *
     * @var Request
     */
    protected $request;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $email;

    /**
     * Undocumented function
     *
     * @param User $user
     * @param Str $str
     * @param string $email
     */
    public function __construct(
        User $user,
        Str $str,
        Request $request,
        string $email
    ) {
        $this->user = $user;

        $this->str = $str;
        $this->request = $request;

        $this->email = $email;
    }

    /**
     * Undocumented function
     *
     * @return User
     */
    public function makeUser() : User
    {
        $user = $this->user->makeService()
            ->create([
                'email' => $this->email,
                'name' => 'user-' . $this->str->uuid(),
                'password' => $this->str->random(12)
            ]);

        $user->update([
            'ip' => $this->request->ip()
        ]);

        return $user;
    }
}
