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

namespace O2System\Glob;

// ------------------------------------------------------------------------

use O2System\Glob\Singleton\Basics;

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
class Loader
{
    use Basics
    {
        Basics::__construct as private __globConstruct;
    }

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
     * List of Loaded Helper Files
     *
     * @access  protected
     * @type    array
     */
    protected $_helpers = array();

    /**
     * List of Loaded Variables
     *
     * @access  protected
     * @type    array
     */
    protected $_vars = array();

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

    protected $_modules_maps = array();

    /**
     * List of class name mappings
     *
     * @access  protected
     * @type    array
     */
    protected $_object_maps = array();

    /**
     * List of Packages Paths
     *
     * @access  protected
     * @type    array
     */
    protected $_packages_paths = array();

    // ------------------------------------------------------------------------

    /**
     * Class Constructor
     *
     * @access  protected
     */
    public function __construct()
    {
        // call glob constructor
        $this->__globConstruct();
    }

    public function register( $throw = TRUE, $prepend = TRUE )
    {
        // Register Autoloader
        spl_autoload_register( array( $this, 'spl_autoload' ), $throw, $prepend );
    }

    public function set_config( $config )
    {
        if( is_array( $config ) )
        {
            $this->_config = $config;
        }
        else
        {
            if( file_exists( $config ) )
            {
                $this->_config = require( $config );
            }
            else
            {
                exit( 'The configuration file ' . $config . ' does not exist.' );
            }
        }

        if( isset( $this->_config[ 'object.maps' ] ) )
        {
            $this->_object_maps = $this->_config[ 'object.maps' ];
            unset( $this->_config[ 'object.maps' ] );
        }
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
        if( is_array( $namespaces ) )
        {
            foreach( $namespaces as $namespace => $path )
            {
                static::add_namespace( $namespace, $path );
            }
        }
        elseif( is_dir( $path ) )
        {
            $namespaces = trim( $namespaces, '\\' ) . '\\';

            $this->_packages_paths[ $namespaces ] = $path;
            $this->_psr_namespace_maps[ $namespaces ] = $path . 'core/';
            $this->_psr_namespace_maps[ $namespaces . 'Core\\' ] = $path . 'core/';

            foreach( $this->_config[ 'class.paths' ] as $class_path )
            {
                if( is_dir( $path . $class_path ) )
                {
                    $this->_psr_namespace_maps[ $namespaces . ucfirst( $class_path ) . '\\' ] = $path . $class_path . '/';
                }
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
        if( is_array( $prefixes ) )
        {
            foreach( $prefixes as $prefix => $path )
            {
                static::add_prefix( $prefix, $path );
            }
        }
        elseif( is_dir( $path ) )
        {
            $prefixes = trim( $prefixes, '_' ) . '_';

            $this->_packages_paths[ $prefixes ] = $path;
            $this->_prefixes_maps[ $prefixes ] = $path . 'core/';

            foreach( $this->_config[ 'class.paths' ] as $class_path )
            {
                if( is_dir( $path . $class_path ) )
                {
                    $this->_prefixes_maps[ prepare_class_name( $prefixes . '_' . singular( $class_path ) ) . '_' ] = $path . $class_path . '/';
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
        $this->_modules_maps[ plural( $module ) ] = $module;
    }

    public function get_modules_maps()
    {
        return $this->_modules_maps;
    }

    /**
     * get class
     *
     * @access  public
     *
     * @param   string   the index
     * @param   boolean  the keys
     *
     * @return  void
     */
    public function is_loaded( $class = FALSE, $return = FALSE )
    {
        if( is_bool( $class ) )
        {
            return $class === TRUE ? array_keys( static::$_libraries_classes ) : static::$_libraries_classes;
        }
        else
        {
            $class = $class === 'db' ? 'database' : $class;

            if( isset( static::$_libraries_classes[ strtolower( $class ) ] ) )
            {
                if( $return === TRUE )
                {
                    return static::$_libraries_classes[ strtolower( $class ) ];
                }

                return TRUE;
            }
        }

        return FALSE;
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
    public function spl_autoload( $class )
    {
        if( ! class_exists( $class, FALSE ) )
        {
            $namespace = get_namespace( $class ) . '\\';
            $class = get_namespace_class( $class );

            if( isset( static::$_instance->_psr_namespace_maps[ $namespace ] ) )
            {
                $path = static::$_instance->_psr_namespace_maps[ $namespace ];
            }

            if( isset( $path ) )
            {
                $path = str_replace( '\\', '/', $path );
                $filename = prepare_filename( $class );

                $filepaths = array(
                    $path . $filename . '.php',
                    $path . ucfirst( strtolower( $filename ) ) . '.php',
                    $path . strtolower( $filename ) . '/' . $filename . '.php',
                    $path . $filename . '/' . $filename . '.php',
                );

                $filepaths = array_unique( $filepaths );

                foreach( $filepaths as $filepath )
                {
                    if( file_exists( $filepath ) )
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
        if( isset( $sub_path ) )
        {
            foreach( $this->_packages_paths as $package_path )
            {
                $package_paths[ ] = $package_path . $sub_path . '/';
            }

            return $package_paths;
        }
        else
        {
            return $this->_packages_paths;
        }
    }
}