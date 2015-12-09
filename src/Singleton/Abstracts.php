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
 * @license        http://o2system.in/features/standalone/o2glob/license.html
 * @license        http://opensource.org/licenses/MIT	MIT License
 * @link           http://o2system.in
 * @since          Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------
namespace O2System\Glob\Singleton;

// ------------------------------------------------------------------------
use O2System\Glob\Factory\Magics;

/**
 * Abstracts Trait Class
 *
 * Use this trait class to get a glob magic methods, if the class has custom constructor method
 *
 * @package        o2glob
 * @category       Core Class
 * @author         Circle Creative Dev Team
 * @link           http://o2system.in/features/standalone/o2glob/user-guide/abstracts.html
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
	 * Abstracts Glob is allowed you to set your own constructor first
	 * it's the opposite of Basics and Statics
	 *
	 * Important: you must call __reconstruct function at your __construct function
	 * if you're not call the __reconstruct function then the glob magics will failed
	 *
	 * @access  public
	 */
	public function __construct()
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
		if ( ! isset( self::$_reflection ) )
		{
			// let's the magic begin
			static::_reflection();
		}
		if ( ! isset( self::$_instance ) )
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
	final public static function &initialize( $config = array() )
	{
		return self::instance( $config );
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
		if ( ! isset( self::$_instance ) )
		{
			$class_name = get_called_class();
			self::$_instance = new $class_name( $config );
		}

		return self::$_instance;
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
