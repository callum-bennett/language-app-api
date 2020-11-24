<?php

namespace App\Event;

use App\Entity\UserBadge;
use Symfony\Contracts\EventDispatcher\Event;

class BadgeAwardedEvent extends Event
{
    public const NAME = 'badge.awarded';

    protected $userBadge;

    public function __construct(UserBadge $userBadge)
    {
        $this->userBadge = $userBadge;
    }

    public function getUserBadge(): UserBadge
    {
        return $this->userBadge;
    }
}