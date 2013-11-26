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

namespace PhpInteractor\PhpInteractorBundle\Helper;

class ReflectionHelper
{
    public static function invokeMethod($className, $methodName, array $arguments)
    {
        $instance   = self::getClassInstance($className, $arguments);
        $method     = self::getReflectionMethod($className, $methodName);

        return $method->invoke($instance, $methodName);
    }

    private static function getClassInstance($className, $arguments)
    {
        return new $className($arguments);
    }

    private static function getReflectionMethod($className, $methodName)
    {
        return self::getReflection($className)->getMethod($methodName);
    }

    private static function getReflection($className)
    {
        return new \ReflectionClass($className);
    }
}
