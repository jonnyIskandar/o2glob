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
 * Drivers
 *
 * This class enables you to create "Driver" libraries that add runtime ability
 * to extend the capabilities of a class via additional driver objects
 *
 * @package        O2Glob
 * @category       Factory Class
 * @author         Steeven Andrian Salim
 * @author         Circle Creative Developer Team
 * @copyright      Copyright (c) 2015, PT. Lingkar Kreasi (Circle Creative).
 * @link           http://o2system.center/standalone/o2glob/wiki#Drivers
 */
abstract class Drivers
{
    protected $_driver_name = NULL;

    /**
     * Instance of the parent class
     *
     * @var object
     */
    protected static $_library;

    public function __construct( array $config = array() )
    {
        // Driver Class
        $this->_driver_name = get_called_class();
    }

    /**
     * Decorate
     *
     * Decorates the child with the parent driver lib's methods and properties
     *
     * @param    object
     *
     * @return    void
     */
    public function _set_library( $library )
    {
        static::$_library =& $library;
    }
    // --------------------------------------------------------------------

    /**
     * __call magic method
     *
     * Handles access to the parent driver library's methods
     *
     * @param    string
     * @param    array
     *
     * @return    mixed
     */
    public function __call( $method, $args = array() )
    {
        if( method_exists( $this, $method ) )
        {
            return $this->$method($args);
        }
        elseif( method_exists( static::$_library, $method ) )
        {
            return static::$_library->$method($args);
        }

        throw new \BadMethodCallException( 'No such method: ' . $method . '()' );
    }
    // --------------------------------------------------------------------

    /**
     * __get magic method
     *
     * Handles reading of the parent driver library's properties
     *
     * @param    string
     *
     * @return    mixed
     */
    public function __get( $property )
    {
        if( property_exists($this, $property) )
        {
            if($property === '_library')
            {
                return static::$_library;
            }
            else
            {
                return $this->{$property};
            }
        }
        elseif( property_exists( static::$_library, $property ) )
        {
            return static::$_library->{$property};
        }
    }
    // --------------------------------------------------------------------

    /**
     * __set magic method
     *
     * Handles writing to the parent driver library's properties
     *
     * @param    string
     * @param    array
     *
     * @return    mixed
     */
    public function __set( $name, $value )
    {
        if( property_exists( get_class(static::$_library), $name ) )
        {
            static::$_library->$name = $value;
        }
        else
        {
            $this->{$name} = $value;
        }
    }
}

/* End of file Drivers.php */
/* Location: ./O2Glob/Factory/Drivers.php */
