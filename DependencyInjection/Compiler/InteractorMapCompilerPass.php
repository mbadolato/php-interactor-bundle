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

use PhpInteractor\InteractorMap;
use PhpInteractor\PhpInteractorBundle\Helper\ReflectionHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InteractorMapCompilerPass implements CompilerPassInterface
{
    const INTERACTOR_DIRECTORY_TAG      = 'php-interactor.directory';
    const INTERACTOR_DISPATCHER         = 'php-interactor.dispatcher';
    const GET_INTERACTORS_METHOD        = 'getInteractorMap';
    const REGISTER_INTERACTORS_METHOD   = 'setInteractorMap';

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
        $className  = $definition->getClass();
        $arguments  = $definition->getArguments();

        return ReflectionHelper::invokeMethod($className, self::GET_INTERACTORS_METHOD, $arguments);
    }

    private function getTaggedServices()
    {
        return $this->container->findTaggedServiceIds(self::INTERACTOR_DIRECTORY_TAG);
    }

    private function interactorDispatcherIsDefined()
    {
        return $this->container->has(self::INTERACTOR_DISPATCHER);
    }

    private function mapInteractors()
    {
        $interactorMap = new InteractorMap();

        foreach ($this->getTaggedServices() as $serviceId => $taggedAttributes) {
            $interactorMap->addMap($this->getServiceInteractors($serviceId)->getMap());
        }

        $definition = $this->getServiceDefinition(self::INTERACTOR_DISPATCHER);
        $definition->addMethodCall(self::REGISTER_INTERACTORS_METHOD, [$interactorMap]);
    }
}
