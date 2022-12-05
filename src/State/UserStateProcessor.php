<?php

namespace App\State;

use App\Entity\User;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserStateProcessor implements ProcessorInterface
{
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
    $this->em = $entityManager;
    $this->passwordHasher = $passwordHasher;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof User === false) {
            return;
        };
        if ($operation instanceof Post) {
            $data->setCreatedAt(new \DateTimeImmutable());
            $data->setUpdatedAt(new \DateTimeImmutable());
            $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPassword()));
        }
        if ($operation instanceof Patch || $operation instanceof Put) {
            $data->setUpdatedAt(new \DateTimeImmutable());
        }
        $this->em->persist($data);
        $this->em->flush();
    }
}
