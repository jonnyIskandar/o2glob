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

namespace O2System\Glob\Interfaces;

// ------------------------------------------------------------------------

use O2System\Glob\Factory\Magics;

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
	 * Driver Class Configuration
	 *
	 * @access protected
	 */
	protected $_config = array();

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
	 * @method  static ::reflection()
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

		$lib_key = strtolower( end( $lib_class ) );
		$lib_key = trim( $lib_key, '\\' );

		$lib_class = reset( $lib_class );

		// Set Config
		$config = $this->library->get_config( $lib_key );

		if ( ! empty( $config ) )
		{
			$this->_config = array_merge( $this->_config, $config );
		}

		// checking custom constructor
		if ( method_exists( $this, '__reconstruct' ) )
		{
			$this->__reconstruct();
		}

		if ( ! isset( static::$_reflection ) )
		{
			// let's the magic begin
			static::_reflection();
		}

		if ( ! isset( static::$_instance ) )
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
		if ( method_exists( $this, $method ) )
		{
			return $this->$method( $args );
		}
		elseif ( method_exists( $this->library, $method ) )
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
	final public function &__getOverride( $property )
	{
		if ( property_exists( $this, $property ) )
		{
			return $this->{$property};
		}
		elseif ( method_exists( $this->library, '__get' ) )
		{
			return $this->library->__get( $property );
		}

		// Dummy property for avoiding error
		$dummy_property = NULL;

		return $dummy_property;
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
		if ( property_exists( get_class( $this->library ), $name ) )
		{
			$this->library->$name = $value;
		}
		else
		{
			$this->{$name} = $value;
		}
	}

	/**
	 * Init
	 * This method is used for initialized called class instance
	 *
	 * @access      public
	 * @static      static class method
	 * @final       this method can't be overwritten
	 *
	 * @method  static ::instance()
	 *
	 * @param   array   isn't really necessary unless when need a parameter
	 *                  to be parsing to __construct() method
	 *
	 * @return object   called class instance
	 */
	final public static function &_init( &$library )
	{
		return static::instance( $library );
	}

	// ------------------------------------------------------------------------

	/**
	 * Instance
	 * Instance class caller
	 *
	 * @access      public
	 * @static      static class method
	 * @final       this method can't be overwritten
	 *
	 * @property-read   static::$_instance
	 *
	 * @param   array   isn't really necessary unless when need a parameter
	 *                  to be parsing to __construct() method
	 *
	 * @return  object  called class instance
	 */
	final public static function &instance( &$library )
	{
		if ( ! isset( static::$_instance ) )
		{
			$class_name = get_called_class();
			static::$_instance = new $class_name( $library );
		}

		return static::$_instance;
	}

	// ------------------------------------------------------------------------

	/**
	 * Clone
	 * Singleton class doesn't allowed class object to be cloned
	 *
	 * @access  protected
	 * @final   this method can't be overwritten
	 */
	final protected function __clone()
	{
	}

	// ------------------------------------------------------------------------

	/**
	 * Wake Up
	 * Singleton class doesn't allowed class handles and object references tobe reinstate
	 *
	 * @access  protected
	 * @final   this method can't be overwritten
	 */
	final protected function __wakeup()
	{
	}

	/**
	 * Throw Error
	 *
	 * @param   string  $error  Error Message
	 * @param   int     $code   Error Code
	 *
	 * @access  public
	 * @return  bool
	 */
	final public function throw_error( $error, $code )
	{
		return $this->library->throw_error( $error, $code );
	}
}