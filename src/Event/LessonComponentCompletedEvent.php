<?php

namespace App\Event;

use App\Entity\LessonComponentInstance;
use App\Entity\LessonProgress;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class LessonComponentCompletedEvent extends Event
{
    public const NAME = 'lessonComponent.completed';

    protected $lessonComponentInstance;
    protected $user;
    protected $lessonProgress;

    public function __construct(User $user, LessonComponentInstance $lessonComponentInstance, LessonProgress $lessonProgress)
    {
        $this->user = $user;
        $this->lessonComponentInstance = $lessonComponentInstance;
        $this->lessonProgress = $lessonProgress;
    }

    public function getLessonComponentInstance(): LessonComponentInstance {
        return $this->lessonComponentInstance;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function getLessonProgress(): LessonProgress {
        return $this->lessonProgress;
    }
}
