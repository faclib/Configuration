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

use Fobia\Configuration\ConfigurationHandler;
use Fobia\Configuration\Interfaces\ConfigurationInterface;
use Fobia\Configuration\Interfaces\ConfigurationHandlerInterface;

/**
 * Конфигурация
 * Использует класс обработчика конфигурации для разбора конфигурационных данных, доступ к которым в виде массива.
 *
 * @author     John Porter
 * @link       http://www.slimframework.com
 * @license    http://www.slimframework.com/license
 */
class Configuration implements ConfigurationInterface, \IteratorAggregate
{
    /**
     * Обработчик значений конфигурации
     * @var mixed
     */
    protected $handler;
    /**
     * Массив для хранения значений
     * @var array
     */
    protected $values = array();

    /**
     * Значения по умолчанию
     * @var array
     */
    protected $defaults = array();

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $defaults = null;
        $args = (func_num_args() > 0) ? func_get_args() : array();

        if (!$args || !($args[0] instanceof ConfigurationHandlerInterface)) {
            $this->handler = new ConfigurationHandler();
        } else {
            $this->handler = $args[0];
        }

        if (is_array($args[0])) {
            $defaults = ConfigurationHandler::parseFlattenArray($args[0]);
        }
        if (is_array($defaults)) {
            $this->defaults = $defaults;
        }
        $this->setDefaults();

        if (isset($args[1]) && is_array($args[1])) {
            $this->handler->setArray($args[1]);
        }
    }

    /**
     * Установить настройки по умолчанию с помощью обработчика
     */
    public function setArray(array $values)
    {
        $this->handler->setArray($values);
    }

    /**
     * Установить настройки по умолчанию с помощью обработчика
     */
    public function setDefaults()
    {
        $this->handler->setArray($this->defaults);
    }

    /**
     * Получить параметры по умолчанию
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Вызвать метод из обработчика
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public function callHandlerMethod($method, array $params = array())
    {
        return call_user_func_array(array($this->handler, $method), $params);
    }

    /**
     * Получить значение
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->handler[$key];
    }

    /**
     * Set a value
     * @param  string $key
     * @param  mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->handler[$key] = $value;
    }

    /**
     * Check a value exists
     * @param  string $key
     * @return boolean
     */
    public function offsetExists($key)
    {
        return isset($this->handler[$key]);
    }

    /**
     * Remove a value
     * @param  string $key
     */
    public function offsetUnset($key)
    {
        unset($this->handler[$key]);
    }

    /**
     * Получить ArrayIterator для хранимых данных
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
