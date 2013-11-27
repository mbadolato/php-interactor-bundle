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
use PhpInteractor\InteractorMap;
use PhpInteractor\PhpInteractorBundle\DependencyInjection\Compiler\InteractorMapCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class InteractorMapCompilerPassTest extends AbstractCompilerPassTestCase
{
    /** @var InteractorMapCompilerPass */
    private $compilerPass;

    /** @var string */
    private $directory;

    /** @test */
    public function process()
    {
        $this->setDefinition('php-interactor.dispatcher', new Definition());

        $directory = new Definition();
        $directory->setArguments([$this->directory]);
        $directory->setClass('PhpInteractor\DirectoryProcessor');
        $directory->addTag('php-interactor.directory');
        $this->setDefinition('php-interactor.directory.test', $directory);

        $this->compile();

        $map = new InteractorMap();
        $map->set('TestInteractor', 'PhpInteractor\PhpInteractorBundle\Tests\Helper\Interactor\TestInteractor');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'php-interactor.dispatcher',
            'setInteractorMap',
            [$map]
        );
    }

    /** {@inheritDoc} */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $this->container->addCompilerPass(new InteractorMapCompilerPass());
    }

    /** {@inheritDoc} */
    protected function setUp()
    {
        parent::setUp();

        $this->compilerPass = new InteractorMapCompilerPass();
        $this->directory    = __DIR__ . '/../../Helper/Interactor';
    }
}
