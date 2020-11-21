<?php

namespace App\Entity;

use App\Repository\UserBadgeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserBadgeRepository::class)
 */
class UserBadge
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userBadges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Badge::class, inversedBy="userBadges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $badge;

    /**
     * @ORM\Column(type="integer")
     */
    private $awardedDate;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setBadge(?Badge $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    public function getAwardedDate(): ?int
    {
        return $this->awardedDate;
    }

    public function setAwardedDate(int $awardedDate): self
    {
        $this->awardedDate = $awardedDate;

        return $this;
    }
}
