<?php

namespace App\State;

use App\Entity\Group;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class SubscibeGroupStateProcessor implements ProcessorInterface
{
    public function __construct(private Security $security, private ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
    }
    
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->security->getUser();
        if ($user === null) {
            return;
        }
        $entityManager = $this->doctrine->getManager();
        $data->addUser($user);
        $entityManager->persist($data);
        $entityManager->flush();
    }
}
