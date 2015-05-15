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
 * Magics Trait Class
 *
 * This class is works to utilize Basics and Statics Trait Class
 * to has additional magics functionality methods
 *
 * @package        O2Glob
 * @category       Factory Class
 * @author         Steeven Andrian Salim
 * @author         Circle Creative Developer Team
 * @copyright      Copyright (c) 2015, PT. Lingkar Kreasi (Circle Creative).
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
     * Class Storage Object
     *
     * @access  protected
     * @var     object of called class
     */
    protected static $_storage;

    /**
     * Class Methods Lists
     *
     * @access  protected
     * @var     array
     */
    protected static $_methods;

    /**
     * Class Properties Lists
     *
     * @access  protected
     * @var     array
     */
    protected static $_properties;

    /**
     * Magic method to set class properties
     * Static property or non static property
     *
     * @access  public
     *
     * @param   string $name  property name
     * @param   mixed  $value property value
     *
     * @return void
     */
    public function __set( $name, $value )
    {
        $this->{$name} = $value;
    }

    /**
     * Magic method to get class properties
     * Static property or non static property
     *
     * @access  public
     *
     * @param   $property   string of property name
     *
     * @return mixed
     */
    public function __get( $property )
    {
        if( isset( static::$_properties[ 'static' ] ) AND in_array( $property, static::$_properties[ 'static' ] ) )
        {
            return static::${$property};
        }
        elseif( isset( static::$_properties[ 'public' ] ) AND in_array( $property, static::$_properties[ 'public' ] ) )
        {
            return $this->{$property};
        }
    }

    /**
     * Property getter method and validation
     *
     * @access  private
     *
     * @param   $property   string of property name
     * @param   $args       array of parameters
     *
     * @replace false     final method cannot be replaced
     *
     * @return mixed
     */
    final static private function __getProperty( $property, $args = array() )
    {
        if( empty( $args ) )
        {
            return static::$_instance->__get( $property );
        }
        else
        {
            $entry = static::$_instance->__get( $property );
            @list( $index, $action, $params ) = $args;

            // if the entry is string nothing else to be proceed
            if( is_string( $entry ) ) return $entry;

            if( is_array( $entry ) )
            {
                $data = $entry;

                if( isset( $entry[ $index ] ) )
                {
                    $data = $entry[ $index ];
                }
                else
                {
                    @list( $action, $params ) = $args;
                }
            }
            elseif( is_object( $entry ) )
            {
                $data = $entry;

                if( isset( $entry->{$index} ) )
                {
                    $data = $entry->{$index};
                }
                else
                {
                    @list( $action, $params ) = $args;
                }
            }

            // if the data is string or there is no action nothing to be proceed
            if( is_string( $data ) OR empty( $action ) ) return $data;

            if( in_array( $action, array( 'array', 'object', 'keys' ) ) )
            {
                switch( $action )
                {
                    default:
                    case 'object';
                        if( is_array( $data ) )
                        {
                            return (object)$data;
                        }

                        return $data;
                        break;
                    case 'array';
                        if( is_object( $data ) )
                        {
                            return get_object_vars( $data );
                        }

                        return $data;
                        break;
                    case 'keys';
                        if( is_object( $data ) )
                        {
                            $data = get_object_vars( $data );
                        }

                        return array_keys( $data );
                        break;
                }
            }
            elseif( in_array( strtolower( $action ), array( 'json', 'serialize' ) ) )
            {
                switch( $action )
                {
                    default:
                    case 'json':
                        return json_encode( $data );
                        break;
                    case 'serialize';
                        return serialize( $data );
                        break;
                }
            }
            elseif( isset( $data->{$action} ) )
            {
                return $data->{$action};
            }
            elseif( isset( $data[ $action ] ) )
            {
                return $data[ $action ];
            }
        }
    }


    /**
     * Validate a non static method calls
     *
     * @access  public
     *
     * @param   $method   string of method name or property name
     * @param   $args     array of parameters
     *
     * @replace false     final method cannot be replaced
     *
     * @return mixed
     */
    public function __call( $method, $args = array() )
    {
        return static::__callStatic( $method, $args );
    }

    /**
     * Validate a static method calls
     *
     * @access  public
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
        // return null for avoiding reference loop
        if( empty( static::$_instance ) AND empty( static::$_reflection ) )
        {
            $class = get_called_class();
            $class::_init();
        }

        // check if is a storage call
        if( isset( static::$_storage ) )
        {
            if( $method === 'storage' )
            {
                return static::$_storage->getArrayCopy();
            }
            elseif( isset( static::$_storage[ $method ] ) )
            {
                if( empty( $args ) )
                {
                    return static::$_storage->__get( $method );
                }

                return static::$_storage->__call( $method, $args );
            }
            elseif( method_exists( static::$_storage, $method ) )
            {
                return static::$_storage->__call( $method, $args );
            }
        }

        // check if is a non static call method
        if( isset( static::$_methods[ 'public' ] ) AND in_array( $non_static_method = str_replace( '_', '', $method ), static::$_methods[ 'public' ] ) )
        {
            return call_user_func_array( array( static::$_instance, $non_static_method ), $args );
        }

        // check if is a property call method
        if( isset( static::$_properties[ 'public' ] ) AND in_array( $method, static::$_properties[ 'public' ] ) )
        {
            if( empty( $args ) )
            {
                return static::$_instance->__get( $method );
            }

            return static::__getProperty( $method, $args );
        }

        return NULL;
    }
}

/* End of file Magics.php */
/* Location: ./O2Glob/Factory/Magics.php */
