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

namespace PhpInteractor\PhpInteractorBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use PhpInteractor\PhpInteractorBundle\DependencyInjection\Compiler\InteractorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class InteractorMapCompilerPassTest extends AbstractCompilerPassTestCase
{
    /** @var InteractorCompilerPass */
    private $compilerPass;

    /** @var string */
    private $directory;

    /** @test */
    public function process()
    {
        $this->setDefinition('php-interactor.dispatcher', new Definition());

        $directory = new Definition();
        $directory->addTag('php-interactor.directory');
        $directory->setArguments([$this->directory]);
        $directory->setClass('PhpInteractor\DirectoryProcessor');
        $this->setDefinition('php-interactor.directory.test', $directory);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'php-interactor.dispatcher',
            'register',
            ['TestInteractor', 'PhpInteractor\PhpInteractorBundle\Tests\Helper\Interactor\TestInteractor']
        );
    }

    /** {@inheritDoc} */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $this->container->addCompilerPass(new InteractorCompilerPass());
    }

    /** {@inheritDoc} */
    protected function setUp()
    {
        parent::setUp();

        $this->compilerPass = new InteractorCompilerPass();
        $this->directory    = __DIR__ . '/../../Helper/Interactor';
    }
}
