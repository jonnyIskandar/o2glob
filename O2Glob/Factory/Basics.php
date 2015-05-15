<?php
/**
 * O2Glob
 *
 * An mini open source application development framework for PHP 5.3 or newer
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014, PT. Lingkar Kreasi (Circle Creative).
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package        O2Glob
 * @author         Steeven Andrian Salim
 * @copyright      Copyright (c) 2015, PT. Lingkar Kreasi (Circle Creative).
 *
 * @license        http://circle-creative.com/products/o2glob/license.html
 * @license        http://opensource.org/licenses/MIT	MIT License
 *
 * @link           http://circle-creative.com/products/o2glob.html
 *                 http://o2system.center/standalone/o2glob.html
 *
 * @filesource
 */

// ------------------------------------------------------------------------

namespace O2Glob\Factory;
defined( 'GLOB_PATH' ) OR exit( 'No direct script access allowed' );

/**
 * Basics Trait Class
 *
 * Use this class to build common classes
 * The classes will automatically support methods call as static or non static
 * The classes will also has additional features when calling the classes property
 *
 * @package        O2Glob
 * @category       Factory Class
 * @author         Steeven Andrian Salim
 * @author         Circle Creative Developer Team
 * @copyright      Copyright (c) 2015, PT. Lingkar Kreasi (Circle Creative). 
 * @link           http://o2system.center/standalone/o2glob/user-guide/basics.html
 */
trait Basics
{
    /**
     * Add magical methods functionality
     */
    use Magics;

    /**
     * Class Name
     *
     * @access  protected
     * @var     string name of called class
     */
    protected $_class_name;

    final public function __construct( array $params = array() )
    {
        $this->_class_name = get_called_class();

        if( method_exists( $this, '__initialize' ) )
        {
            $this->__initialize($params);
        }

        if( ! isset( static::$_reflection ) )
        {
            static::$_reflection = new \ReflectionClass( $this->_class_name );

            $methods = array(
                'public' => \ReflectionMethod::IS_PUBLIC,
                'protected' => \ReflectionMethod::IS_PROTECTED,
                'private' => \ReflectionMethod::IS_PRIVATE,
                'static' => \ReflectionMethod::IS_STATIC
            );

            foreach($methods as $method => $reflect)
            {
                $reflection = static::$_reflection->getMethods($reflect);

                if(! empty($reflection))
                {
                    foreach($reflection as $object)
                    {
                        static::$_methods[$method][] = $object->name;
                    }
                }
            }

            $properties = array(
                'public' => \ReflectionProperty::IS_PUBLIC,
                'protected' => \ReflectionProperty::IS_PROTECTED,
                'private' => \ReflectionProperty::IS_PRIVATE,
                'static' => \ReflectionProperty::IS_STATIC
            );

            foreach($properties as $property => $reflect)
            {
                $reflection = static::$_reflection->getProperties($reflect);

                if(! empty($reflection))
                {
                    foreach($reflection as $object)
                    {
                        static::$_properties[$property][] = $object->name;
                    }
                }
            }
        }

        if(! isset(static::$_instance))
        {
            static::$_instance =& $this;
        }
    }

    /**
     * Class Init
     * Method to construct a static class
     *
     * @access  public
     *
     * @param   array       is'nt really necessary unless when need a parameter
     *                      to be parsing to __initialize() method
     *
     * @replace false       final method cannot be replaced
     *
     * @return void
     */
    final public static function &_init( $params = array() )
    {
        return static::instance( $params );
    }

    /**
     * Get Class Instance
     *
     * @access  public
     *
     * @param   array       is'nt really necessary unless when need a parameter
     *                      to be parsing to __initialize() method
     *
     * @replace false       final method cannot be replaced
     *
     * @return  object of called class
     */
    final public static function &instance( $params = array() )
    {
        if( ! isset( static::$_instance ) )
        {
            $class_name = get_called_class();
            static::$_instance = new $class_name( $params );
        }

        return static::$_instance;
    }

    /**
     * Disabled clone
     *
     * @access  protected
     *
     * @replace false  final method cannot be replaced
     *
     * @return void
     */
    final private function __clone()
    {
    }

    /**
     * Disabled reinstate handles and object references
     *
     * @access  protected
     *
     * @replace false       final method cannot be replaced
     *
     * @return void
     */
    final private function __wakeup()
    {
    }
}
/* End of file Basics.php */
/* Location: ./O2Glob/Factory/Basics.php */