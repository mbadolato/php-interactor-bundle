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
        $this->assertCount(1, $resources);

        /** @var FileResource $resource */
        $resource = $resources[0];
        $parts = explode('/', $resource->getResource());
        $this->assertEquals('InteractorCompilerPass.php', end($parts));
    }
}
