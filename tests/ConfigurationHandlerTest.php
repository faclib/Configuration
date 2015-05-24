<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author      Josh Lockhart <info@joshlockhart.com>
 * @copyright   2011 Josh Lockhart
 * @link        http://www.slimframework.com
 * @license     http://www.slimframework.com/license
 * @version     2.3.5
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

use Fobia\Configuration\ConfigurationHandler;

/**
 * Configuration Test
 */
class ConfigurationHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetArray()
    {
        $con = new ConfigurationHandler;
        $con->setArray(array(
            'foo' => 'bar'
        ));

        $this->assertEquals($con['foo'], 'bar');
    }

    public function testSetAndGet()
    {
        $con = new ConfigurationHandler;
        $con['foo'] = 'bar';

        $this->assertEquals($con['foo'], 'bar');
    }

    public function testKeys()
    {
        $con = new ConfigurationHandler;
        $con->setArray(array(
            'foo' => 'bar'
        ));
        $keys = $con->getKeys();

        $this->assertEquals($keys[0], 'foo');
    }

    public function  testWithNamespacedKey()
    {
        $con = new ConfigurationHandler;
        $con['my.namespaced.keyname'] = 'My Value';

        $this->arrayHasKey($con, 'my');
        $this->arrayHasKey($con['my'], 'namespaced');
        $this->arrayHasKey($con['my.namespaced'], 'keyname');
        $this->assertEquals('My Value', $con['my.namespaced.keyname']);
    }

    public function testWithString()
    {
        $con = new ConfigurationHandler;
        $con['keyname'] = 'My Value';

        $this->assertEquals('My Value', $con['keyname']);
    }

    public function testIsset()
    {
        $con = new ConfigurationHandler;
        $con['param'] = 'value';

        $this->assertTrue(isset($con['param']));
        $this->assertFalse(isset($con['non_existent']));
    }

    public function testUnset()
    {
        $con = new ConfigurationHandler;
        $con['param'] = 'value';

        unset($con['param'], $con['service']);
        $this->assertFalse(isset($con['param']));
        $this->assertFalse(isset($con['service']));
    }

    public function testFlattenArray()
    {
        $con = new ConfigurationHandler;
        $a0 = array(
            'k2.1' => array(
                'k2.3.1' => 'val231'
            ),
        );
        $p = $con->parseFlattenArray($a0);
        $this->assertArrayHasKey('k2.1.k2.3.1', $p);
    }

    public function testFlattenArrayWithPrefix()
    {
        $con = new ConfigurationHandler;
        $a0 = array('k2.1' => 'val21');

        $p = $con->parseFlattenArray($a0, 'pref');
        $this->assertArrayHasKey('pref.k2.1', $p);
    }

    public function testFlattenArrayWithSeparator()
    {
        $con = new ConfigurationHandler;
        $a0 = array(
            'foo' => array(
                'bar' => array(
                    'baz' => 'val'
                ),
            ),
            'bar:1' => array(
                'bar:2' => 'val2'
            )
        );

        $p = $con->parseFlattenArray($a0, '', ':');
        $this->assertArrayHasKey('foo:bar:baz', $p);
        $this->assertEquals('val', $p['foo:bar:baz']);

        $this->assertArrayHasKey('bar:1:bar:2', $p);
        $this->assertEquals('val2', $p['bar:1:bar:2']);
    }
}
