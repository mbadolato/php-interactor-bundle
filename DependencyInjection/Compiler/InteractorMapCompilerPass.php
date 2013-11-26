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

    private function getTaggedServices()
    {
        return $this->container->findTaggedServiceIds($this->container->getParameter('php-interactor.tag.directory'));
    }

    private function interactorDispatcherIsDefined()
    {
        return $this->container->has($this->container->getParameter('php-interactor.tag.dispatcher'));
    }

    private function mapInteractors()
    {
        $interactorMap = new InteractorMap();

        foreach ($this->getTaggedServices() as $serviceId => $taggedAttributes) {
            $interactorMap->addMap($this->getServiceInteractors($serviceId)->getMap());
        }

        $definition = $this->getServiceDefinition($this->container->getParameter('php-interactor.tag.dispatcher'));
        $definition->addMethodCall(Dispatcher::REGISTER_INTERACTORS_METHOD, [$interactorMap]);
    }
}
