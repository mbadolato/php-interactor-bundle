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

use PhpInteractor\Dispatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InteractorDependencyCompilerPass implements CompilerPassInterface
{
    const DEPENDENCY_TAG = 'php_interactor.tag.dependency';
    const DISPATCHER_TAG = 'php_interactor.tag.dispatcher';

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

    private function getDispatcherDefinition()
    {
        return $this->getServiceDefinition($this->container->getParameter(self::DISPATCHER_TAG));
    }

    private function getInteractorName(array $attributes)
    {
        return $this->isInteractorDependency($attributes) ? $attributes[0]['interactor'] : null;
    }

    private function getServiceDefinition($definitionId)
    {
        return $this->container->findDefinition($definitionId);
    }

    private function getTaggedServices()
    {
        return $this->container->findTaggedServiceIds($this->container->getParameter(self::DEPENDENCY_TAG));
    }

    private function interactorDispatcherIsDefined()
    {
        return $this->container->hasDefinition($this->container->getParameter(self::DISPATCHER_TAG));
    }

    private function isInteractorDependency(array $attributes)
    {
        return array_key_exists('interactor', $attributes[0]);
    }

    private function mapInteractorDependencies()
    {
        foreach ($this->getTaggedServices() as $serviceId => $attributes) {
            $this->registerDependencies($serviceId, $attributes);
        }
    }

    private function registerDependencies($serviceId, $attributes)
    {
        $arguments = $this->getServiceDefinition($serviceId)->getArguments();

        foreach ($arguments as $definition) {
            $this->registerDependency($attributes, $definition);
        }
    }

    private function registerDependency($attributes, array $definition)
    {
        foreach ($definition as $name => $value) {
            $parameters = [$name, $value, $this->getInteractorName($attributes)];
            $this->getDispatcherDefinition()->addMethodCall(Dispatcher::REGISTER_DEPENDENCIES_METHOD, $parameters);
        }
    }
}
