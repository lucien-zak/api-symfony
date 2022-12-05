<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\SubscibeGroupStateProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
#[ApiResource(
    formats: ['json'], 
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['groupList']],
            uriTemplate: '/groups/users',
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['groupName']],
        ),
        new Put(
            processor: SubscibeGroupStateProcessor::class,
            uriTemplate: '/groups/subscribe/{id}',
            openapiContext: [
                'summary' => 'Permet de s\'inscrire à un groupe', 
                'description' => 'Permet de s\'inscrire à un groupe', 
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object', 
                                'properties' => []
                            ], 
                            'example' => []
                        ]
                    ]
                ]
            ]

        )
    ]
)]
#[ORM\HasLifecycleCallbacks]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['groupName','groupList','detail'])]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups('groupList')]
    #[ORM\OneToMany(mappedBy: 'group_id', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setGroupId($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGroupId() === $this) {
                $user->setGroupId(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function updatedTimestamps(): void
    {
        $dateTimeNow = new \DateTimeImmutable;

        // $this->setUpdatedAt($dateTimeNow);
        $this->setCreatedAt($dateTimeNow);

    }

    
}
