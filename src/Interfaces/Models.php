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
 * Models
 *
 * This class enables you to create "sub_model" libraries that add runtime ability
 * to extend the capabilities of a class via additional sub_model objects
 *
 * @package        O2System
 * @subpackage     core\glob
 * @category       Factory Class
 * @author         Circle Creative Dev Team
 * @link           http://o2system.center/wiki/#GlobLibraries
 */
abstract class Models
{
    /**
     * Using Glob Magics Trait Class
     *
     * @uses \O2System\Glob\Magics
     */
    use Magics;

    /**
     * Called sub_model Name
     *
     * @access  protected
     *
     * @type    string   sub_model class name
     */
    protected $_model_name = NULL;

    /**
     * List of valid sub models
     *
     * @access  protected
     *
     * @type    array   sub_model classes list
     */
    protected $_valid_sub_models = array();

    /**
     * Model Class Constructor
     *
     * @access  public
     *
     * @method  static ::reflection()
     */
    public function __construct()
    {
        // Model Class
        $this->_model_name = get_called_class();

        // let the magic begin
        static::_reflection();

        foreach( glob( $this->_get_sub_models_path() . '*.php' ) as $filepath )
        {
            $this->_valid_sub_models[ strtolower( pathinfo( $filepath, PATHINFO_FILENAME ) ) ] = $filepath;
        }

        // set class instance
        static::$_instance =& $this;
    }
    // ------------------------------------------------------------------------

    final protected function _get_sub_models_path()
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
     * @param   string $property property or sub_model name
     *
     * @return mixed    property or sub_model class object
     */
    public function &__getOverride( $property )
    {
        if( property_exists( $this, $property ) )
        {
            return $this->{$property};
        }
        elseif( array_key_exists( $property, $this->_valid_sub_models ) )
        {
            // Try to load the sub_model
            return $this->_load_sub_model( $property );
        }

        // Dummy property for avoiding error
        $dummy_property = NULL;

        return $dummy_property;
    }

    // ------------------------------------------------------------------------

    final protected function _get_sub_models_path()
    {
        $class_realpath = static::$_reflection->getFileName();

        return dirname( $class_realpath ) . '/' . strtolower( pathinfo( $class_realpath, PATHINFO_FILENAME ) ) . '/';
    }

    /**
     * Load sub_model
     *
     * Separate load_sub_model call to support explicit sub_model load by library or user
     *
     * @param   string $sub_model sub_model class name (lowercase)
     *
     * @return    object    sub_model class
     */
    protected function _load_sub_model( $sub_model )
    {
        if( file_exists( $this->_valid_sub_models[ $sub_model ] ) )
        {
            require_once( $this->_valid_sub_models[ $sub_model ] );

            if( strpos( $this->_model_name, '\\' ) !== FALSE )
            {
                $class_name = '\\' . $this->_model_name . '\\' . ucfirst( $sub_model );
            }
            else
            {
                $class_name = ucfirst( $sub_model ) . '_Model';
            }

            if( class_exists( $class_name ) )
            {
                $this->{$sub_model} = new $class_name();

                if( isset( $this->db ) )
                {
                    $this->{$sub_model}->db = $this->db;
                }
            }
        }

        return $this->{$sub_model};
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
    final public static function &initialize( $config = array() )
    {
        return static::instance( $config );
    }

    // ------------------------------------------------------------------------

    /**
     * Instance
     *
     * Instance class caller
     *
     * @access      public
     * @static      static class method
     * @final       this method can't be overwritten
     * @return  object  called class instance
     */
    final public static function &instance()
    {
        if( ! isset( static::$_instance ) )
        {
            $class_name = get_called_class();
            static::$_instance = new $class_name();
        }

        return static::$_instance;
    }

    // ------------------------------------------------------------------------

    /**
     * Get Library Config Item
     *
     * @param string|null $item Config item index name
     *
     * @return array|null
     *
     * @access  public
     * @final   this method can't be overwritten
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