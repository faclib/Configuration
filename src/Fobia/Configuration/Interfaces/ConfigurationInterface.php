<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author    Josh Lockhart <info@slimframework.com>
 * @link      http://www.slimframework.com
 * @license   http://www.slimframework.com/license
 * @version   2.3.0
 */
namespace Fobia\Configuration\Interfaces;

/**
 * Configuration Interface
 *
 * @author  John Porter
 * @link    http://www.slimframework.com
 * @license http://www.slimframework.com/license
 */
interface ConfigurationInterface extends \ArrayAccess
{
    public function setArray(array $values);

    /**
     * As we can't extend multiple interfaces, we need to mimic \IteratorAggregate
     * @return \ArrayIterator
     */
    //public function getIterator();
}
