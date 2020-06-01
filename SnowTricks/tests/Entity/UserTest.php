<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{

    use FixturesTrait;

    public function getEntity(): User
    {
        return (new User())
        ->setUsername('Mohamed')
        ->setEmail('mohamed@gmail.com')
        ->setPassword('12345678');
    }   

    public function assertHasErrors(User $user,int $number = 0)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($user);
        $this->assertCount($number,$error);
    }
    
    public function testValidEntity(){
        $this->assertHasErrors($this->getEntity(),0);
    }

    public function testInvalidEntity(){
        $this->assertHasErrors($this->getEntity()
        ->setPassword('sasa'),1);
    }

    public function testNotBlankEmailEntity(){
        $this->assertHasErrors($this->getEntity()
        ->setEmail(''),1);
    }

}