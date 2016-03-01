<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author    Josh Lockhart <info@slimframework.com>
 * @copyright 2011 Josh Lockhart
 * @link      http://www.slimframework.com
 * @license   http://www.slimframework.com/license
 * @version   2.3.0
 */
namespace Fobia\Configuration;

use Fobia\Configuration\ConfigurationHandler;
use Fobia\Configuration\Interfaces\ConfigurationInterface;
use Fobia\Configuration\Interfaces\ConfigurationHandlerInterface;

/**
 * Конфигурация
 * Использует класс обработчика конфигурации для разбора конфигурационных данных, доступ к которым в виде массива.
 *
 * @author  Dmitriy Tyurin
 *
 * @author  John Porter
 * @link    http://www.slimframework.com
 * @license http://www.slimframework.com/license
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
     *
     * @param array                                                         $defaults
     * @param \Fobia\Configuration\Interfaces\ConfigurationHandlerInterface $handler
     */
    public function __construct(array $defaults = array(), ConfigurationHandlerInterface $handler = null)
    {
        if ($handler === null) {
            $handler = new ConfigurationHandler();
        }
        if ($defaults) {
            $this->defaults = $defaults;
        }
        $this->handler = $handler;
        $this->setDefaults();
    }

    /**
     * Установить настройки по умолчанию с помощью обработчика
     *
     * @param array $values
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
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Вызвать метод из обработчика
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    public function callHandlerMethod($method, array $params = array())
    {
        return call_user_func_array(array($this->handler, $method), $params);
    }

    /**
     * Получить значение
     *
     * @param string $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->handler[$key];
    }

    /**
     * Set a value
     * @param  string $key
     * @param  mixed  $value
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
