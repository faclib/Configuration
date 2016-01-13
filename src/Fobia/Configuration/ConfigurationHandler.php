<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author      Josh Lockhart <info@slimframework.com>
 * @copyright   2011 Josh Lockhart
 * @link        http://www.slimframework.com
 * @license     http://www.slimframework.com/license
 * @version     2.3.0
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Fobia\Configuration;

use Fobia\Configuration\Interfaces\ConfigurationHandlerInterface;

/**
 * ConfigurationHandler
 * Класс конфигурации по умолчанию, который обеспечивает конфигурацию значений хранящихся в виде вложенных массивов,
 * которые могут быть доступны и хранятся с использованием '.'
 *
 * @author     John Porter
 * @author     Dmitriy Tyurin
 * @link       http://www.slimframework.com
 * @license    http://www.slimframework.com/license
 */
class ConfigurationHandler implements ConfigurationHandlerInterface
{
    /**
     * Cache of previously parsed keys
     * @var array
     */
    protected $keys = array();

    /**
     * Storage array of values
     * @var array
     */
    protected $values = array();

    /**
     * Expected nested key separator
     * @var string
     */
    protected $separator = '.';

    /**
     * Установить массив опций конфигурации
     * Слияние при условии значения по умолчанию, чтобы гарантировать что все необходимые значения устанавливаются
     * @param array $values
     * @required
     */
    public function setArray(array $values = array())
    {
        $this->values = $this->mergeArrays($this->values, $values);
    }

    /**
     * Получить все значения как вложенный массив
     * @return array
     */
    public function getAllNested()
    {
        return $this->values;
    }

    /**
     * Получить все значения в виде ключа массива
     * @return array
     */
    public function getAllFlat()
    {
        return $this->parseFlattenArray($this->values, '', $this->separator);
    }

    /**
     * Get all flattened array keys
     * @return array
     */
    public function getKeys()
    {
        $flattened = $this->getAllFlat();
        return array_keys($flattened);
    }

    /**
     * Get a value from a nested array based on a separated key
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->getValue($key, $this->values);
    }

    /**
     * Set nested array values based on a separated key
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public function offsetSet($key, $value)
    {
        $this->setValue($key, $value, $this->values);
    }

    /**
     * Check an array has a value based on a separated key
     * @param  string  $key
     * @return boolean
     */
    public function offsetExists($key)
    {
        return (bool)$this->getValue($key, $this->values);
    }

    /**
     * Remove nested array value based on a separated key
     * @param  string  $key
     */
    public function offsetUnset($key)
    {
        $keys = $this->parseKey($key);
        $array = &$this->values;

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                return;
            }

            $array =& $array[$key];
        }

        unset($array[array_shift($keys)]);
    }

    /**
     * Parse a separated key and cache the result
     * @param  string $key
     * @return array
     */
    protected function parseKey($key)
    {
        if (!isset($this->keys[$key])) {
            $this->keys[$key] = explode($this->separator, $key);
        }

        return $this->keys[$key];
    }

    /**
     * Get a value from a nested array based on a separated key
     * @param  string $key
     * @param  array  $array
     * @return mixed
     */
    protected function getValue($key, array $array = array())
    {
        $keys = $this->parseKey($key);

        while (count($keys) > 0 and !is_null($array)) {
            $key = array_shift($keys);
            $array = isset($array[$key]) ? $array[$key] : null;
        }

        return $array;
    }

    /**
     * Set nested array values based on a separated key
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  array   $array
     * @return array
     */
    protected function setValue($key, $value, array &$array = array())
    {
        $keys = $this->parseKey($key, $this->separator);
        $pointer = &$array;

        while (count($keys) > 0) {
            $key = array_shift($keys);
            $pointer[$key] = (isset($pointer[$key]) ? $pointer[$key] : array());
            $pointer = &$pointer[$key];
        }

        $pointer = $value;
        return $array;
    }

    /**
     * Merge arrays with nested keys into the values store
     * Usage: $this->mergeArrays(array $array [, array $...])
     * @return array
     */
    protected function mergeArrays()
    {
        $arrays = func_get_args();
        $merged = array();

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                $merged = $this->setValue($key, $value, $merged);
            }
        }

        return $merged;
    }

    /**
     * Flatten a nested array to a separated key
     *
     * @param  array  $array
     * @param  string $prefix
     * @param  string $separator
     * @return array
     */
    public static function parseFlattenArray(array $array, $prefix = '', $separator = '.')
    {
        $flattened = array();

        $prefix = trim($prefix, $separator);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flattened = array_merge($flattened, self::parseFlattenArray($value, $prefix.$separator.$key, $separator));
            } else {
                $flattened[trim($prefix.$separator.$key, $separator)] = $value;
            }
        }

        return $flattened;
    }

    /**
     * Get an ArrayIterator for the stored items
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
