<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;


class AddressesFixtures extends Fixture implements DependentFixtureInterface
{

    private Generator  $faker;

    private int $autoIncrement;


    public function __construct()
    {
        $this->faker = Factory::create("fr_FR");
        $this->autoIncrement = 0;
    }


    /**
     * @param  ObjectManager $objectManager
     * @return void
     */
    public function load(ObjectManager $objectManager): void
    {
        /**
         * @var User[] $users
         */
        $users = $objectManager
            ->getRepository(User::class)
            ->findBy(['roles' => ['ROLE_USER']]);

        /**
         * @var User $admin
         */
        $admin = $objectManager
            ->getRepository(User::class)
            ->findOneBy(['roles' => ['ROLE_ADMIN']]);

        /**
         * add adresses to each user
         */
        foreach ($users as $user) {
            $objectManager->persist($this->createAddresses($user));
        }

        /**
         * add an address to the admin
         */
        $objectManager->persist($this->createAdminAddress($admin));


        $objectManager->flush();

    }


    /**
     * @param  User $user
     * @return Address
     */
    public function createAddresses(User $user): Address
    {
        /**
         * @var Address $address
         */
        $address = (new Address())
            ->setTitle(sprintf("address+%d", $this->autoIncrement))
            ->setType($this->faker->secondaryAddress)
            ->setNumber($this->faker->buildingNumber)
            ->setStreet($this->faker->streetName)
            ->setCity($this->faker->city)
            ->setCp($this->faker->postcode)
            ->setCountry($this->faker->country)
            ->setAdditional($this->faker->secondaryAddress)
            ->setFirstname($this->faker->firstNameMale)
            ->setLastname($this->faker->username)
            ->setUser($user);

        $this->autoIncrement++;

        return $address;
    }


    /**
     * @param  User $user
     * @return Address
     */
    public function createAdminAddress(User $user): Address
    {

        /**
         * @var Address $AdminAddress
         */
        $adminAddress = (new Address())
            ->setTitle("VesoulEdition")
            ->setType("")
            ->setNumber("")
            ->setStreet("Boite Postale 1038")
            ->setCity("Vesoul Cedex")
            ->setCp("70001")
            ->setCountry("France")
            ->setFirstname("Christian")
            ->setLastname("Petit")
            ->setUser($user);

        return $adminAddress;
    }


    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
