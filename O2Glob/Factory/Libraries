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
 * Libraries
 *
 * This class enables you to create "Driver" libraries that add runtime ability
 * to extend the capabilities of a class via additional driver objects
 *
 * @package        O2System
 * @subpackage     system/core
 * @category       Core Class
 * @author         Steeven Andrian Salim
 * @author         Circle Creative Developer Team
 * @copyright      Copyright (c) 2015, PT. Lingkar Kreasi (Circle Creative). 
 * @link           http://circle-creative.com/products/o2system/user-guide/core/library.html
 */
abstract class Libraries
{
    use Magics;

    /**
     * Name of the current class - usually the library class
     *
     * @access protected
     * @var string
     */
    protected $_library_name;
    
    /**
     * List of valid drivers
     *
     * @access protected
     * @var array
     */
    protected $_valid_drivers = array();

    /**
     * Class constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Library Class
        $this->_library_name = get_called_class();

        $path = namespace_to_path( $this->_library_name );

        $sub_path = str_replace( 'O2', '', get_namespace_class( $this->_library_name ) );
        $sub_path = ucfirst( $sub_path ) . _DS_;

        $path = str_replace( '\\', _DS_, strtolower( $path . $sub_path ) );

        Loader::add_namespace( $this->_library_name, $path );
        Loader::add_namespace( $this->_library_name . '\Drivers', $path . 'drivers/' );

        $drivers = glob( $path . 'drivers/*.php' );

        if( ! empty( $drivers ) )
        {
            foreach( glob( $path . 'drivers/*.php' ) as $filepath )
            {
                $this->_valid_drivers[ ] = strtolower( pathinfo( $filepath, PATHINFO_FILENAME ) );
            }
        }

        if( ! isset( static::$_reflection ) )
        {
            static::$_reflection = new \ReflectionClass( $this->_library_name );

            $methods = array(
                'public'    => \ReflectionMethod::IS_PUBLIC,
                'protected' => \ReflectionMethod::IS_PROTECTED,
                'private'   => \ReflectionMethod::IS_PRIVATE,
                'static'    => \ReflectionMethod::IS_STATIC
            );

            foreach( $methods as $method => $reflect )
            {
                $reflection = static::$_reflection->getMethods( $reflect );

                if( ! empty( $reflection ) )
                {
                    foreach( $reflection as $object )
                    {
                        static::$_methods[ $method ][ ] = $object->name;
                    }
                }
            }

            $properties = array(
                'public'    => \ReflectionProperty::IS_PUBLIC,
                'protected' => \ReflectionProperty::IS_PROTECTED,
                'private'   => \ReflectionProperty::IS_PRIVATE,
                'static'    => \ReflectionProperty::IS_STATIC
            );

            foreach( $properties as $property => $reflect )
            {
                $reflection = static::$_reflection->getProperties( $reflect );

                if( ! empty( $reflection ) )
                {
                    foreach( $reflection as $object )
                    {
                        static::$_properties[ $property ][ ] = $object->name;
                    }
                }
            }
        }

        if( ! isset( static::$_instance ) )
        {
            static::$_instance =& $this;
        }
    }

    /**
     * Get magic method
     *
     * The first time a child is used it won't exist, so we instantiate it
     * subsequents calls will go straight to the proper child.
     *
     * @param    string    Driver class name
     *
     * @return    object    Driver class
     */
    public function __get( $driver )
    {
        if( property_exists( $this, $driver ) )
        {
            return $this->{$driver};
        }
        elseif( in_array( $driver, $this->_valid_drivers ) )
        {
            // Try to load the driver
            return $this->_load_driver( $driver );
        }

        return NULL;
    }

    /**
     * Load driver
     *
     * Separate load_driver call to support explicit driver load by library or user
     *
     * @param    string    Driver name (w/o parent prefix)
     *
     * @return    object    Driver class
     */
    protected function _load_driver( $driver )
    {
        if( ! isset( $this->{$driver} ) )
        {
            // Driver Class
            $class_name = $this->_library_name . '\Drivers\\' . prepare_class_name( $driver );

            // Instantiate Driver
            $object_class = new $class_name();
            $object_class->_set_library( $this );

            // Assign to Library
            $this->{$driver} = $object_class;
        }

        return $this->{$driver};
    }
}
/* End of file Libraries.php */
/* Location: ./O2Glob/Factory/Libraries.php */
