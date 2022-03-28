<?php

namespace App\Response;

use App\Entity\Appointments;
use DateTime;

class Appointment
{
    public int $id;

    public string $time_from;

    public string $time_to;

    public string $date;

    public string $type;

    public string $description;

    public DoctorBase $doctor;

    public User $patient;

    public function __construct(Appointments $appointments, DoctorBase $doctor, ?User $patient)
    {
        $this->id = $appointments->getId();
        $this->time_from = $appointments->getTimeFrom();
        $this->time_to = $appointments->getTimeTo();
        $this->date = $appointments->getDate();
        $this->type = $appointments->getType();
        $this->description = $appointments->getDescription();
        $this->doctor = $doctor;
        $this->patient = $patient;
    }

}