<?php

namespace App\Entity;

use App\Repository\XPRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=XPRepository::class)
 */
class XP
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $daily;

    /**
     * @ORM\Column(type="integer")
     */
    private $weekly;

    /**
     * @ORM\Column(type="integer")
     */
    private $monthly;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="XP", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDaily(): ?int
    {
        return $this->daily;
    }

    public function setDaily(int $daily): self
    {
        $this->daily = $daily;

        return $this;
    }

    public function getWeekly(): ?int
    {
        return $this->weekly;
    }

    public function setWeekly(int $weekly): self
    {
        $this->weekly = $weekly;

        return $this;
    }

    public function getMonthly(): ?int
    {
        return $this->monthly;
    }

    public function setMonthly(int $monthly): self
    {
        $this->monthly = $monthly;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
