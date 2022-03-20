<?php

namespace App\Response;

use App\Entity\Doctors;

class DoctorBase extends User
{
    public string $title;

    public Specialisation $specialisation;

    public int $appointments_length;

    public string $address;

    public string $city;

    public string $description;

    /**
     * @param Doctors $doctors
     */
    public function __construct(Doctors $doctors)
    {
        parent::__construct($doctors->getUser());

        $this->title = $doctors->getTitle();
        $this->specialisation = new Specialisation($doctors->getSpecialisation());
        $this->appointments_length = $doctors->getAppointmentsLength();
        $this->address = $doctors->getAddress();
        $this->city = $doctors->getCity();
        $this->description = $doctors->getDescription();
    }
}