<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * AccessTokens
 *
 * @ORM\Table(name="access_tokens", uniqueConstraints={@ORM\UniqueConstraint(name="access_tokens_token_uindex", columns={"token"})}, indexes={@ORM\Index(name="IDX_58D184BCA76ED395", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\AccessTokenRepository")
 */
class AccessTokens
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="access_tokens_id_seq", allocationSize=1, initialValue=1)
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=1024, nullable=false)
     */
    private string $token;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="valid_until", type="datetime", nullable=false)
     */
    private DateTime $validUntil;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $createdAt;

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private Users $user;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getValidUntil(): ?DateTime
    {
        return $this->validUntil;
    }

    public function setValidUntil(DateTime $validUntil): self
    {
        $this->validUntil = $validUntil;

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

    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): self
    {
        $this->user = $user;

        return $this;
    }


}
