<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="users_email_uindex", columns={"email"})}, indexes={@ORM\Index(name="IDX_1483A5E987F4FB17", columns={"doctor_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="users_id_seq", allocationSize=1, initialValue=1)
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    #[Groups(['doctors.read'])]
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=64, nullable=false)
     */
    #[Groups(['doctors.read'])]
    private string $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=256, nullable=false)
     */
    #[Groups(['doctors.read'])]
    private string $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=16, nullable=false)
     */
    #[Groups(['doctors.read'])]
    private string $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="password_hash", type="string", length=64, nullable=false)
     */
    private string $passwordHash;

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
     * @ORM\ManyToOne(targetEntity="Doctors", fetch="EAGER")
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;

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

    public function getRoles(): array
    {
        if ($this->getDoctor() instanceof Doctors) {
            return ['ROLE_DOCTOR', 'ROLE_PATIENT'];
        }
        return ['ROLE_PATIENT'];
    }

    public function eraseCredentials() { }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getPassword(): ?string
    {
        return $this->getPasswordHash();
    }
}
