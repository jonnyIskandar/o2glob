<?php
/**
 * O2System
 *
 * An open source application development framework for PHP 5.2.4 or newer
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
 * Magics Trait Class
 *
 * This class is works to utilize Basics and Statics Trait Class
 * to has additional magics functionality methods
 *
 * @package        O2Glob
 * @category       Factory Class
 * @author         Steeven Andrian Salim
 * @link           http://o2system.center/standalone/o2glob/user-guide/magics.html
 */
trait Magics
{
    /**
     * Class Reflection Object
     *
     * @access  protected
     * @var     object of reflected called class
     */
    protected static $_reflection;

    /**
     * Class Instance Object
     *
     * @access  protected
     * @var     object of called class
     */
    protected static $_instance;

    /**
     * Magic method to set class properties
     * Static property or non static property
     *
     * @access public
     *
     * @param   $property   string of property name
     * @param   $args       array of parameters
     *
     * @replace false       final method cannot be replaced
     *
     * @return mixed
     */
    final public function __set( $name, $value )
    {
        if(! isset($this->{$name}) AND ! isset(static::${$name}))
        {
            $this->{$name} = $value;
        }
        elseif(isset($this->{$name}) AND ! isset(static::${$name}))
        {
            if(is_array($this->{$name}))
            {
                if(is_string($value))
                {
                    array_push($this->{$name}, $value);
                }
                elseif(is_array($value))
                {
                    $this->{$name} = array_merge($this->{$name}, $value);
                }
            }
            else
            {
                $this->{$name} = $value;
            }
        }
    }

    /**
     * Magic method to get class properties
     * Static property or non static property
     *
     * @access public
     *
     * @param   $property   string of property name
     * @param   $args       array of parameters
     *
     * @replace false     final method cannot be replaced
     *
     * @return mixed
     */
    final public function __get( $name )
    {
        if ( isset( $this->{$name} ) )
        {
            return $this->{$name};
        }
        elseif ( isset( static::${$name} ) )
        {
            return static::${$name};
        }

        if ( isset( static::$_access_protected_properties ) AND static::$_access_protected_properties === TRUE )
        {
            $name = '_' . $name;

            if ( isset( $this->{$name} ) )
            {
                return $this->{$name};
            }
            elseif ( isset( static::${$name} ) )
            {
                return static::${$name};
            }
        }

        return NULL;
    }

    /**
     * Property getter method and validation
     *
     * @access private
     *
     * @param   $property   string of property name
     * @param   $args       array of parameters
     *
     * @replace false     final method cannot be replaced
     *
     * @return mixed
     */
    final private static function __get_property( $property, $args = array() )
    {
        if ( empty( $args ) )
        {
            return static::$_instance->__get( $property );
        }
        else
        {
            $entry = static::$_instance->__get( $property );
            list( $index, $action ) = $args;
            $params = isset( $args[ 2 ] ) ? $args[ 2 ] : array();

            // if the value is string nothing to be proceed
            if ( is_string( $entry ) OR is_null( $entry ) ) return $entry;

            if ( isset( $entry[ $index ] ) )
            {
                $data = $entry[ $index ];
            }
            elseif ( isset( $entry->{$index} ) )
            {
                $data = $entry->{$index};
            }

            // if the index value is string nothing to be proceed
            if ( is_string( $data ) ) return $data;

            if ( in_array( $action, array( 'array', 'object', 'keys' ) ) )
            {
                switch ( $action )
                {
                    default:
                    case 'object';
                        if ( is_array( $data ) )
                        {
                            return (object) $data;
                        }

                        return $data;
                        break;
                    case 'array';
                        if ( is_object( $data ) )
                        {
                            return get_object_vars( $data );
                        }

                        return $data;
                        break;
                    case 'keys';
                        if ( is_object( $data ) )
                        {
                            $data = get_object_vars( $data );
                        }

                        return array_keys( $data );
                        break;
                }
            }
            elseif ( in_array( strtolower( $action ), array( 'json', 'serialize' ) ) )
            {
                switch ( $action )
                {
                    default:
                    case 'json':
                        return json_encode( $data );
                        break;
                    case 'serialize';
                        if ( method_exists( $data, 'serialize' ) )
                        {
                            return $data->serialize();
                        }
                        else
                        {
                            return serialize( $data );
                        }
                        break;
                }
            }
            elseif ( method_exists( $data, $action ) )
            {
                return call_user_func_array( $data, $params );
            }
            elseif ( $action !== FALSE )
            {
                if ( isset( $data->{$action} ) )
                {
                    return $data->{$action};
                }
                elseif ( isset( $data[ $action ] ) )
                {
                    return $data[ $action ];
                }
            }
            elseif ( $action === FALSE )
            {
                return $data;
            }
        }
    }

    /**
     * Validate a non static method calls
     *
     * @access public
     *
     * @param   $method   string of method name or property name
     * @param   $args     array of parameters
     *
     * @replace false     final method cannot be replaced
     *
     * @return mixed
     */
    final public function __call( $method, $args = array() )
    {
        return static::__callStatic( $method, $args );
    }

    /**
     * Validate a static method calls
     *
     * @access public
     *
     * @param   $method   string of method name or property name
     * @param   $args     array of parameters
     *
     * @replace false     final method cannot be replaced
     *
     * @return mixed
     */
    final public static function __callStatic( $method, $args = array() )
    {
        // Keep maintain class visibility methods
        static $_methods;

        if ( ! isset( $_methods ) )
        {
            foreach ( static::$_reflection->getMethods( \ReflectionMethod::IS_PUBLIC ) as $reflect_method )
            {
                $_methods[ $reflect_method->name ] =& $reflect_method;
            }
        }

        // Let's make non static method to be able to be called statically
        if ( isset( $_methods[ $method ] ) )
        {
            return call_user_func_array( array( static::$_instance, $method ), $args );
        }
        elseif ( isset( $_methods[ substr( $method, 1 ) ] ) )
        {
            return call_user_func_array( array( static::$_instance, substr( $method, 1 ) ), $args );
        }

        // Keep maintain class visibility property
        if ( isset( static::$_access_protected_properties ) AND static::$_access_protected_properties === TRUE )
        {
            $_public_properties    = static::$_reflection->getProperties( \ReflectionProperty::IS_PUBLIC );
            $_protected_properties = static::$_reflection->getProperties( \ReflectionProperty::IS_PROTECTED );

            if ( is_array( $_public_properties ) )
            {
                $properties = array_merge( $_public_properties, $_protected_properties );
            }
        }
        else
        {
            $properties = static::$_reflection->getProperties( \ReflectionProperty::IS_PUBLIC );
        }

        if ( ! empty( $properties ) )
        {
            foreach ( $properties as $property )
            {
                $_properties[ $property->name ] = $property;
            }

            if ( isset( $_properties[ $method ] ) )
            {
                return static::__get_property( $method, $args );
            }
        }
    }
}
/* End of file Magics.php */
/* Location: ./O2Glob/Factory/Magics.php */