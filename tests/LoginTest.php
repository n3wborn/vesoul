<?php

declare(strict_types=1);

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class LoginTest
 *
 * @package App\Tests
 */
class LoginTest extends WebTestCase
{
    use SetupTrait;

    public function testGood()
    {
        static::kernelShutdown();

        /** @var KernelBrowser $client */
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET,
            $urlGenerator->generate("login"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter('.form-signin')->form([
            'username' => 'user+1@email.com',
            'password' => 'password'
        ]);


        /** submit form  and follow 302 redirection */
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        /** test if we landed where user should once logged in */
        $this->assertRouteSame("home");
    }



    /**
     * @dataProvider provideBadCredentials
     * @param array $formData
     * @param string $errorMessage
     */
    public function testBad(array $formData, string $errorMessage)
    {
        static::kernelShutdown();

        /** @var KernelBrowser $client */
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** get to login page and expect HTTP 200 */
        $crawler = $client->request(Request::METHOD_GET,
            $urlGenerator->generate("login"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        /** fill login form with provided values */
        $form = $crawler->filter('.form-signin')->form($formData);

        $client->submit($form);

        /** expect HTTP 302 lead us back to login page */
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame("login");

        /** Test if we have a "correct" error message */
        $this->assertSelectorTextContains('div .alert', $errorMessage);
    }


    /**
     * @return Generator
     */
    public function provideBadCredentials(): Generator
    {
        /* bad password */
        yield [
            [
                "username" => "user+1@email.com",
                "password" => "bad"
            ],
            'Identifiants invalides.'
        ];

        /* bad username */
        yield [
            [
                "username" => "bad",
                "password" => "password"
            ],
            'Identifiants invalides.'
        ];

        /* empty username and password */
        yield [
            [
                "username" => "",
                "password" => ""
            ],
            'Identifiants invalides.'
        ];
    }

}
