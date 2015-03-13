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

/*
 * ------------------------------------------------------
 *  Define O2Glob Path
 * ------------------------------------------------------
 */
    $glob_path = pathinfo( __FILE__, PATHINFO_DIRNAME );
    $glob_path = realpath( $glob_path );
    define( 'GLOB_PATH', $glob_path . '/' );

/*
 * ------------------------------------------------------
 *  Load Core Autoloader
 * ------------------------------------------------------
 */
    require_once( GLOB_PATH . 'core/Loader.php' );
    \O2Glob\Core\Loader::_init();

/**
 * O2Glob Controller Class
 *
 * This class is working as Singleton Controller.
 * Extending your class to O2Glob and make Super Global Singleton Object
 *
 * @package        O2Glob
 * @category       Core Class
 * @author         Steeven Andrian Salim
 * @link           http://o2system.center/standalone/o2glob/user-guide/getting-started.html
 */
class O2Glob
{
    /**
     * Using Basic Singleton Factory Trait Class
     */
    use \O2Glob\Factory\Basics;

    /**
     * O2Glob Version
     *
     * @var string
     */
    const VERSION = '1.0';

    /**
     * Initialize Class
     *
     * Initialize method work as replacement method for __construct() method
     * Every extending class using O2Glob as the parent cannot have a __construct() method
     *
     * At the child class __initialize() method you must call parent::__initialize();
     *
     * @access public
     * @return void
     */
    public function __initialize()
    {
        // Load Classes
        foreach ( \O2Glob\Core\Loader::classes() as $class_object_name )
        {
            $this->{ $class_object_name } =& \O2Glob\Core\Loader::library( $class_object_name );
        }
    }
}
/* End of file O2Glob.php */
/* Location: ./o2glob/O2Glob.php */