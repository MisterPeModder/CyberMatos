<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Validator\ActuallyNotBlank;
use App\Validator\UniqueValueInEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(length: 255, unique: true)]
    #[ActuallyNotBlank]
    #[UniqueValueInEntity(options: ['entityClass' => User::class, 'field' => 'login'])]
    private ?string $login = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Ignore]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email]
    #[ActuallyNotBlank]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[ActuallyNotBlank]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[ActuallyNotBlank]
    private ?string $lastname = null;

    #[ORM\Column]
    #[Ignore]
    private ?array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    #[ActuallyNotBlank]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: AccessToken::class, orphanRemoval: true)]
    #[Ignore]
    private Collection $accessTokens;

    public function __construct()
    {
        $this->accessTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, AccessToken>
     */
    public function getAccessTokens(): Collection
    {
        return $this->accessTokens;
    }

    public function addAccessToken(AccessToken $accessToken): self
    {
        if (!$this->accessTokens->contains($accessToken)) {
            $this->accessTokens->add($accessToken);
            $accessToken->setUserId($this);
        }

        return $this;
    }

    public function removeAccessToken(AccessToken $accessToken): self
    {
        if ($this->accessTokens->removeElement($accessToken)) {
            // set the owning side to null (unless already changed)
            if ($accessToken->getUserId() === $this) {
                $accessToken->setUserId(null);
            }
        }

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }
}
