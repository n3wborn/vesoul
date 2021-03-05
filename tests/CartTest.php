<?php

namespace App\Tests;

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

    /**
     * Test if a user with empty cart is redirected if trying to access the
     * order page
     */
    public function testRedirectfromOrderIfEmptyCart()
    {
        /** avoid deprecation notice */
        static::ensureKernelShutdown();

        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $urlGenerator->generate("order"));

        /** expect a 302 and follow redirection */
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        /** test if we landed where user should if his cart is empty */
        $this->assertRouteSame("login");
    }
}

