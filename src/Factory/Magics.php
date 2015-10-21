<?php
/**
 * O2Glob
 *
 * Singleton Global Class Libraries for PHP 5.4 or newer
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
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS ||
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS || COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES || OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT || OTHERWISE, ARISING FROM,
 * OUT OF || IN CONNECTION WITH THE SOFTWARE || THE USE || OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package        O2System
 * @author         Steeven Andrian Salim
 * @copyright      Copyright (c) 2005 - 2014, PT. Lingkar Kreasi (Circle Creative).
 * @license        http://circle-creative.com/products/o2glob/license.html
 * @license        http://opensource.org/licenses/MIT	MIT License
 * @link           http://circle-creative.com
 * @since          Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

namespace O2System\Glob\Factory;

// ------------------------------------------------------------------------

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
     * Reflection of Called Class
     *
     * @access  protected
     * @static
     *
     * @type    object  reflection object class of called class
     */
    protected static $_reflection;

    /**
     * Instance of Called Class
     *
     * @access  protected
     * @static
     *
     * @type     object object of called class
     */
    protected static $_instance;

    /**
     * Storage Property of Called Class
     *
     * @access  protected
     * @static
     *
     * @type    object|array    property storage of called class
     */
    protected static $_storage;

    /**
     * List of Called Class Methods
     *
     * @access  protected
     * @static
     *
     * @type     array
     */
    protected static $_methods;

    /**
     * List of Called Class Properties
     *
     * @access  protected
     * @static
     *
     * @type     array
     */
    protected static $_properties;

    /**
     * Reflection
     * This method is used to reflect the called class
     *
     * @access   protected
     * @final    this method can't be overwritten
     *
     * @uses     \ReflectionClass()
     * @uses     \ReflectionMethod()
     * @uses     \ReflectionProperty()
     *
     * @used_by  all \O2System\Glob Classes
     *
     * @property-write  array   $_methods
     * @property-write  array   $_properties
     */
    final protected static function _reflection()
    {
        static::$_reflection = new \ReflectionClass( get_called_class() );

        $methods = array(
            'public'    => \ReflectionMethod::IS_PUBLIC,
            'protected' => \ReflectionMethod::IS_PROTECTED,
            'private'   => \ReflectionMethod::IS_PRIVATE,
            'static'    => \ReflectionMethod::IS_STATIC
        );

        foreach( $methods as $method => $reflect )
        {
            $reflection = static::$_reflection->getMethods( $reflect );

            if( ! empty( $reflection ) )
            {
                foreach( $reflection as $object )
                {
                    static::$_methods[ $method ][ ] = $object->name;
                }
            }
        }

        $properties = array(
            'public'    => \ReflectionProperty::IS_PUBLIC,
            'protected' => \ReflectionProperty::IS_PROTECTED,
            'private'   => \ReflectionProperty::IS_PRIVATE,
            'static'    => \ReflectionProperty::IS_STATIC
        );

        foreach( $properties as $property => $reflect )
        {
            $reflection = static::$_reflection->getProperties( $reflect );

            if( ! empty( $reflection ) )
            {
                foreach( $reflection as $object )
                {
                    static::$_properties[ $property ][ ] = $object->name;
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Set Magic Method
     * Magic method to write called class properties
     *
     * @access      public
     * @final       this method can't be overwritten
     *
     * @param   string $name  property name
     * @param   mixed  $value property value
     */
    final public function __set( $name, $value )
    {
        if( method_exists( $this, '__setOverride' ) )
        {
            $this->__setOverride( $name, $value );
        }
        else
        {
            $this->{$name} = $value;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Set Storage
     * This method is used for write called class storage properties
     *
     * @access      public
     * @final       this method can't be overwritten
     *
     * @param string $name  storage property name
     * @param mixed  $value storage property value
     */
    final public function __setStorage( $name, $value )
    {
        static::$_storage[ $name ] = $value;
    }

    // ------------------------------------------------------------------------

    /**
     * Get
     * Magic method used as called class property getter
     *
     * @access      public
     * @static      static class method
     *
     * @param   string $property property name
     *
     * @return mixed
     */
    final public function &__get( $property )
    {
        if( isset( static::$_properties[ 'static' ] ) && in_array( $property, static::$_properties[ 'static' ] ) )
        {
            return static::${$property};
        }
        elseif( isset( static::$_properties[ 'public' ] ) && in_array( $property, static::$_properties[ 'public' ] ) )
        {
            return $this->{$property};
        }
        elseif( $property === 'storage' )
        {
            return static::$_storage;
        }
        elseif( isset( static::$_storage[ $property ] ) )
        {
            return static::$_storage[ $property ];
        }
        elseif( method_exists( $this, '__getOverride' ) )
        {
            return $this->__getOverride( $property );
        }

        // Dummy property for avoiding error
        $dummy_property = NULL;

        return $dummy_property;
    }

    // ------------------------------------------------------------------------

    /**
     * Get Property
     * Magic method used as called class property getter with custom returning value conversion
     *
     * @access    private
     * @final     this method can't be overwritten
     *
     * @method    static ::$_instance->__get()
     *
     * @param   $property   string of property name
     * @param   $args       array of parameters
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
            if( is_string( $data ) || empty( $action ) ) return $data;

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

    // ------------------------------------------------------------------------

    /**
     * Call
     * Magic method caller
     *
     * @access  public
     * @final   this method can't be overwritten directly, to overwrite this method create __callOverride($method,
     *          $args = array())
     *
     * @param   $method   string of method name or property name
     * @param   $args     array of parameters
     *
     * @return mixed
     */
    final public function __call( $method, $args = array() )
    {
        if( method_exists( $this, '__callOverride' ) )
        {
            return $this->__callOverride( $method, $args );
        }
        else
        {
            return static::__callStatic( $method, $args );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Call Static
     * Magic method to bewitch class methods or properties call
     *
     * Methods: methods can be called as static method or non-static method
     * Properties: properties can be called as a method with custom returning value conversion
     *
     * @access      public
     * @final       this method can't be overwritten
     *
     * @method      _init() called class init method
     *              _getProperty() called class method
     *
     * @property-read   static::$_instance
     * @property-read   static::$_reflection
     * @property-read   static::$_storage
     *
     * @param   string $method method or property name
     * @param   array  $args   array of parameters
     *
     * @return mixed
     */
    final public static function __callStatic( $method, $args = array() )
    {
        // check if the called class has been initialized and reflected
        if( empty( static::$_instance ) && empty( static::$_reflection ) )
        {
            $class = get_called_class();
            $class::_init();
        }

        // check whether the called method is a to call class storage properties
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

        // check whether is a public non static method
        elseif( isset( static::$_methods[ 'public' ] ) && (
                in_array( $non_static_method = $method, static::$_methods[ 'public' ] ) ||
                in_array( $non_static_method = '_' . $method, static::$_methods[ 'public' ] ) ||
                in_array( $non_static_method = str_replace( '_', '', $method ), static::$_methods[ 'public' ] )
            )
        )
        {
            if( is_array( $args ) )
            {
                return call_user_func_array( array( static::$_instance, $non_static_method ), $args );
            }
            else
            {
                return static::$_instance->{$non_static_method}( $args );
            }

        }

        // check whether is to call class properties
        elseif( isset( static::$_properties[ 'public' ] ) && in_array( $method, static::$_properties[ 'public' ] ) ||
                isset( static::$_properties[ 'static' ] ) && in_array( $method, static::$_properties[ 'static' ] )
        )
        {
            if( empty( $args ) )
            {
                return static::$_instance->__get( $method );
            }

            return static::__getProperty( $method, $args );
        }

        throw new \BadMethodCallException( 'Undefined class method: ' . get_called_class() . '::' . $method );
    }
}