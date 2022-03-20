<?php

namespace App\Response;

use App\Entity\Doctors;
use App\Entity\Users;

class LoginResponse
{
    public ?DoctorBase $doctor;

    public ?User $user;

    public string $access_token;

    /**
     * @param Doctors|Users $user
     * @param string $access_token
     */
    public function __construct(Doctors|Users $user, string $access_token)
    {
        if ($user instanceof Doctors) {
            $this->doctor = new DoctorBase($user);
        } else {
            $this->user = new User($user);
        }

        $this->access_token = $access_token;
    }

}