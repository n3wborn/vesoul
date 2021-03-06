<?php

declare(strict_types=1);

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * RedirectionsTest
 *
 * @package App\Tests
 */
class RedirectionsTest extends WebTestCase
{
    use SetupTrait;

    /**
     * RedirectionsTest check only the simplest pages that must respond with
     * HTTP_FOUND (302) when current user isn't connected yet.
     *
     * @dataProvider provideUri
     * @param string $uri
     */
    public function test(string $uri)
    {
        static::kernelShutdown();

        /** @var KernelBrowser $client */
        $client = static::createClient();

        /** request uri and check response */
        $client->request(Request::METHOD_GET, $uri);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }


    /**
     * @return Generator
     */
    public function provideUri(): Generator
    {
        /** @var KernelBrowser $client */
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** generate urls */
        $order = $urlGenerator->generate("order");
        $userHome = $urlGenerator->generate("dashboard_user_home");
        $userInfos = $urlGenerator->generate("dashboard_user_informations");
        $userAddresses = $urlGenerator->generate("dashboard_user_addresses");
        $userOrders = $urlGenerator->generate("dashboard_user_orders");


        /** yield every generated urls */
        yield ["$order"];
        yield ["$userHome"];
        yield ["$userInfos"];
        yield ["$userAddresses"];
        yield ["$userOrders"];
    }
}
