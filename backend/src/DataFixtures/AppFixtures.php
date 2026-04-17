<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $password = 'ChangeMe123!';

        $admin = (new User())
            ->setEmail('admin1@example.com')
            ->setRoles([User::ROLE_ADMINISTRATOR]);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));

        $manager->persist($admin);

        $managerUser = (new User())
            ->setEmail('manager@example.com')
            ->setRoles([User::ROLE_MANAGER]);
        $managerUser->setPassword($this->passwordHasher->hashPassword($managerUser, $password));

        $manager->persist($managerUser);

        $member = (new User())
            ->setEmail('member@example.com')
            ->setRoles([User::ROLE_MEMBER]);
        $member->setPassword($this->passwordHasher->hashPassword($member, $password));

        $manager->persist($member);

        $manager->flush(true);
    }
}
