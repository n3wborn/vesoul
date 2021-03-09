<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    private Generator $faker;

    private int $autoIncrement;


    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create("fr_FR");
        $this->autoIncrement = 0;
    }


    /**
     * @param  ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        /** create 10 users */
        for ($i = 0; $i < 10; $i++) {
            $manager->persist($this->createUser());
        }

        /** create an admin */
        $manager->persist($this->createAdmin());

        $manager->flush();
    }


    /**
     * @return User
     */
    private function createUser(): User
    {
        /** @var User $user */
        $newuser = (new User())
            ->setUsername(sprintf("user+%d@email.com", $this->autoIncrement))
            ->setFirstName($this->faker->firstName('male'))
            ->setLastName($this->faker->lastName)
            ->setTel($this->faker->phoneNumber)
            ->setRoles(['ROLE_USER'])
            ->setGender(0)
            ->setBirth(new DateTime());

        $newuser->setPassword(
            $this->passwordEncoder
                ->encodePassword($newuser, "password")
        );

        $this->autoIncrement++;

        return $newuser;
    }


    /**
     * @return User
     */
    private function createAdmin(): User
    {
        /** @var User $user */
        $admin = (new User())
            ->setUsername("admin@email.com")
            ->setFirstName("admin")
            ->setLastName("admin")
            ->setTel($this->faker->phoneNumber)
            ->setRoles(['ROLE_ADMIN'])
            ->setGender(0)
            ->setBirth(new DateTime());

        $admin->setPassword(
            $this->passwordEncoder
                ->encodePassword($admin, "password")
        );

        return $admin;
    }
}
