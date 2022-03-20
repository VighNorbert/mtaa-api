<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Doctors
 *
 * @ORM\Table(name="doctors", indexes={@ORM\Index(name="IDX_B67687BE5627D44C", columns={"specialisation_id"}), @ORM\Index(name="IDX_B67687BEA76ED395", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\DoctorRepository")
 */
#[ApiFilter(
    SearchFilter::class, properties: ['name' => 'partial', 'specialisation' => 'exact', 'city' => 'partial']
)]
class Doctors
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="doctors_id_seq", allocationSize=1, initialValue=1)
     */
    #[Groups(['doctors.read'])]
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=8, nullable=false)
     */
    #[Groups(['doctors.read'])]
    private string $title;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default"="1"})
     */
    private bool $active = true;

    /**
     * @var int
     *
     * @ORM\Column(name="appointments_length", type="integer", nullable=false, options={"default"="30"})
     */
    #[Groups(['doctors.read'])]
    private int $appointmentsLength = 30;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar_filename", type="string", length=64, nullable=true)
     */
    private ?string $avatarFilename;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=128, nullable=false)
     */
    #[Groups(['doctors.read'])]
    private string $address;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=128, nullable=false)
     */
    #[Groups(['doctors.read'])]
    private string $city;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    #[Groups(['doctors.read'])]
    private string $description;

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
     * @var Specialisations
     *
     * @ORM\ManyToOne(targetEntity="Specialisations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="specialisation_id", referencedColumnName="id")
     * })
     */
    #[Groups(['doctors.read'])]
    private Specialisations $specialisation;

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    #[Groups(['doctors.read'])]
    private Users $user;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getAppointmentsLength(): ?int
    {
        return $this->appointmentsLength;
    }

    public function setAppointmentsLength(int $appointmentsLength): self
    {
        $this->appointmentsLength = $appointmentsLength;

        return $this;
    }

    public function getAvatarFilename(): ?string
    {
        return $this->avatarFilename;
    }

    public function setAvatarFilename(?string $avatarFilename): self
    {
        $this->avatarFilename = $avatarFilename;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getSpecialisation(): ?Specialisations
    {
        return $this->specialisation;
    }

    public function setSpecialisation(?Specialisations $specialisation): self
    {
        $this->specialisation = $specialisation;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }


}
