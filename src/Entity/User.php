<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("username", message="Username already in use")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, nullable=true))
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Username is required")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=191, unique=true, nullable=true)
     */
    private $apiToken;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message="Password is required")
     */
    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity=LessonProgress::class, mappedBy="user", orphanRemoval=true)
     */
    private $lessonProgress;

    /**
     * @ORM\OneToMany(targetEntity=UserVocabulary::class, mappedBy="user", orphanRemoval=true)
     */
    private $userVocabularies;

    public function __construct()
    {
        $this->lessonProgress = new ArrayCollection();
        $this->userVocabularies = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return Collection|LessonProgress[]
     */
    public function getLessonProgress(): Collection
    {
        return $this->lessonProgress;
    }

    public function addLessonProgress(LessonProgress $lessonProgress): self
    {
        if (!$this->lessonProgress->contains($lessonProgress)) {
            $this->lessonProgress[] = $lessonProgress;
            $lessonProgress->setUser($this);
        }

        return $this;
    }

    public function removeLessonProgress(LessonProgress $lessonProgress): self
    {
        if ($this->lessonProgress->contains($lessonProgress)) {
            $this->lessonProgress->removeElement($lessonProgress);
            // set the owning side to null (unless already changed)
            if ($lessonProgress->getUser() === $this) {
                $lessonProgress->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserVocabulary[]
     */
    public function getUserVocabularies(): Collection
    {
        return $this->userVocabularies;
    }

    public function addUserVocabulary(UserVocabulary $userVocabulary): self
    {
        if (!$this->userVocabularies->contains($userVocabulary)) {
            $this->userVocabularies[] = $userVocabulary;
            $userVocabulary->setUser($this);
        }

        return $this;
    }

    public function removeUserVocabulary(UserVocabulary $userVocabulary): self
    {
        if ($this->userVocabularies->contains($userVocabulary)) {
            $this->userVocabularies->removeElement($userVocabulary);
            // set the owning side to null (unless already changed)
            if ($userVocabulary->getUser() === $this) {
                $userVocabulary->setUser(null);
            }
        }

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
