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

namespace PhpInteractor\PhpInteractorBundle;

use PhpInteractor\PhpInteractorBundle\DependencyInjection\Compiler\InteractorDependencyCompilerPass;
use PhpInteractor\PhpInteractorBundle\DependencyInjection\Compiler\InteractorMapCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PhpInteractorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new InteractorMapCompilerPass(), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new InteractorDependencyCompilerPass(), PassConfig::TYPE_AFTER_REMOVING);
    }
}
