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

use PhpInteractor\DirectoryProcessor;
use PhpInteractor\Dispatcher;
use PhpInteractor\InteractorMap;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InteractorMapCompilerPass implements CompilerPassInterface
{
    const DIRECTORY_TAG  = 'php_interactor.tag.directory';
    const DISPATCHER_TAG = 'php_interactor.tag.dispatcher';

    /** @var ContainerBuilder */
    private $container;

    /** {@inheritDoc} */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        if ($this->interactorDispatcherIsDefined()) {
            $this->mapInteractors();
        }
    }

    private function getServiceDefinition($definitionId)
    {
        return $this->container->findDefinition($definitionId);
    }

    private function getServiceInteractors($serviceId)
    {
        $definition = $this->getServiceDefinition($serviceId);
        $reflector  = new \ReflectionClass($definition->getClass());
        $object     = $reflector->newInstanceArgs($definition->getArguments());

        return $reflector->getMethod(DirectoryProcessor::GET_INTERACTOR_MAP_METHOD)->invoke($object);
    }

    private function getDispatcherDefinition()
    {
        return $this->getServiceDefinition($this->container->getParameter(self::DISPATCHER_TAG));
    }

    private function getTaggedServices()
    {
        return $this->container->findTaggedServiceIds($this->container->getParameter(self::DIRECTORY_TAG));
    }

    private function interactorDispatcherIsDefined()
    {
        return $this->container->hasDefinition($this->container->getParameter(self::DISPATCHER_TAG));
    }

    private function mapInteractors()
    {
        foreach ($this->getTaggedServices() as $serviceId => $taggedAttributes) {
            $this->registerInteractors($this->getServiceInteractors($serviceId));
        }
    }

    private function registerInteractors(InteractorMap $interactorMap)
    {
        foreach ($interactorMap->iterator() as $name => $class) {
            $this->getDispatcherDefinition()->addMethodCall(Dispatcher::REGISTER_INTERACTORS_METHOD, [$name, $class]);
        }
    }
}
