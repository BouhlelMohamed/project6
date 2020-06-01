<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture 
{
    public function load(ObjectManager $manager){
        for($i = 0; $i < 10; $i++){
            $user = new User();
            $user
            ->setUsername("moha$i")
            ->setEmail("user$i@gmail.com")
            ->setActive(0)
            ->setPassword("jifrjgoer$i");
            $manager->persist($user);
        }
        $manager->flush();
    }
}