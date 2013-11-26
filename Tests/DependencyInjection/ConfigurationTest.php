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

namespace PhpInteractor\PhpInteractorBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use PhpInteractor\PhpInteractorBundle\DependencyInjection\Configuration;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    const BUNDLE_CONFIGURATION_CLASS        = 'PhpInteractor\PhpInteractorBundle\DependencyInjection\Configuration';
    const SYMFONY_CONFIGURATION_INTERFACE   = 'Symfony\Component\Config\Definition\ConfigurationInterface';
    const SYMFONY_TREE_BUILDER_CLASS        = 'Symfony\Component\Config\Definition\Builder\TreeBuilder';

    /** @test */
    public function instantiation()
    {
        $this->assertInstanceOf(self::BUNDLE_CONFIGURATION_CLASS, $this->getConfiguration());
        $this->assertInstanceOf(self::SYMFONY_CONFIGURATION_INTERFACE, $this->getConfiguration());
    }

    /** @test */
    public function treeBuilderCreated()
    {
        $this->assertInstanceOf(self::SYMFONY_TREE_BUILDER_CLASS, $this->getConfiguration()->getConfigTreeBuilder());
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }
}
