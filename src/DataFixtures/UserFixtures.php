<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


class UserFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $testUser = new User();
        $testUser->setName('user');
        $testUser->setRoles(['ROLE_USER']);

        $testAdmin = new User();
        $testAdmin->setName('admin');
        $testAdmin->setRoles(['ROLE_RH']);

        $manager->persist($testUser);
        $manager->persist($testAdmin);

        $manager->flush();
    }
}
