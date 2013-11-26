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

namespace PhpInteractor\PhpInteractorBundle\Tests\Helper\Request;

use PhpInteractor\Helper\AbstractInteractorRequest;

class TestInteractorRequest extends AbstractInteractorRequest
{
    /** @var string */
    public $testVar1;

    /** @var string */
    public $testVar2;
}
