<?php
/**
 * O2System
 *
 * An open source application development framework for PHP 5.4 or newer
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2015, PT. Lingkar Kreasi (Circle Creative).
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
 * Abstracts Trait Class
 *
 * Use this trait class to get a glob magic methods, if the class has custom constructor method
 *
 * @package        O2System
 * @subpackage     core\glob
 * @category       Factory Class
 * @author         Circle Creative Dev Team
 * @link           http://o2system.center/wiki/#GlobAbstracts
 */
trait Abstracts
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
     * Abstracts Glob is allowed you to set your own constructor first
     * it's the opposite of Basics and Statics
     *
     * Important: you must call __reconstruct function at your __construct function
     * if you're not call the __reconstruct function then the glob magics will failed
     *
     * @access  public
     */
    public function __construct( $config = array() )
    {
        $this->__reconstruct();
    }

    // ------------------------------------------------------------------------

    /**
     * Reconstruct
     * This method is used for initialized Glob Magic Methods
     *
     * @access      protected
     * @final       this method can't be overwritten
     *
     * @property    static::$_reflection
     *              static::$_instance
     *
     * @method      static ::_reflection()
     */
    final protected function __reconstruct()
    {
        $this->_class_name = get_called_class();

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
     * @property    static::$_instance
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
}