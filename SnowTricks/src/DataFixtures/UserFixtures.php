<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture 
{
    public function load(ObjectManager $manager){
        $user = new User();
        for($i = 0; $i < 10; $i++){
            $user
            ->setUsername("moha$i")
            ->setEmail("user$i@gmail.com")
            ->setActive(0)
            ->setPassword("jifrjgoerk");
            $manager->persist($user);
        }
        $manager->flush();
    }
}