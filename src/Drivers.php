<?php
/**
 * O2System
 *
 * An open source application development framework for PHP 5.4 or newer
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
 * @license        http://circle-creative.com/products/o2system/license.html
 * @license        http://opensource.org/licenses/MIT	MIT License
 * @link           http://circle-creative.com
 * @since          Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

namespace O2System\O2Glob;

// ------------------------------------------------------------------------

/**
 * Drivers
 *
 * This class enables you to create "Driver" libraries that add runtime ability
 * to extend the capabilities of a class via additional driver objects
 *
 * @package        O2System
 * @subpackage     core\glob
 * @category       Factory Class
 * @author         Circle Creative Dev Team
 * @link           http://o2system.center/wiki/#GlobDrivers
 */
abstract class Drivers
{
    /**
     * Using Glob Magics Trait Class
     *
     * @uses \O2System\Glob\Magics
     */
    use Magics;

    /**
     * Called Driver Name
     *
     * @access  protected
     *
     * @type    string   driver class name
     */
    protected $_driver_name;

    /**
     * Instance of the library class
     *
     * @type object
     */
    public $library;

    /**
     * Driver Class Constructor
     *
     * @access  public
     *
     * @uses    \O2System\Core\Loader::driver() called library instance
     *
     * @property-write  $_driver_name
     * @property-write  $_library
     * @property-write  static::$_instance
     *
     * @property-read   static::$_reflection
     * @property-read   static::$_instance
     *
     * @method  static::reflection()
     * @method  $this->__reconstruct()
     *
     * @param array     $config driver class configurations
     */
    public function __construct( &$library )
    {
        // Set Driver Library Object
        $this->library =& $library;

        // Driver Class
        $this->_driver_name = get_called_class();

        // Instance parent library
        $lib_class = explode( '\Drivers', $this->_driver_name );
        $lib_class = reset( $lib_class );

        // checking custom constructor
        if( method_exists( $this, '__reconstruct' ) )
        {
            $this->__reconstruct();
        }

        if( ! isset( static::$_reflection ) )
        {
            // let's the magic begin
            static::_reflection();
        }

        if( ! isset( static::$_instance ) )
        {
            static::$_instance =& $this;
        }
    }
    // --------------------------------------------------------------------

    /**
     * Call Override
     * Handles access to the driver or library's methods
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
    public function __callOverride( $method, $args = array() )
    {
        if( method_exists( $this, $method ) )
        {
            return $this->$method( $args );
        }
        elseif( method_exists( $this->library, $method ) )
        {
            return $this->library->$method( $args );
        }
        else
        {
            return static::__callStatic( $method, $args );
        }
    }
    // --------------------------------------------------------------------

    /**
     * Get Override
     * Handles reading of the parent driver or library's properties
     *
     * @access      public
     * @static      static class method
     * @final       this method can't be overwritten
     *
     * @param   string $property property name
     *
     * @return mixed
     */
    final public function __getOverride( $property )
    {
        if( property_exists( $this, $property ) )
        {
            return $this->{$property};
        }
        elseif( method_exists( $this->library, '__get' ) )
        {
            return $this->library->__get( $property );
        }
    }
    // --------------------------------------------------------------------

    /**
     * Set Magic Method Override
     * Overriding Set Magic method to write called driver or library's class properties
     *
     * @access      public
     * @final       this method can't be overwritten
     *
     * @property-write  $_library driver library class properties
     *
     * @param   string  $name     property name
     * @param   mixed   $value    property value
     */
    final public function __setOverride( $name, $value )
    {
        if( property_exists( get_class( $this->library ), $name ) )
        {
            $this->library->$name = $value;
        }
        else
        {
            $this->{$name} = $value;
        }
    }
}