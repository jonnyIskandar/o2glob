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

use O2System\Glob\Singleton\Basics;

use O2System\Glob\Helpers\Inflector;
use O2System\Glob\Helpers\Convension;

/**
 * System Loader
 *
 * This class contains functions that enable config files to be managed based on CodeIgniter Concept by EllisLab, Inc.
 *
 * @package        O2System
 * @subpackage     system/core
 * @category       Core Class
 * @author         Steeven Andrian Salim
 * @link           http://circle-creative.com/products/o2system/user-guide/core/config.html
 */
abstract class Loader
{
	use Basics;

	/**
	 * Loader Configurations
	 *
	 * @access  protected
	 * @type    array
	 */
	protected $_config = array();

	/**
	 * List of Loaded Libraries
	 *
	 * @static
	 * @access  protected
	 * @type    array
	 */
	protected static $_libraries_classes = array();

	/**
	 * List of Loaded Models
	 *
	 * @static
	 * @access  protected
	 * @type    array
	 */
	protected static $_models_classes = array();

	/**
	 * List of Loaded Controllers
	 *
	 * @static
	 * @access  protected
	 * @type    array
	 */
	protected static $_controllers_classes = array();

	/**
	 * List of Loaded Helper Files
	 *
	 * @access  protected
	 * @type    array
	 */
	protected $_helpers = array();

	/**
	 * Holds all the Prefix Class maps.
	 *
	 * @access  protected
	 * @type    array
	 */
	protected $_prefixes_maps = array();

	/**
	 * Holds all the PSR-4 compliant namespaces maps.
	 * These namespaces should be loaded according to the PSR-4 standard.
	 *
	 * @access  protected
	 * @type    array
	 */
	protected $_psr_namespace_maps = array();

	/**
	 * List of Packages Paths
	 *
	 * @access  protected
	 * @type    array
	 */
	protected $_packages_paths = array();

	// ------------------------------------------------------------------------

	/**
	 * Register
	 *
	 * Register SPL Autoloader
	 *
	 * @param bool $throw
	 * @param bool $prepend
	 *
	 * @access  public
	 */
	public function register( $throw = TRUE, $prepend = TRUE )
	{
		// Register Autoloader
		spl_autoload_register( array( $this, '_spl_autoload' ), $throw, $prepend );
	}

	/**
	 * Adds a namespace search path.  Any class in the given namespace will be
	 * looked for in the given path.
	 *
	 * @access  public
	 *
	 * @param   string  the namespace
	 * @param   string  the path
	 *
	 * @return  void
	 */
	public function add_namespace( $namespaces, $path )
	{
		if ( is_array( $namespaces ) )
		{
			foreach ( $namespaces as $namespace => $path )
			{
				self::add_namespace( $namespace, $path );
			}
		}
		elseif ( is_dir( $path ) )
		{
			$namespaces = trim( $namespaces, '\\' ) . '\\';

			$path = rtrim( $path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;

			$this->_packages_paths[ $namespaces ] = $path;
			$this->_psr_namespace_maps[ $namespaces ] = $path . 'core' . DIRECTORY_SEPARATOR;
			$this->_psr_namespace_maps[ $namespaces . 'Core\\' ] = $path . 'core' . DIRECTORY_SEPARATOR;

			foreach ( $this->_config[ 'class_paths' ] as $class_path )
			{
				if ( is_dir( $path . $class_path ) )
				{
					$this->_psr_namespace_maps[ $namespaces . ucfirst( $class_path ) . '\\' ] = $path . $class_path . DIRECTORY_SEPARATOR;
				}
			}

			// Load Composer
			if ( file_exists( $path . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php' ) )
			{
				require( $path . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php' );
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Adds a namespace search path.  Any class in the given namespace will be
	 * looked for in the given path.
	 *
	 * @access  public
	 *
	 * @param   string  the namespace
	 * @param   string  the path
	 *
	 * @return  void
	 */
	public function add_prefix( $prefixes, $path )
	{
		if ( is_array( $prefixes ) )
		{
			foreach ( $prefixes as $prefix => $path )
			{
				self::add_prefix( $prefix, $path );
			}
		}
		elseif ( is_dir( $path ) )
		{
			$prefixes = trim( $prefixes, '_' ) . '_';

			$this->_packages_paths[ $prefixes ] = $path;
			$this->_prefixes_maps[ $prefixes ] = $path . 'core' . DIRECTORY_SEPARATOR;

			foreach ( $this->_config[ 'class_paths' ] as $class_path )
			{
				if ( is_dir( $path . $class_path ) )
				{
					$this->_prefixes_maps[ Convension::to_classname( $prefixes . '_' . Inflector::singularize( $class_path ) ) . '_' ] = $path . $class_path . DIRECTORY_SEPARATOR;
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Gets a namespace path for read namespace path
	 *
	 * @access  public
	 *
	 * @return  object
	 */
	public function get_namespaces()
	{
		return $this->_psr_namespace_maps;
	}

	// ------------------------------------------------------------------------

	public function add_module_map( $module )
	{
		$this->_modules_maps[ Inflector::pluralize( $module ) ] = $module;
	}

	public function get_modules_maps()
	{
		return $this->_modules_maps;
	}

	/**
	 * Autoload Class APP O2
	 *
	 * @access  public
	 *
	 * @param   string $class the path
	 * @param   string $path  the class
	 *
	 * @return  boolean
	 */
	protected function _spl_autoload( $class )
	{
		if ( ! class_exists( $class, FALSE ) )
		{
			$namespace = Convension::get_namespace( $class );
			$class = Convension::get_classname( $class );

			if ( isset( self::$_instance->_psr_namespace_maps[ $namespace ] ) )
			{
				$path = self::$_instance->_psr_namespace_maps[ $namespace ];
			}

			if ( isset( $path ) )
			{
				$path = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $path );
				$filename = Convension::to_filename( $class );

				$filepaths = array(
					$path . $filename . '.php',
					$path . ucfirst( strtolower( $filename ) ) . '.php',
					$path . strtolower( $filename ) . DIRECTORY_SEPARATOR . $filename . '.php',
					$path . $filename . DIRECTORY_SEPARATOR . $filename . '.php',
				);

				$filepaths = array_unique( $filepaths );

				foreach ( $filepaths as $filepath )
				{
					if ( file_exists( $filepath ) )
					{
						require_once( $filepath );
						break;
					}
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	public function get_package_paths( $sub_path = NULL )
	{
		if ( isset( $sub_path ) )
		{
			foreach ( $this->_packages_paths as $package_path )
			{
				$package_paths[] = $package_path . $sub_path . DIRECTORY_SEPARATOR;
			}

			return $package_paths;
		}
		else
		{
			return $this->_packages_paths;
		}
	}

	/**
	 * Init Class
	 *
	 * Create class object using native __constructor method or using initialize() method.
	 *
	 * @param   string $class  Class Name
	 * @param   array  $params Class Constructor Parameters
	 *
	 * @uses-by Loader::library
	 * @uses-by Loader::driver
	 *
	 * @access  private
	 * @return mixed
	 * @throws \Exception
	 */
	protected function _init_class( $class, array $params = array() )
	{
		if ( class_exists( $class ) )
		{
			if ( method_exists( $class, 'initialize' ) and is_callable( $class . '::initialize' ) )
			{
				return $class::initialize( $params );
			}
			else
			{
				return new $class( $params );
			}
		}
		// or an interface...
		elseif ( interface_exists( $class, FALSE ) )
		{
			// nothing to do here
		}
		// or a trait if you're not on 5.3 anymore...
		elseif ( function_exists( 'trait_exists' ) and trait_exists( $class, FALSE ) )
		{
			// nothing to do here
		}
		else
		{
			throw new \Exception( 'Loader: Cannot find requested class: ' . $class );
		}
	}
}