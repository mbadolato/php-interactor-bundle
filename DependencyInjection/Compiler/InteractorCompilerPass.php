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

class InteractorCompilerPass implements CompilerPassInterface
{
    const INTERACTOR_DIRECTORY_TAG      = 'php-interactor.directory';
    const INTERACTOR_DISPATCHER         = 'php-interactor.dispatcher';
    const GET_INTERACTORS_METHOD        = 'getInteractorMap';
    const REGISTER_INTERACTORS_METHOD   = 'register';

    /** @var ContainerBuilder */
    private $container;

    /** {@inheritDoc} */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        if ($this->interactorManagerIsDefined()) {
            $this->mapInteractors();
            $this->mapInteractorDependencies();
        }
    }

    private function getDispatcher()
    {
        return $this->container->findDefinition(self::INTERACTOR_DISPATCHER);
    }

    private function getServiceInteractors($serviceId)
    {
        $definition = $this->container->findDefinition($serviceId);
        $className  = $definition->getClass();
        $arguments  = $definition->getArguments();

        return ReflectionHelper::invokeMethod($className, self::GET_INTERACTORS_METHOD, $arguments);
    }

    private function getTaggedServices($tagName)
    {
        return $this->container->findTaggedServiceIds($tagName);
    }

    private function interactorManagerIsDefined()
    {
        return $this->container->has(self::INTERACTOR_DISPATCHER);
    }

    private function mapInteractors()
    {
        foreach ($this->getTaggedServices(self::INTERACTOR_DIRECTORY_TAG) as $serviceId => $attributes) {
            $this->registerInteractors($this->getServiceInteractors($serviceId));
        }
    }

    private function mapInteractorDependencies()
    {
        // Get tagged services for global dependencies
        // Register global dependencies

        // Get tagged services for interactor-specific dependencies
        // Register interactor-specific dependencies

        // Register dependency map with dispatcher

        // Do we want the tag to be interactor.dependency.global and interactor.dependency.interactor(?)
        // or would we rather just have interactor.dependency then a tag attribute as "global" and "interactor"
        // or maybe interactor.dependency and it's considered local unless a type="global" attribute exists?
        // or better yet, since we need to specify which interactor it's for, maybe just interactor.dependency
        // and that's considered global unless an interactor="..." attribute appears to make it interactor-specific
    }

    private function registerInteractors(InteractorMap $interactors)
    {
        foreach ($interactors->iterator() as $className => $interactorName) {
            $this->getDispatcher()->addMethodCall(self::REGISTER_INTERACTORS_METHOD, [$className, $interactorName]);
        }
    }
}
