<?php

namespace App\Entity;

use App\Repository\LessonComponentInstanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonComponentInstanceRepository::class)
 */
class LessonComponentInstance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=LessonComponent::class, inversedBy="lessonComponentInstances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lessonComponent;

    /**
     * @ORM\ManyToOne(targetEntity=Lesson::class, inversedBy="lessonComponentInstances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lesson;

    /**
     * @ORM\OneToMany(targetEntity=LessonProgress::class, mappedBy="activeComponent")
     */
    private $lessonProgress;

    /**
     * @ORM\Column(type="integer")
     */
    private $sequence;

    public function __construct()
    {
        $this->lessonProgress = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLessonComponent(): ?LessonComponent
    {
        return $this->lessonComponent;
    }

    public function setLessonComponent(?LessonComponent $lessonComponent): self
    {
        $this->lessonComponent = $lessonComponent;

        return $this;
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
            $lessonProgress->setActiveComponent($this);
        }

        return $this;
    }

    public function removeLessonProgress(LessonProgress $lessonProgress): self
    {
        if ($this->lessonProgress->contains($lessonProgress)) {
            $this->lessonProgress->removeElement($lessonProgress);
            // set the owning side to null (unless already changed)
            if ($lessonProgress->getActiveComponent() === $this) {
                $lessonProgress->setActiveComponent(null);
            }
        }

        return $this;
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }
}
