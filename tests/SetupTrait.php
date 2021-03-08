<?php

namespace App\Tests;

use symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Trait Setup
 *
 * @package App\Tests
 */
trait SetupTrait
{

    /**
     * Avoid
     * """
     *  Calling
     *  "Symfony\Bundle\FrameworkBundle\Test\WebTestCase::createClient()" while
     *  a kernel has been booted is deprecated since Symfony 4.4 and will throw
     *  an exception in 5.0, ensure the kernel is shut down before calling the
     *  method.
     *  """
     *  deprecation notice
     *
     * @return void
     */
    public static function kernelShutdown(): void
    {
        static::ensureKernelShutdown();
    }
}

