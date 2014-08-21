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
use PhpInteractor\PhpInteractorBundle\DependencyInjection\Compiler\InteractorDependencyCompilerPass;
use PhpInteractor\PhpInteractorBundle\DependencyInjection\Compiler\InteractorMapCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class InteractorDependencyCompilerPassTest extends AbstractCompilerPassTestCase
{
    const G_DEPENDENCY_NAME     = 'global_dependency_name';
    const G_DEPENDENCY_VALUE    = 'global_dependency_service_id';
    const I_DEPENDENCY_NAME     = 'interactor_dependency_name';
    const I_DEPENDENCY_VALUE    = 'interactor_dependency_service_id';
    const I_NAME                = 'interactor_name';

    /** @var InteractorMapCompilerPass */
    private $compilerPass;

    /** @var string */
    private $directory;

    /** @test */
    public function process()
    {
        $this->setDefinition( $this->container->getParameter('php_interactor.tag.dispatcher'), new Definition());
        $this->setDefinition('php_interactor.dependency.global', $this->getGlobalDependencyDefinition());
        $this->setDefinition('php_interactor.dependency.interactor_specific', $this->getInteractorDependencyDefinition());
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $this->container->getParameter('php_interactor.tag.dispatcher'),
            'registerDependency',
            [self::G_DEPENDENCY_NAME, self::G_DEPENDENCY_VALUE, null]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $this->container->getParameter('php_interactor.tag.dispatcher'),
            'registerDependency',
            [self::I_DEPENDENCY_NAME, self::I_DEPENDENCY_VALUE, self::I_NAME]
        );
    }

    /** {@inheritDoc} */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $this->container->addCompilerPass(new InteractorDependencyCompilerPass());
    }

    /** {@inheritDoc} */
    protected function setUp()
    {
        parent::setUp();

        $this->compilerPass = new InteractorDependencyCompilerPass();
        $this->directory    = __DIR__ . '/../../Helper/Interactor';

        $this->container->setParameter('php_interactor.tag.dependency', 'php_interactor.dependency');
        $this->container->setParameter('php_interactor.tag.dispatcher', 'php_interactor.dispatcher');
    }

    private function getGlobalDependencyDefinition()
    {
        $definition = new Definition();
        $definition->addArgument([self::G_DEPENDENCY_NAME => self::G_DEPENDENCY_VALUE]);
        $definition->setClass('PhpInteractor\DependencyCoordinator');
        $definition->addTag($this->container->getParameter('php_interactor.tag.dependency'));

        return $definition;
    }

    private function getInteractorDependencyDefinition()
    {
        $definition = new Definition();
        $definition->addArgument([self::I_DEPENDENCY_NAME => self::I_DEPENDENCY_VALUE]);
        $definition->setClass('PhpInteractor\DependencyCoordinator');
        $definition->addTag($this->container->getParameter('php_interactor.tag.dependency'), ['interactor' => self::I_NAME]);

        return $definition;
    }
}
