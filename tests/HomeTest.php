<?php

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeTest extends WebTestCase
{
    /**
     * @dataProvider provideUri
     * @param string $uri
     */
    public function test(string $uri)
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, $uri);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return Generator
     */
    public function provideUri(): Generator
    {
        yield ['/'];
    }
}
