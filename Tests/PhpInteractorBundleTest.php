<?php

/**
 * This file is part of the Case Management System package
 *
 * @package    CaseManagementSystem
 * @subpackage BundleName
 * @author     Mark Badolato <mbadolato@cainandassociates.com>
 * @copyright  Copyright (c) Cain and Associates, Inc. All rights reserved.
 */

namespace PhpInteractor\PhpInteractorBundle\Tests;

use IC\Bundle\Base\TestBundle\Test\BundleTestCase;
use PhpInteractor\PhpInteractorBundle\PhpInteractorBundle;
use Symfony\Component\Config\Resource\FileResource;

class PhpInteractorBundleTest extends BundleTestCase
{
    /** @test */
    public function build()
    {
        $bundle = new PhpInteractorBundle();
        $bundle->build($this->container);

        $resources = $this->container->getResources();
        $this->assertCount(2, $resources);

        /** @var FileResource $interactorMapResource */
        $interactorMapResource = $resources[0];
        $parts = explode('/', $interactorMapResource->getResource());
        $this->assertEquals('InteractorMapCompilerPass.php', end($parts));

        /** @var FileResource $interactorDependencyResource */
        $interactorDependencyResource = $resources[1];
        $parts = explode('/', $interactorDependencyResource->getResource());
        $this->assertEquals('InteractorDependencyCompilerPass.php', end($parts));
    }
}
