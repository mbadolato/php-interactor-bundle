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
use PhpInteractor\Dispatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InteractorDependencyCompilerPass implements CompilerPassInterface
{
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

    private function getDispatcher()
    {
        return $this->getServiceDefinition($this->container->getParameter('php-interactor.tag.dispatcher'));
    }

    private function getInteractorName(array $attributes)
    {
        return $this->isInteractorDependency($attributes) ? $attributes[0]['interactor'] : null;
    }

    private function getRegistrationMethodName($interactor)
    {
        return $interactor ? DependencyCoordinator::REGISTER_INTERACTOR_METHOD : DependencyCoordinator::REGISTER_GLOBAL_METHOD;
    }

    private function getServiceDefinition($definitionId)
    {
        return $this->container->findDefinition($definitionId);
    }

    private function getTaggedServices()
    {
        return $this->container->findTaggedServiceIds($this->container->getParameter('php-interactor.tag.dependency'));
    }

    private function interactorDispatcherIsDefined()
    {
        return $this->container->has($this->container->getParameter('php-interactor.tag.dispatcher'));
    }

    private function isInteractorDependency(array $attributes)
    {
        return array_key_exists('interactor', $attributes[0]);
    }

    private function mapInteractorDependencies()
    {
        $dependencyCoordinator = new DependencyCoordinator();

        foreach ($this->getTaggedServices() as $serviceId => $attributes) {
            $this->registerDependencies($serviceId, $attributes, $dependencyCoordinator);
        }

        $this->getDispatcher()->addMethodCall(Dispatcher::REGISTER_DEPENDENCIES_METHOD, [$dependencyCoordinator]);
    }

    private function registerDependencies($serviceId, $attributes, $dependencyCoordinator)
    {
        $interactorName     = $this->getInteractorName($attributes);
        $registrationMethod = $this->getRegistrationMethodName($interactorName);
        $arguments          = $this->getServiceDefinition($serviceId)->getArguments();

        foreach ($arguments as $argument) {
            $dependencyCoordinator->$registrationMethod($argument['key'], $argument['id'], $interactorName);
        }
    }
}
