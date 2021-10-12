<?php

namespace AppBundle\DataFixtures\ORM;

use Faker\Factory as Faker;

use AppBundle\Model\Brand;
use AppBundle\Model\Category;
use AppBundle\Model\User;
use AppBundle\Model\Group;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppFixtures extends Fixture
{

    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();

        // Brand
        for($i = 1; $i<= 100; $i++){
            $brand = new Brand();
            $brand->setName($faker->name);
            $brand->setDescription($faker->text);
            $manager->persist($brand);
            $manager->flush();
        }

        // Category
        for($i = 1; $i<= 100; $i++){
            $category = new Category();
            $category->setName($faker->name);
            $category->setDescription($faker->text);
            $manager->persist($category);
            $manager->flush();
        }

        // Roles
        $role = array(
            "User"=>"ROLE_USER",
            "Admin"=>"ROLE_ADMIN"
        );
        foreach($role as $key => $row){
            $group = new Group($key);
            $group->addRole($row);
            $manager->persist($group);
            $manager->flush();
            $max = 1;
            if($row!="ROLE_ADMIN")
                $max = 99;
            $this->createUser($faker,$manager,$group,$max);
        }

    }


    private function createUser($faker, $manager, $group, $max){
         $userManager = $this->container->get('fos_user.user_manager');
         for($i = 1; $i <= $max; $i++){
             $username = $faker->unique()->userName;
             $email =  $faker->unique()->safeEmail;
             $password = "secret";
             $user = $userManager->createUser();
             $user->setUsername($username);
             $user->setEmail($email);
             $user->setEmailCanonical($email);
             $user->setEnabled(1); 
             $user->setPlainPassword($password);
             $user->addGroup($group);
             $userManager->updateUser($user);

         }
    }
   

}