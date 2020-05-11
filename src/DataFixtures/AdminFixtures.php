<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Admin;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) 
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $objectManager)
    {
        $man = new Admin();
        $hash = $this->passwordEncoder->encodePassword($man, "online@2017");

        $man->setCompany("Vesoul Édition")
        ->setLibelle("Boite Postale 1038")
        ->setCity("Vesoul Cedex")
        ->setCp("70001")
        ->setCountry("France")
        ->setEmail("vesouledition@sfr.fr")
        ->setTel("0699658600")
        ->setUsername("root")
        ->setRoles(["ROLE_ADMIN"])
        ->setPassword($hash);

        $objectManager->persist($man);
        $objectManager->flush();
    }
}