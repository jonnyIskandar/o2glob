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

namespace O2Glob\Core;
defined( 'GLOB_PATH' ) OR exit( 'No direct script access allowed' );

/**
 * Loader Class
 *
 * This class is working as Singleton Loader Library.
 *
 * @package        O2Glob
 * @category       Core Class
 * @author         Steeven Andrian Salim
 * @link           http://o2system.center/standalone/o2glob/user-guide/getting-started.html
 */
class Loader
{
    /**
     * Singleton class registry
     *
     * @access  private
     * @var     array
     */
    private static $_libraries_classes = array();

    /**
     * Alias classes registry
     *
     * @access  private
     * @var     array
     */
    private static $_alias_classes = array();

    /**
     * Variables registry
     *
     * @access  private
     * @var     array
     */
    private static $_vars = array();

    /**
     * Class Initialize
     *
     * @access  public
     *
     * @replace false       final method cannot be replaced
     *
     * @return  void
     */
    final public static function _init()
    {
        // Register PSR-4 Autoloader
        spl_autoload_register( '\O2Glob\Core\Loader::__autoload', TRUE, TRUE );
    }

    /**
     * PSR-4 Autoloader
     *
     * @access  public
     *
     * @param   $class    name of called class
     *
     * @replace false     final method cannot be replaced
     *
     * @return  bool
     */
    final public static function __autoload( $class )
    {
        $class     = ltrim( $class, '\\' );
        $filename  = '';
        $namespace = '';
        if ( $last_namespace_pos = strrpos( $class, '\\' ) )
        {
            $namespace = substr( $class, 0, $last_namespace_pos );
            $class     = substr( $class, $last_namespace_pos + 1 );
            $filename  = str_replace( '\\', DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;
        }
        $filename .= str_replace( '_', DIRECTORY_SEPARATOR, static::__prepare_class_name( $class ) ) . '.php';

        if ( ! defined( 'ROOT_PATH' ) )
        {
            throw new \Exception( 'Undefined root path of your application' );
        }

        $filepath = ROOT_PATH . $filename;

        if ( file_exists( $filepath ) )
        {
            require_once( $filepath );

            $class = strtolower( $class );
            if ( ! isset( static::$_libraries_classes[ $class ] ) )
            {
                static::__init_class( $class );
            }
        }
    }

    /**
     * Convert Class Name to standard PSR-4 Class Name and Filename
     *
     * @access  private
     *
     * @param   $class   name of called class
     *
     * @replace false    final method cannot be replaced
     *
     * @return  string   PSR-4 Standard Class Name
     */
    final private static function __prepare_class_name( $class )
    {
        $class       = explode( '_', $class );
        $class_names = array_map( function ( $class_name )
        {
            return ucfirst( $class_name );
        }, $class );

        return implode( '_', $class_names );
    }

    /**
     * Get Singleton Class Registry
     *
     * @access  public
     *
     * @param   $index    classes | libraries | alias
     * @param   $keys     return array keys of called registry
     *
     * @replace false     final method cannot be replaced
     *
     * @return  array
     */
    final public static function get_classes( $index = 'libraries', $keys = FALSE )
    {
        switch ( $index )
        {
            case 'classes':
            case 'libraries':
                return $keys === TRUE ? array_keys( static::$_libraries_classes ) : static::$_libraries_classes;
                break;
            case 'alias':
                return $keys === TRUE ? array_keys( static::$_alias_classes ) : static::$_alias_classes;
                break;
        }
    }

    /**
     * Set Variables
     *
     * Once variables are set they become available within
     * the controller class and its "view" files.
     *
     * @param    mixed  $vars An associative array or object containing values to be set, or a value's name if string
     * @param    string $val  Value to set, only used if $vars is a string
     *
     * @return    object
     */
    public static function vars( $vars, $value = '' )
    {
        if ( is_string( $vars ) )
        {
            $vars = array( $vars => $value );
        }

        if ( is_object( $vars ) )
        {
            $vars = get_object_vars( $vars );
        }

        if ( is_array( $vars ) AND count( $vars ) > 0 )
        {
            foreach ( $vars as $key => $val )
            {
                static::$_vars[ $key ] = $val;
            }
        }
    }
    // --------------------------------------------------------------------

    /**
     * Clear Cached Variables
     *
     * Clears the cached variables.
     *
     * @return  Class Chaining Method
     */
    public static function flush_vars()
    {
        static::$_vars = array();
    }
    // --------------------------------------------------------------------

    /**
     * Get Variable
     *
     * Check if a variable is set and retrieve it.
     *
     * @param    string $index Variable name
     *
     * @return    mixed    The variable or NULL if not found
     */
    public static function get_vars( $index )
    {
        if ( $index === 'ALL' )
        {
            return static::$_vars;
        }
        elseif ( isset( static::$_vars[ $index ] ) )
        {
            return static::$_vars[ $index ];
        }

        return NULL;
    }

    /**
     * Library class loader
     *
     * @access  public
     *
     * @param   $class          name of called class
     * @param   $params         array of parameters
     * @param   $object_name    alias name of called class
     *
     * @replace false       final method cannot be replaced
     *
     * @return  object      object of called class
     */
    final public static function &library( $class, $params = NULL, $object_name = NULL )
    {
        $class = strtolower( $class );

        static::__init_class( $class, $params, $object_name );

        return static::$_libraries_classes[ $class ];
    }

    /**
     * Instantiate called class
     *
     * @access  private
     *
     * @param   $class          name of called class
     * @param   $params         array of parameters
     * @param   $object_name    alias name of called class
     *
     * @replace false       final method cannot be replaced
     *
     * @return void
     */
    final private static function __init_class( $class, $params = NULL, $object_name = NULL )
    {
        // if the loaded file contains a class...
        if ( class_exists( $class, FALSE ) )
        {
            $alias_class = isset( $object_name ) ? ucfirst( $object_name ) : pathinfo( $class, PATHINFO_FILENAME );

            if ( ! class_exists( $alias_class, FALSE ) )
            {
                static::$_alias_classes[ $alias_class ] = $class;
                class_alias( $class, $alias_class );
            }

            if ( method_exists( $class, '_init' ) and is_callable( $class . '::_init' ) )
            {
                return call_user_func( $class . '::_init', $params );
            }
            else
            {
                return new $class( $params );
            }
        } // or an interface...
        elseif ( interface_exists( $class, FALSE ) )
        {
            // nothing to do here
        } // or a trait if you're not on 5.3 anymore...
        elseif ( function_exists( 'trait_exists' ) and trait_exists( $class, FALSE ) )
        {
            // nothing to do here
        }
        else
        {
            throw new \Exception( 'Cannot find requested class:' . $class );
        }
    }
}

/* End of file Loader.php */
/* Location: ./O2Glob/Core/Loader.php */