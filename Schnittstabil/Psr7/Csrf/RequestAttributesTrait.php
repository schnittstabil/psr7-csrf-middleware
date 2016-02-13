<?php

namespace Schnittstabil\Psr7\Csrf;

/**
 * Attributes used by Schnittstabil\Psr7\Csrf.
 *
 * See <a href="http://www.php-fig.org/psr/psr-7/">ServerRequestInterface::getAttributes()</a> for details.
 */
trait RequestAttributesTrait
{
    /**
     * Attribute name used for determining request validity.
     *
     * @var string
     */
    public static $isValidAttribute = 'Schnittstabil\\Psr7\\Csrf\\isValid';

    /**
     * Attribute name used for storing constraint violations.
     *
     * @var string
     */
    public static $violationsAttribute = 'Schnittstabil\\Psr7\\Csrf\\violations';
}
