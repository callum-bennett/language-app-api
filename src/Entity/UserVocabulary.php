<?php

namespace App\Entity;

use App\Repository\UserVocabularyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserVocabularyRepository::class)
 */
class UserVocabulary
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Word::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $word;

    /**
     * @ORM\Column(type="integer")
     */
    private $correct = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $wrong = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lastAttempt;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeCreated;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userVocabularies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWord(): ?Word
    {
        return $this->word;
    }

    public function setWord(Word $word): self
    {
        $this->word = $word;

        return $this;
    }

    public function getCorrect(): ?int
    {
        return $this->correct;
    }

    public function setCorrect(int $correct): self
    {
        $this->correct = $correct;

        return $this;
    }

    public function getWrong(): ?int
    {
        return $this->wrong;
    }

    public function setWrong(int $wrong): self
    {
        $this->wrong = $wrong;

        return $this;
    }

    public function getLastAttempt(): ?int
    {
        return $this->lastAttempt;
    }

    public function setLastAttempt(?int $lastAttempt): self
    {
        $this->lastAttempt = $lastAttempt;

        return $this;
    }

    public function getTimeCreated(): ?int
    {
        return $this->timeCreated;
    }

    public function setTimeCreated(int $timeCreated): self
    {
        $this->timeCreated = $timeCreated;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
