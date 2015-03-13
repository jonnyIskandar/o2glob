<?php
/**
 * O2Glob
 *
 * An mini open source application development framework for PHP 5.3 or newer
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

namespace O2Glob\Factory;
defined( 'GLOB_PATH' ) OR exit( 'No direct script access allowed' );

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
    protected static $_class_name;

    /**
     * Class protection so cannot be able to construct
     *
     * @access protected
     *
     * @replace false       final method cannot be replaced
     *
     * @return void
     */
    final protected function __construct(){ }

    /**
     * Class Init
     * Method to construct a static class
     *
     * @access public
     *
     * @param   array       is'nt really necessary unless when need a parameter
     *                      to be parsing to __initialize() method
     *
     * @replace false       final method cannot be replaced
     *
     * @return void
     */
    final public static function &_init( $params = array() )
    {
        static::$_class_name = get_called_class();

        if ( ! isset( static::$_instance ) )
        {
            static::$_reflection = new \ReflectionClass( static::$_class_name );
            static::$_instance   = static::$_reflection->newInstanceWithoutConstructor();
        }

        if ( static::$_reflection->hasMethod( '__initialize' ) )
        {
            static::__initialize( $params );
        }

        return static::$_instance;
    }

    /**
     * Get Class Instance
     *
     * @access public
     *
     * @param   array       is'nt really necessary unless when need a parameter
     *                      to be parsing to __initialize() method
     *
     * @replace false       final method cannot be replaced
     *
     * @return  object of called class
     */
    final public static function &get_instance( $params = array() )
    {
        if ( ! isset( static::$_instance ) )
        {
            static::_init( $params );
        }

        return static::$_instance;
    }

    /**
     * Disabled reinstate handles and object references
     *
     * @access protected
     *
     * @replace false       final method cannot be replaced
     *
     * @return void
     */
    final protected function __wakeup(){ }
}
/* End of file Statics.php */
/* Location: ./O2Glob/Factory/Statics.php */