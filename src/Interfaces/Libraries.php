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
 * Libraries
 *
 * This class enables you to create "Driver" libraries that add runtime ability
 * to extend the capabilities of a class via additional driver objects
 *
 * @package        O2System
 * @subpackage     core\glob
 * @category       Factory Class
 * @author         Circle Creative Dev Team
 * @link           http://o2system.center/wiki/#GlobLibraries
 */
abstract class Libraries
{
	/**
	 * Using Glob Magics Trait Class
	 *
	 * @uses \O2System\Glob\Magics
	 */
	use Magics;

	/**
	 * Class Configuration
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
	protected $_library_name;

	/**
	 * List of library valid drivers
	 *
	 * @access  protected
	 *
	 * @type    array   driver classes list
	 */
	protected $_valid_drivers = array();

	/**
	 * List of library errors
	 *
	 * @access  protected
	 * @type    array
	 */
	protected $_errors = array();

	/**
	 * Debug Mode Flag
	 *
	 * @access  protected
	 * @type    bool
	 */
	protected $_debug_mode = FALSE;

	/**
	 * Libraries Class Constructor
	 *
	 * @access  public
	 *
	 * @uses    \O2System\Core\Loader::add_namespace()  registering library namespace to O2System Loader
	 *
	 * @property-write  $_library_name
	 * @property-write  static::$_instance
	 *
	 * @property-read   static::$_reflection
	 * @property-read   static::$_instance
	 *
	 * @method  static ::reflection()
	 */
	public function __construct( $config = array() )
	{
		// Set Config
		if ( ! empty( $config ) )
		{
			$this->_config = $config;

			if ( isset( $this->_config[ 'debug_mode' ] ) )
			{
				$this->_debug_mode = $this->_config[ 'debug_mode' ];
			}
		}

		// Library Class
		$this->_library_name = get_called_class();

		// let the magic begin
		static::_reflection();

		foreach ( glob( $this->_get_drivers_path() . '*.php' ) as $filepath )
		{
			$this->_valid_drivers[ strtolower( pathinfo( $filepath, PATHINFO_FILENAME ) ) ] = $filepath;
		}

		// create instance
		static::$_instance =& $this;
	}

	// ------------------------------------------------------------------------

	final protected function _get_drivers_path()
	{
		$class_realpath = static::$_reflection->getFileName();

		$driver_paths = array(
			dirname( $class_realpath ) . DIRECTORY_SEPARATOR . 'Drivers' . DIRECTORY_SEPARATOR,
			dirname( $class_realpath ) . DIRECTORY_SEPARATOR . strtolower( pathinfo( $class_realpath, PATHINFO_FILENAME ) ) . DIRECTORY_SEPARATOR,
		);

		foreach ( $driver_paths as $driver_path )
		{
			if ( is_dir( $driver_path ) )
			{
				return $driver_path;
				break;
			}
		}
	}

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
	public function &__getOverride( $property )
	{
		if ( isset( static::$_properties[ 'static' ] ) && in_array( $property, static::$_properties[ 'static' ] ) )
		{
			return static::${$property};
		}
		elseif ( isset( static::$_properties[ 'public' ] ) && in_array( $property, static::$_properties[ 'public' ] ) )
		{
			return $this->{$property};
		}
		elseif ( $property === 'registry' )
		{
			return static::$_registry;
		}
		elseif ( isset( static::$_registry[ $property ] ) )
		{
			return static::$_registry[ $property ];
		}
		elseif ( array_key_exists( $property, $this->_valid_drivers ) )
		{
			// Try to load the driver
			return $this->_load_driver( $property );
		}

		// make a dummy property for avoiding error
		$dummy_property = NULL;

		return $dummy_property;
	}

	// ------------------------------------------------------------------------

	/**
	 * Load driver
	 *
	 * Separate load_driver call to support explicit driver load by library or user
	 *
	 * @param   string $driver driver class name (lowercase)
	 *
	 * @return    object    Driver class
	 */
	protected function &_load_driver( $driver )
	{
		if ( empty( $this->{$driver} ) )
		{
			if ( file_exists( $filepath = $this->_valid_drivers[ $driver ] ) )
			{
				require_once( $filepath );

				$class_name = get_called_class() . '\\Drivers\\' . ucfirst( $driver );

				if ( class_exists( $class_name, FALSE ) )
				{
					$this->{$driver} =& $class_name::instance( $this );
				}
			}
		}

		return $this->{$driver};
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
	final public static function &initialize()
	{
		return static::instance( func_get_args() );
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
	final public static function &instance()
	{
		$params = func_get_args();

		if ( ! isset( static::$_instance ) )
		{
			$class_name = get_called_class();
			static::$_instance = new $class_name( $params );
		}

		return static::$_instance;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Library Config Item
	 *
	 * @access  public
	 * @final   this method can't be overwritten
	 *
	 * @param string|null $item Config item index name
	 *
	 * @return array|null
	 */
	final public function get_config( $item = NULL )
	{
		if ( isset( $this->_config[ $item ] ) )
		{
			return $this->_config[ $item ];
		}
		else
		{
			return ! empty( $this->_config ) ? $this->_config : NULL;
		}
	}


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
	 * @param   string $error Error Message
	 * @param   int    $code  Error Code
	 *
	 * @access  public
	 * @return  bool
	 */
	final public function throw_error( $error, $code = NULL )
	{
		if ( $this->_debug_mode === TRUE )
		{
			if ( isset( $code ) )
			{
				$this->_errors[ $code ] = $error;
			}
			else
			{
				$this->_errors[] = $error;
			}

			return FALSE;
		}
		else
		{
			$class_name = get_called_class() . '\\Exception';

			if ( class_exists( $class_name, FALSE ) )
			{
				throw new $class_name( $error, $code );
			}
			else
			{
				throw new \BadFunctionCallException( $error, $code );
			}
		}
	}

	/**
	 * Get Error
	 *
	 * @access  public
	 * @return  array
	 */
	final public function get_errors()
	{
		if ( ! empty( $this->_errors ) )
		{
			return $this->_errors;
		}
	}
}