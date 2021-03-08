<?php

declare(strict_types=1);

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class StaticPagesTest extends WebTestCase
{

    use SetupTrait;

    /**
     * StaticPagesTest check only the simplest pages that must respond with
     * 200 for every kind of user.
     *
     * @dataProvider provideUri
     * @param string $uri
     */
    public function test(string $uri)
    {
        static::kernelShutdown();

        /** @var KernelBrowser $client */
        $client = static::createClient();

        $client->request(Request::METHOD_GET, $uri);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    /**
     * @return Generator
     */
    public function provideUri(): Generator
    {
        static::kernelShutdown();

        /** @var KernelBrowser $client */
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** generate urls */
        $home = $urlGenerator->generate("home");
        $connexion = $urlGenerator->generate("login");
        $registration = $urlGenerator->generate("registration");

        /** yield every generated urls */
        yield ["$home"];
        yield ["$connexion"];
        yield ["$registration"];
    }
}
