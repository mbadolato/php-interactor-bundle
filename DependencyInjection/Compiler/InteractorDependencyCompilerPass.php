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

namespace PhpInteractor\PhpInteractorBundle\DependencyInjection\Compiler;

use PhpInteractor\DependencyCoordinator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InteractorDependencyCompilerPass implements CompilerPassInterface
{
    const INTERACTOR_DEPENDENCY_TAG             = 'php-interactor.dependency';
    const INTERACTOR_DISPATCHER                 = 'php-interactor.dispatcher';
    const REGISTER_DEPENDENCIES_METHOD          = 'setDependencyCoordinator';
    const REGISTER_DEPENDENCY_GLOBAL_METHOD     = 'registerGlobalDependency';
    const REGISTER_DEPENDENCY_INTERACTOR_METHOD = 'registerInteractorDependency';

    /** @var ContainerBuilder */
    private $container;

    /** {@inheritDoc} */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        if ($this->interactorDispatcherIsDefined()) {
            $this->mapInteractorDependencies();
        }
    }

    private function getDependencyRegistrationMethodName($interactorName)
    {
        return $interactorName ? self::REGISTER_DEPENDENCY_INTERACTOR_METHOD : self::REGISTER_DEPENDENCY_GLOBAL_METHOD;
    }

    private function getDispatcher()
    {
        return $this->container->findDefinition(self::INTERACTOR_DISPATCHER);
    }

    private function getInteractorName(array $attributes)
    {
        return $this->isInteractorDependency($attributes) ? $attributes[0]['interactor'] : null;
    }

    private function getTaggedServices($tagName)
    {
        return $this->container->findTaggedServiceIds($tagName);
    }

    private function interactorDispatcherIsDefined()
    {
        return $this->container->has(self::INTERACTOR_DISPATCHER);
    }

    private function isInteractorDependency(array $attributes)
    {
        return array_key_exists('interactor', $attributes[0]);
    }

    private function mapInteractorDependencies()
    {
        $dependencyCoordinator = new DependencyCoordinator();

        foreach ($this->getTaggedServices(self::INTERACTOR_DEPENDENCY_TAG) as $serviceId => $taggedAttributes) {
            $this->registerDependencies($taggedAttributes, $serviceId, $dependencyCoordinator);
        }

        $this->getDispatcher()->addMethodCall(self::REGISTER_DEPENDENCIES_METHOD, [$dependencyCoordinator]);
    }

    private function registerDependencies($attributes, $serviceId, $dependencies)
    {
        $interactorName = $this->getInteractorName($attributes);
        $methodToCall   = $this->getDependencyRegistrationMethodName($interactorName);
        $arguments      = $this->container->findDefinition($serviceId)->getArguments();

        foreach ($arguments as $argument) {
            $dependencies->$methodToCall($argument['key'], $argument['id'], $interactorName);
        }
    }
}
