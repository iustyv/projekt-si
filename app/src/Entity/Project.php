<?php
/**
 * Project entity.
 */

namespace App\Entity;

use App\Entity\Enum\UserRole;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Project.
 */
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Table(name: 'projects')]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Created at.
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Updated at.
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Name.
     */
    #[ORM\Column(type: 'string', length: 64)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 64)]
    private ?string $name = null;

    /**
     * Manager.
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Type(User::class)]
    #[Assert\NotBlank]
    private ?User $manager = null;

    /**
     * Members.
     *
     * @var ArrayCollection<int, Tag>
     */
    #[Assert\Valid]
    #[ORM\ManyToMany(targetEntity: User::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\JoinTable(name: 'project_members')]
    private Collection $members;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    /**
     * Getter for id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for created at.
     *
     * @return \DateTimeImmutable|null Created at
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     *
     * @param \DateTimeImmutable $createdAt Created at
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for updated at.
     *
     * @return \DateTimeImmutable|null Updated at
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     *
     * @param \DateTimeImmutable $updatedAt Updated at
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for name.
     *
     * @return string|null Name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setter for name.
     *
     * @param string $name Name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Getter for manager.
     *
     * @return User|null Project manager
     */
    public function getManager(): ?User
    {
        return $this->manager;
    }

    /**
     * Setter for manager.
     *
     * @param User|null $manager Project manager
     */
    public function setManager(?User $manager): void
    {
        $this->manager = $manager;
        $manager->addRole(UserRole::ROLE_PROJECT_MANAGER->value);
        $this->addMember($manager);
    }

    /**
     * Getter for members.
     *
     * @return Collection Members
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * Checks if user is a member of the project.
     *
     * @param User $user User entity
     *
     * @return bool Result
     */
    public function isMemeber(User $user)
    {
        return $this->members->contains($user);
    }

    /**
     * Add a member to project.
     *
     * @param User $member Member
     */
    public function addMember(User $member): void
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }
    }

    /**
     * Add members to project.
     *
     * @param array $members Members
     */
    public function addMembers(array $members): void
    {
        foreach ($members as $member) {
            $this->addMember($member);
        }
    }

    /**
     * Remove member from project.
     *
     * @param User $member Member
     */
    public function removeMember(User $member): void
    {
        $this->members->removeElement($member);
    }
}
