<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * WorkSchedules
 *
 * @ORM\Table(name="work_schedules", indexes={@ORM\Index(name="IDX_533374DB87F4FB17", columns={"doctor_id"})})
 * @ORM\Entity
 */
class WorkSchedules
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="work_schedules_id_seq", allocationSize=1, initialValue=1)
     */
    private int $id;

    /**
     * @var int
     *
     * @ORM\Column(name="weekday", type="smallint", nullable=false)
     */
    private int $weekday;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="time_from", type="time", nullable=false)
     */
    private DateTime $timeFrom;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="time_to", type="time", nullable=false)
     */
    private DateTime $timeTo;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $createdAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $updatedAt;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private ?DateTime $deletedAt;

    /**
     * @var Doctors
     *
     * @ORM\ManyToOne(targetEntity="Doctors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_id", referencedColumnName="id")
     * })
     */
    private Doctors $doctor;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeekday(): ?int
    {
        return $this->weekday;
    }

    public function setWeekday(int $weekday): self
    {
        $this->weekday = $weekday;

        return $this;
    }

    public function getTimeFrom(): ?DateTime
    {
        return $this->timeFrom;
    }

    public function setTimeFrom(DateTime $timeFrom): self
    {
        $this->timeFrom = $timeFrom;

        return $this;
    }

    public function getTimeTo(): ?DateTime
    {
        return $this->timeTo;
    }

    public function setTimeTo(DateTime $timeTo): self
    {
        $this->timeTo = $timeTo;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getDoctor(): ?Doctors
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctors $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
    }


}
