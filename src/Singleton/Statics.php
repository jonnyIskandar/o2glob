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

namespace O2System\Glob\Singleton;

// ------------------------------------------------------------------------

use O2System\Glob\Factory\Magics;

/**
 * Statics Trait Class
 *
 * Use this class to build classes with statics methods only
 * The classes will automatically support methods call as static or non static
 * The classes will also has additional features when calling the classes property
 *
 * @package        O2Glob
 * @category       Factory Class
 * @author         Steeven Andrian Salim
 * @link           http://o2system.center/standalone/o2glob/user-guide/statics.html
 */
trait Statics
{
    /**
     * Add magical methods functionality
     */
    use Magics;

    /**
     * Class Name
     *
     * @access  protected
     * @var     string name of called class
     */
    protected $_class_name;

    /**
     * Singleton Static Class Constructor
     *
     * The class is can't be constructed
     * If you still need a constructor create a static method named __reconstruct()
     *
     * @access      protected
     * @final       this method can't be overwritten
     */
    final protected function __construct()
    {
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
     * @method  static::_reflection()
     * @method  static::__reconstruct()
     *
     * @param   array $config class config
     *
     * @return object   called class instance
     */
    final public static function &_init( $config = array() )
    {
        // check singleton instance
        if( ! isset( static::$_instance ) )
        {
            if( ! isset( static::$_reflection ) )
            {
                // let the magic begin
                static::_reflection();
            }

            static::$_instance = static::$_reflection->newInstanceWithoutConstructor();
            static::$_instance->_class_name = get_called_class();
        }

        // call __reconstruct
        if( method_exists( static::$_instance, '__reconstruct' ) )
        {
            static::__reconstruct( $config );
        }

        return static::$_instance;
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
     * @param   array   $config class config
     *
     * @return  object  called class instance
     */
    final public static function &instance( $config = array() )
    {
        if( ! isset( static::$_instance ) )
        {
            static::_init( $config );
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