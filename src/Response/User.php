<?php

namespace App\Response;

use App\Entity\Users;

class User
{
    public int $id;

    public string $name;

    public string $surname;

    public string $email;

    public string $phone;

    /**
     * @param Users $user
     */

    public function __construct(Users $user)
    {
        $this->id = $user->getId();
        $this->name = $user->getName();
        $this->surname = $user->getSurname();
        $this->email = $user->getEmail();
        $this->phone = $user->getPhone();
    }


}