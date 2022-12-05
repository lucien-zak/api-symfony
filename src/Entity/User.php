<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use App\State\UserStateProcessor;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\RegistrationController;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[Post(processor: UserStateProcessor::class)]
#[ApiResource(
    formats: ['json'],    
    operations:[
        new Post(),
        new GetCollection(
            normalizationContext: ['groups' => ['list']]
        ),
        new Get(
            normalizationContext: ['groups' => ['detail']],
            uriTemplate: '/users/{id}',
            security: 'is_granted("ROLE_USER")',
            openapiContext: [
                'summary' => 'Donne les informations d\'un utilisateur', 
                'description' => 'Donne les informations d\'un utilisateur', 
                // 'requestBody' => [
                //     'content' => [
                //         'application/json' => [
                //             'schema' => [
                //                 'type' => 'object', 
                //                 'properties' => [
                //                     'name' => ['type' => 'string'], 
                //                     'description' => ['type' => 'string']
                //                 ]
                //             ], 
                //             'example' => [
                //                 'name' => 'Mr. Rabbit', 
                //                 'description' => 'Pink Rabbit'
                //             ]
                //         ]
                //     ]
                // ]
            ]
        ),
    ]
)]
// - Une route qui retourne les groupes ainsi que les users qui y sont rattachés
// (uniquement prenom et nom)
// - Une route qui permet d’avoir les détails d’un user (nom prenom email groupe)
// - Une route qui permet aux utilisateur de s’ajouter à un groupe
// - Une route qui permet de modifier ses informations (uniquement celles de
// l’utilisateur connecté)
// - Les routes qui permettent de supprimer, modifier un user et sélectionner tous les
// users.
// - Les routes qui permettent d’ajouter, modifier, supprimer un groupe
// - Une route qui permet de modifier les utilisateurs présents dans un groupe

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['detail'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['list','groupList','detail'])]
    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[Groups(['list', 'groupList','detail'])]
    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['listWithGroups','detail'])]
    private ?Group $group_id = null;

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
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getGroupId(): ?Group
    {
        return $this->group_id;
    }

    public function setGroupId(?Group $group_id): self
    {
        $this->group_id = $group_id;

        return $this;
    }
}
