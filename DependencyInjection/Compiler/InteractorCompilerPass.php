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
use Symfony\Component\DependencyInjection\Reference;

class InteractorCompilerPass implements CompilerPassInterface
{
    const INTERACTOR_DEPENDENCY_TAG             = 'php-interactor.dependency';
    const INTERACTOR_DIRECTORY_TAG              = 'php-interactor.directory';
    const INTERACTOR_DISPATCHER                 = 'php-interactor.dispatcher';
    const GET_INTERACTORS_METHOD                = 'getInteractorMap';
    const REGISTER_DEPENDENCY_GLOBAL_METHOD     = 'registerGlobalDependency';
    const REGISTER_DEPENDENCY_INTERACTOR_METHOD = 'registerInteractorDependency';
    const REGISTER_INTERACTORS_METHOD           = 'registerInteractor';

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
        foreach ($this->getTaggedServices(self::INTERACTOR_DEPENDENCY_TAG) as $serviceId => $taggedAttributes) {
            $interactor = (array_key_exists('interactor', $taggedAttributes)) ? $taggedAttributes['interactor'] : null;
            $method     = $interactor ? self::REGISTER_DEPENDENCY_INTERACTOR_METHOD : self::REGISTER_DEPENDENCY_GLOBAL_METHOD;

            print "\nService ID is $serviceId\n";
            printf("Is Interactor: %s\n", (bool) $interactor);
            print "Method to call is $method\n";

            $definition = $this->container->findDefinition($serviceId);
            $className  = $definition->getClass();
            $arguments  = $definition->getArguments();

            print "Class is $className\n";

            foreach ($arguments as $argument) {
                printf("Type [%s], Key [%s], Id [%s]\n", $argument['type'], $argument['key'], $argument['id']);
                $reference = new Reference($argument['id']);
                $this->getDispatcher()->addMethodCall($method, [$argument['key'], $reference]);
            }

            print "-------------------------\n";
        }

        // Get tagged services for global dependencies
        // Register global dependencies

        // Get tagged services for interactor-specific dependencies
        // Register interactor-specific dependencies

        // Since we need to specify which interactor it's for, just tag as
        // interactor.dependency and that's considered global unless an
        // interactor="..." attribute appears to make it interactor-specific
    }

    private function registerInteractors(InteractorMap $interactors)
    {
        foreach ($interactors->iterator() as $className => $interactorName) {
            $this->getDispatcher()->addMethodCall(self::REGISTER_INTERACTORS_METHOD, [$className, $interactorName]);
        }
    }
}
