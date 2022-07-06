<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $endDate;

    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private $status;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasks')]
    private $responsible;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private $project;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Regex(pattern: '/\d+[дчмДЧМ]+/', message: 'Затраченное время не соответствует шаблону')]
    private $spenttime;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $createdUser;

    #[ORM\ManyToOne(targetEntity: TaskType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $type;

    #[ORM\ManyToOne(targetEntity: TaskPriority::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $priority;

    #[ORM\Column(type: 'string', length: 255)]
    private $spentTimeHours;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getResponsible(): ?User
    {
        return $this->responsible;
    }

    public function setResponsible(?User $responsible): self
    {
        $this->responsible = $responsible;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getSpenttime(): ?string
    {
        return $this->spenttime;
    }

    public function setSpenttime(?string $spenttime): self
    {
        $this->spenttime = $spenttime;

        return $this;
    }

    public function getCreatedUser(): ?User
    {
        return $this->createdUser;
    }

    public function setCreatedUser(?User $createdUser): self
    {
        $this->createdUser = $createdUser;

        return $this;
    }

    public function __toString(): string
    {
        return '# ' . $this->getId() . " " . $this->getName();
    }

    #[ORM\PrePersist]
    public function onBeforeSave(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getType(): ?TaskType
    {
        return $this->type;
    }

    public function setType(?TaskType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPriority(): ?TaskPriority
    {
        return $this->priority;
    }

    public function setPriority(?TaskPriority $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getSpentTimeHours(): ?string
    {
        return $this->spentTimeHours;
    }

    public function setSpentTimeHours(string $spentTimeHours): self
    {
        $this->spentTimeHours = $spentTimeHours;

        return $this;
    }
}
