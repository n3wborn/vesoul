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
 * ProductTest
 *
 * @package App\Tests
 */
class ProductTest extends WebTestCase
{
    use SetupTrait;

    /**
     * Test if a random product details page lives
     *
     * @return Response
     */
    public function test()
    {

        static::kernelShutdown();

        /** @var KernelBrowser $client */
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator  */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $em */
        $em = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var Book $book */
        $book = $em->getRepository(Book::class)->findOneBy([]);

        /** request our random book page */
        $client->request(Request::METHOD_GET,
            $urlGenerator->generate("product", ['id' => $book->getId()]));


        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
