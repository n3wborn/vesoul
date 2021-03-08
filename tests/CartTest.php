<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * Class CartTest
 * @package  App\Tests
 */
class CartTest extends WebTestCase
{

    use SetupTrait;

    /**
     * Test if a user with empty cart is redirected if trying to access the
     * order page
     */
    public function testRedirectfromOrderIfEmptyCart()
    {
        static::kernelShutdown();

        /** @var KernelBrowser $client */
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** request random product page*/
        $client->request(Request::METHOD_GET, $urlGenerator->generate("order"));

        /** expect a 302 and follow redirection */
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        /** test if we landed where user should if his cart is empty */
        $this->assertRouteSame("login");
    }
}

