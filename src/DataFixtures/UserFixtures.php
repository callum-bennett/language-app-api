<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $objectManager)
    {
        foreach ($this->getUsers() as [$username, $firstname, $lastname, $password]) {

            $user = new User();
            $encodedPassword = $this->passwordEncoder->encodePassword($user, $password);

            $user->setUsername($username);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setPassword($encodedPassword);

            $objectManager->persist($user);
        }

        $objectManager->flush();
    }

    private function getUsers(): array {
        return [
            // $user = [$username, $firstname, $lastname, $password];
            ['user1', "Test", "User 1", "password.test.2020"],
            ['user2', "Test", "User 2", "password.test.2020"],
        ];
    }
}
