<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $secondName;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastName;

    #[ORM\Column(type: 'datetime')]
    private $birthDate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $position;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'users')]
    private $groups;

    #[ORM\OneToMany(mappedBy: 'responsible', targetEntity: Task::class)]
    private $tasks;

    #[ORM\ManyToMany(targetEntity: Team::class, mappedBy: 'users')]
    private $teams;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSecondName(): ?string
    {
        return $this->secondName;
    }

    public function setSecondName(?string $secondName): self
    {
        $this->secondName = $secondName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function addGroup(Role $role): self
    {
        if (!$this->groups->contains($role)) {
            $this->groups[] = $role;
            $role->addUser($this);
        }

        return $this;
    }

    public function removeGroup(Role $role): self
    {
        if ($this->groups->removeElement($role)) {
            $role->removeUser($this);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param ArrayCollection $groups
     */
    public function setGroups($groups): void
    {
        $this->groups = $groups;
    }

    public function getRoles(): array
    {
        $rolesArray = ['ROLE_USER'];

        /* @var Role $role */
        foreach ($this->groups as $role) {
            $rolesArray[] = 'ROLE_' . strtoupper($role->getCode());
        }

        return array_unique($rolesArray);
    }

    public function setRoles(array $roles)
    {
        $this->groups = new ArrayCollection($roles);
    }

    public function resetRoles()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setResponsible($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getResponsible() === $this) {
                $task->setResponsible(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->addUser($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            $team->removeUser($this);
        }

        return $this;
    }

    public function getFormatName(): string
    {
        $formattedName = $this->lastName . " " . substr($this->getFirstName(), 0, 2) . ".";

        if (!empty($this->getSecondName())) {
            $formattedName .= " " . substr($this->getSecondName(), 0, 2) . ".";
        }

        return $formattedName;
    }

    public function __toString(): string
    {
        return '# ' . $this->getId() . " " . $this->getFormatName();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $passwordHasherFactory = new PasswordHasherFactory([
            PasswordAuthenticatedUserInterface::class => ['algorithm' => 'auto'],
        ]);
        $passwordHasher = new UserPasswordHasher($passwordHasherFactory);

//        dump($this);
//        die();
        $hashedPassword = $passwordHasher->hashPassword($this, $this->password);
        $this->setPassword($hashedPassword);
    }
}
