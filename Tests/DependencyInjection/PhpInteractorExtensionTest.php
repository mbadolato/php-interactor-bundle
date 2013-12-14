<?php

/**
 * This file is part of the PhpInteractorBundle package
 *
 * @package    PhpInteractorBundle
 * @author     Mark Badolato <mbadolato@cybernox.com>
 * @copyright  Copyright (c) CyberNox Technologies. All rights reserved.
 * @license    http://www.opensource.org/licenses/MIT MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpInteractor\PhpInteractorBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PhpInteractor\PhpInteractorBundle\DependencyInjection\PhpInteractorExtension;

class PhpInteractorExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function tagParameters()
    {
        $this->load();
        $this->assertContainerBuilderHasParameter('php_interactor.tag.dependency', 'php_interactor.dependency');
        $this->assertContainerBuilderHasParameter('php_interactor.tag.directory', 'php_interactor.directory');
        $this->assertContainerBuilderHasParameter('php_interactor.tag.dispatcher', 'php_interactor');
    }

    /** {@inheritDoc} */
    protected function getContainerExtensions()
    {
        return [new PhpInteractorExtension()];
    }
}
