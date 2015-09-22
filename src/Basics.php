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
 * Basics Trait Class
 *
 * Use this trait class if the class allowed to be constructed and will be combined with the global super o2system
 * object (singleton)
 *
 * @package        O2System
 * @subpackage     core\glob
 * @category       Factory Class
 * @author         Circle Creative Dev Team
 * @link           http://o2system.center/wiki/#GlobBasics
 */
trait Basics
{
    /**
     * Using Glob Magics Trait Class
     *
     * @uses \O2System\Glob\Magics
     */
    use Magics;

    /**
     * Called Class Name
     *
     * @access  protected
     *
     * @type    string   called class name
     */
    protected $_class_name;

    /**
     * Singleton Basic Class Constructor
     *
     * The class is still able to be constructed, but not allowed to overwrite the __construct() method
     * If you still need a constructor create a method named __reconstruct()
     *
     * @access      public
     * @final       this method can't be overwritten
     *
     * @property-read    static::$_reflection
     * @property-read    static::$_instance
     *
     * @method  static ::_reflection()
     */
    final public function __construct( $config = array() )
    {
        $this->_class_name = get_called_class();

        // checking custom constructor
        if( method_exists( $this, '__reconstruct' ) )
        {
            $this->__reconstruct( $config );
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

    // ------------------------------------------------------------------------

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