<?php

namespace App\Entity;

use App\Repository\WordAttemptRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WordAttemptRepository::class)
 */
class WordAttempt
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Word::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $word;

    /**
     * @ORM\Column(type="integer")
     */
    private $correct;

    /**
     * @ORM\Column(type="integer")
     */
    private $wrong;

    /**
     * @ORM\Column(type="integer")
     */
    private $lastAttempt;

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

    public function setLastAttempt(int $lastAttempt): self
    {
        $this->lastAttempt = $lastAttempt;

        return $this;
    }
}
