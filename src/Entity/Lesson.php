<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonRepository::class)
 */
class Lesson
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="lessons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="integer")
     */
    private $sequence;

    /**
     * @ORM\OneToMany(targetEntity=Word::class, mappedBy="lesson")
     */
    private $words;

    /**
     * @ORM\OneToMany(targetEntity=LessonComponentInstance::class, mappedBy="lesson")
     */
    private $lessonComponentInstances;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function __construct()
    {
        $this->words = new ArrayCollection();
        $this->lessonComponentInstances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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

    /**
     * @return Collection|Word[]
     */
    public function getWords(): Collection
    {
        return $this->words;
    }

    public function addWord(Word $word): self
    {
        if (!$this->words->contains($word)) {
            $this->words[] = $word;
            $word->setLesson($this);
        }

        return $this;
    }

    public function removeWord(Word $word): self
    {
        if ($this->words->contains($word)) {
            $this->words->removeElement($word);
            // set the owning side to null (unless already changed)
            if ($word->getLesson() === $this) {
                $word->setLesson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LessonComponentInstance[]
     */
    public function getLessonComponentInstances(): Collection
    {
        return $this->lessonComponentInstances;
    }

    public function addLessonComponentInstance(LessonComponentInstance $lessonComponentInstance): self
    {
        if (!$this->lessonComponentInstances->contains($lessonComponentInstance)) {
            $this->lessonComponentInstances[] = $lessonComponentInstance;
            $lessonComponentInstance->setLesson($this);
        }

        return $this;
    }

    public function removeLessonComponentInstance(LessonComponentInstance $lessonComponentInstance): self
    {
        if ($this->lessonComponentInstances->contains($lessonComponentInstance)) {
            $this->lessonComponentInstances->removeElement($lessonComponentInstance);
            // set the owning side to null (unless already changed)
            if ($lessonComponentInstance->getLesson() === $this) {
                $lessonComponentInstance->setLesson(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
