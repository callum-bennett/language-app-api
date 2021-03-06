<?php

namespace App\Entity;

use App\Repository\LessonComponentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonComponentRepository::class)
 */
class LessonComponent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=LessonComponentInstance::class, mappedBy="lessonComponent")
     */
    private $lessonComponentInstances;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $shortname;

    /**
     * @ORM\Column(type="boolean")
     */
    private $requiresInput = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hintsAvailable = false;

    public function __construct()
    {
        $this->lessonComponentInstances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $lessonComponentInstance->setLessonComponent($this);
        }

        return $this;
    }

    public function removeLessonComponentInstance(LessonComponentInstance $lessonComponentInstance): self
    {
        if ($this->lessonComponentInstances->contains($lessonComponentInstance)) {
            $this->lessonComponentInstances->removeElement($lessonComponentInstance);
            // set the owning side to null (unless already changed)
            if ($lessonComponentInstance->getLessonComponent() === $this) {
                $lessonComponentInstance->setLessonComponent(null);
            }
        }

        return $this;
    }

    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname): self
    {
        $this->shortname = $shortname;

        return $this;
    }

    public function getRequiresInput(): ?bool
    {
        return $this->requiresInput;
    }

    public function setRequiresInput(bool $requiresInput): self
    {
        $this->requiresInput = $requiresInput;

        return $this;
    }

    public function getHintsAvailable(): ?bool
    {
        return $this->hintsAvailable;
    }

    public function setHintsAvailable(bool $hintsAvailable): self
    {
        $this->hintsAvailable = $hintsAvailable;

        return $this;
    }
}
