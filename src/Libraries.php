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

    protected $_config = array();

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
    public function __construct()
    {
        // Library Class
        $this->_library_name = get_called_class();

        if( ! isset( static::$_reflection ) )
        {
            // let the magic begin
            static::_reflection();
        }

        foreach( glob( $this->_get_drivers_path() . '*.php' ) as $filepath )
        {
            $this->_valid_drivers[ strtolower( pathinfo( $filepath, PATHINFO_FILENAME ) ) ] = $filepath;
        }

        if( ! isset( static::$_instance ) )
        {
            static::$_instance =& $this;
        }
    }
    // ------------------------------------------------------------------------

    final protected function _get_drivers_path()
    {
        $class_realpath = static::$_reflection->getFileName();
        return dirname($class_realpath) . '/' . strtolower( pathinfo($class_realpath, PATHINFO_FILENAME) ) . '/';
    }

    /**
     * Get Override
     *
     * The first time a child is used it won't exist, so we instantiate it
     * subsequents calls will go straight to the proper child.
     *
     * @access      public
     *
     * @param   string $property property or driver name
     *
     * @return mixed    property or driver class object
     */
    public function __getOverride( $property )
    {
        if( property_exists( $this, $property ) )
        {
            return $this->{$property};
        }
        elseif( array_key_exists( $property, $this->_valid_drivers ) )
        {
            // Try to load the driver
            return $this->_load_driver( $property );
        }

        return NULL;
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
    protected function _load_driver( $driver )
    {
        if( empty( $this->{$driver} ) )
        {
            if(file_exists($filepath = $this->_valid_drivers[$driver]))
            {
                require_once($filepath);

                $class_name = get_called_class() . '\\Drivers\\' . ucfirst($driver);

                if(class_exists($class_name, FALSE))
                {
                    $this->{$driver} = new $class_name();
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
    final public static function &_init( $config = array() )
    {
        return static::instance( $config );
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
    final public static function &instance( $config = array() )
    {
        if( ! isset( static::$_instance ) )
        {
            $class_name = get_called_class();
            static::$_instance = new $class_name( $config );
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
    final public function config( $item = NULL )
    {
        if( isset( $this->_config[ $item ] ) )
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
}