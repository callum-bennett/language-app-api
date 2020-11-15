<?php

namespace App\Entity;

use App\Repository\LessonProgressRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonProgressRepository::class)
 */
class LessonProgress
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Lesson::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $lesson;


    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="lessonProgress")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $responses = [];

    /**
     * @ORM\ManyToOne(targetEntity=LessonComponentInstance::class, inversedBy="lessonProgress")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activeComponent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void {
        $this->user = $user;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getResponses(): ?array
    {
        return $this->responses;
    }

    public function setResponses(?array $responses): self
    {
        $this->responses = $responses;

        return $this;
    }

    public function getActiveComponent(): ?LessonComponentInstance
    {
        return $this->activeComponent;
    }

    public function setActiveComponent(?LessonComponentInstance $activeComponent): self
    {
        $this->activeComponent = $activeComponent;

        return $this;
    }
}
