<?php

namespace App\Response;

use App\Entity\Doctors;

class DoctorFavorite extends DoctorBase
{
    public bool $is_favourite;

    /**
     * @param Doctors $doctor
     * @param bool $is_favourite
     */
    public function __construct(Doctors $doctor, bool $is_favourite)
    {
        parent::__construct($doctor);
        $this->is_favourite = $is_favourite;
    }

}