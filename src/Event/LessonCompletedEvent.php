<?php

namespace App\Event;

use App\Entity\LessonProgress;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class LessonCompletedEvent extends Event
{
    public const NAME = 'lesson.completed';

    protected $user;
    protected $lessonProgress;

    public function __construct(User $user, LessonProgress $lessonProgress)
    {
        $this->user = $user;
        $this->lessonProgress = $lessonProgress;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getLessonProgress(): LessonProgress
    {
        return $this->lessonProgress;
    }
}
