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

if( ! function_exists( 'is_php' ) )
{
    /**
     * Determines if the current version of PHP is equal to or greater than the supplied value
     *
     * @param    string
     *
     * @return    bool    TRUE if the current version is $version or higher
     */
    function is_php( $version )
    {
        static $_is_php;
        $version = (string)$version;

        if( ! isset( $_is_php[ $version ] ) )
        {
            $_is_php[ $version ] = version_compare( PHP_VERSION, $version, '>=' );
        }

        return $_is_php[ $version ];
    }
}

// ------------------------------------------------------------------------

if( ! function_exists( 'is_really_writable' ) )
{
    /**
     * Tests for file writability
     *
     * is_writable() returns TRUE on Windows servers when you really can't write to
     * the file, based on the read-only attribute. is_writable() is also unreliable
     * on Unix servers if safe_mode is on.
     *
     * @link    https://bugs.php.net/bug.php?id=54709
     *
     * @param    string
     *
     * @return    void
     */
    function is_really_writable( $file )
    {
        // If we're on a Unix server with safe_mode off we call is_writable
        if( DIRECTORY_SEPARATOR === '/' && ( is_php( '5.4' ) || ! ini_get( 'safe_mode' ) ) )
        {
            return is_writable( $file );
        }

        /* For Windows servers and safe_mode "on" installations we'll actually
         * write a file then read it. Bah...
         */
        if( is_dir( $file ) )
        {
            $file = rtrim( $file, '/' ) . '/' . md5( mt_rand() );
            if( ( $fp = @fopen( $file, 'ab' ) ) === FALSE )
            {
                return FALSE;
            }

            fclose( $fp );
            @chmod( $file, 0777 );
            @unlink( $file );

            return TRUE;
        }
        elseif( ! is_file( $file ) || ( $fp = @fopen( $file, 'ab' ) ) === FALSE )
        {
            return FALSE;
        }

        fclose( $fp );

        return TRUE;
    }
}

// ------------------------------------------------------------------------

if( ! function_exists( 'is_https' ) )
{
    /**
     * Is HTTPS?
     *
     * Determines if the application is accessed via an encrypted
     * (HTTPS) connection.
     *
     * @return    bool
     */
    function is_https()
    {
        if( ! empty( $_SERVER[ 'HTTPS' ] ) && strtolower( $_SERVER[ 'HTTPS' ] ) !== 'off' )
        {
            return TRUE;
        }
        elseif( isset( $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] ) && $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] === 'https' )
        {
            return TRUE;
        }
        elseif( ! empty( $_SERVER[ 'HTTP_FRONT_END_HTTPS' ] ) && strtolower( $_SERVER[ 'HTTP_FRONT_END_HTTPS' ] ) !== 'off' )
        {
            return TRUE;
        }

        return FALSE;
    }
}

// ------------------------------------------------------------------------

if( ! function_exists( 'is_cli' ) )
{
    /**
     * Is CLI?
     *
     * Test to see if a request was made from the command line.
     *
     * @return    bool
     */
    function is_cli()
    {
        return ( PHP_SAPI === 'cli' || defined( 'STDIN' ) );
    }
}

// ------------------------------------------------------------------------

if( ! function_exists( 'remove_invisible_characters' ) )
{
    /**
     * Remove Invisible Characters
     *
     * This prevents sandwiching null characters
     * between ascii characters, like Java\0script.
     *
     * @param    string
     * @param    bool
     *
     * @return    string
     */
    function remove_invisible_characters( $str, $url_encoded = TRUE )
    {
        $non_displayables = array();

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if( $url_encoded )
        {
            $non_displayables[ ] = '/%0[0-8bcef]/';    // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[ ] = '/%1[0-9a-f]/';    // url encoded 16-31
        }

        $non_displayables[ ] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127

        do
        {
            $str = preg_replace( $non_displayables, '', $str, -1, $count );
        }
        while( $count );

        return $str;
    }
}

// ------------------------------------------------------------------------

if( ! function_exists( 'html_escape' ) )
{
    /**
     * Returns HTML escaped variable.
     *
     * @param    mixed $var           The input string or array of strings to be escaped.
     * @param    bool  $double_encode $double_encode set to FALSE prevents escaping twice.
     *
     * @return    mixed            The escaped string or array of strings as a result.
     */
    function html_escape( $var, $double_encode = TRUE )
    {
        if( is_array( $var ) )
        {
            return array_map( 'html_escape', $var, array_fill( 0, count( $var ), $double_encode ) );
        }

        return htmlspecialchars( $var, ENT_QUOTES, config_item( 'charset' ), $double_encode );
    }
}

// ------------------------------------------------------------------------

if( ! function_exists( '_stringify_attributes' ) )
{
    /**
     * Stringify attributes for use in HTML tags.
     *
     * Helper function used to convert a string, array, or object
     * of attributes to a string.
     *
     * @param    mixed    string, array, object
     * @param    bool
     *
     * @return    string
     */
    function _stringify_attributes( $attributes, $js = FALSE )
    {
        $atts = NULL;

        if( empty( $attributes ) )
        {
            return $atts;
        }

        if( is_string( $attributes ) )
        {
            return ' ' . $attributes;
        }

        $attributes = (array)$attributes;

        foreach( $attributes as $key => $val )
        {
            $atts .= ( $js ) ? $key . '=' . $val . ',' : ' ' . $key . '="' . $val . '"';
        }

        return rtrim( $atts, ',' );
    }
}

// ------------------------------------------------------------------------

if( ! function_exists( 'function_usable' ) )
{
    /**
     * Function usable
     *
     * Executes a function_exists() check, and if the Suhosin PHP
     * extension is loaded - checks whether the function that is
     * checked might be disabled in there as well.
     *
     * This is useful as function_exists() will return FALSE for
     * functions disabled via the *disable_functions* php.ini
     * setting, but not for *suhosin.executor.func.blacklist* and
     * *suhosin.executor.disable_eval*. These settings will just
     * terminate script execution if a disabled function is executed.
     *
     * The above described behavior turned out to be a bug in Suhosin,
     * but even though a fix was commited for 0.9.34 on 2012-02-12,
     * that version is yet to be released. This function will therefore
     * be just temporary, but would probably be kept for a few years.
     *
     * @link    http://www.hardened-php.net/suhosin/
     *
     * @param    string $function_name Function to check for
     *
     * @return    bool    TRUE if the function exists and is safe to call,
     *            FALSE otherwise.
     */
    function function_usable( $function_name )
    {
        static $_suhosin_func_blacklist;

        if( function_exists( $function_name ) )
        {
            if( ! isset( $_suhosin_func_blacklist ) )
            {
                if( extension_loaded( 'suhosin' ) )
                {
                    $_suhosin_func_blacklist = explode( ',', trim( ini_get( 'suhosin.executor.func.blacklist' ) ) );

                    if( ! in_array( 'eval', $_suhosin_func_blacklist, TRUE ) && ini_get( 'suhosin.executor.disable_eval' ) )
                    {
                        $_suhosin_func_blacklist[ ] = 'eval';
                    }
                }
                else
                {
                    $_suhosin_func_blacklist = array();
                }
            }

            return ! in_array( $function_name, $_suhosin_func_blacklist, TRUE );
        }

        return FALSE;
    }
}

if( ! function_exists( 'prepare_namespace' ) )
{
    /**
     * Return a valid namespace class
     *
     * @param    string $class class name with namespace
     *
     * @return   string     valid string namespace
     */
    function prepare_namespace( $class )
    {
        $class = explode( '/', $class );
        $class = array_map( 'ucfirst', $class );

        return implode( '\\', $class );
    }
}
// ------------------------------------------------------------------------

if( ! function_exists( 'prepare_class_name' ) )
{
    /**
     * Return a valid class naming
     *
     * @param    string $class class name
     *
     * @return   string     valid string class name
     */
    function prepare_class_name( $class )
    {
        $class = trim( $class );
        if( ! empty( $class ) or $class != '' )
        {
            $patterns = array(
                '/[\s]+/',
                '/[^a-zA-Z0-9]/',
                '/[_]+/',
                '/[-]+/',
                '/-/',
                '/[_]+/'
            );
            $replace = array(
                '-',
                '-',
                '-',
                '-',
                '_',
                '_'
            );
            $class = preg_replace( $patterns, $replace, $class );
        }

        $class = explode( '_', $class );

        $class_name = array_map(
            function ( $class_name )
            {
                $class_name = trim( $class_name );
                $class_name = str_replace( '_', '', $class_name );

                return ucfirst( $class_name );
            }, $class
        );

        // Remove Duplicates
        $class_name = array_unique( $class_name );
        $class_name = array_filter( $class_name );

        return implode( '_', $class_name );
    }
}
// ------------------------------------------------------------------------

if( ! function_exists( 'prepare_filename' ) )
{
    /**
     * Returns Fixed Class Name according to O2System Class Name standards
     *
     * @param    $class    String class name
     *
     * @return   mixed
     */
    function prepare_filename( $filename )
    {
        $filename = preg_split( '[/]', $filename, -1, PREG_SPLIT_NO_EMPTY );

        // Remove Duplicates
        $filename = array_unique( $filename );

        $filename = array_map(
            function ( $filename )
            {
                return prepare_class_name( $filename );
            }, $filename
        );

        return implode( '/', $filename );
    }
}
// ------------------------------------------------------------------------

if( ! function_exists( 'get_namespace' ) )
{
    /**
     * Return the namespace from class
     *
     * @param   string $path Class name
     *
     * @return  string  namespace class
     */
    function get_namespace( $class )
    {
        $x_class = explode( '\\', $class );
        $x_class = array_slice($x_class, 0, count($x_class)-1);

        return implode( '\\', $x_class );
    }
}
// ------------------------------------------------------------------------

if( ! function_exists( 'get_namespace_class' ) )
{
    /**
     * Return the class name of namespace class
     *
     * @param   string $path Class name
     *
     * @return  string  namespace class
     */
    function get_namespace_class( $namespace )
    {
        $x_namespace = explode( '\\', $namespace );

        return end( $x_namespace );
    }
}
// ------------------------------------------------------------------------

if( ! function_exists( 'namespace_to_path' ) )
{
    /**
     * Return a realpath of namespace class
     *
     * Note: This does not check if the file exists...just gets the path
     *
     * @param   string $namespace Class name
     *
     * @return  string  realpath for the class
     */
    function namespace_to_path( $namespace, $root_path = NULL )
    {
        $namespace = get_namespace( $namespace );
        $path = str_replace( '\\', '/', $namespace );
        $path = ROOTPATH . strtolower( $path ) . '/';

        $psr_namespaces = \O2System\Loader::_get_namespaces();

        if( isset( $psr_namespaces[ $namespace ] ) )
        {
            $path = $psr_namespaces[ $namespace ];
        }
        else
        {
            $x_namespace = explode( '\\', $namespace );
            $x_path = array_slice( $x_namespace, 2 );
            $x_namespace = array_slice( $x_namespace, 0, 2 );

            // Try to get root namespace
            if( isset( $psr_namespaces[ implode( '\\', $x_namespace ) ] ) )
            {
                $path = $psr_namespaces[ implode( '\\', $x_namespace ) ] . strtolower( implode( '/', $x_path ) ) . '/';

            }
        }

        return $path;
    }
}
// ------------------------------------------------------------------------

if( ! function_exists( 'path_to_namespace' ) )
{
    /**
     * Return a valid namespace naming from realpath
     *
     * Note: This does not check if the file exists...just create a namespace naming
     *
     * @param   string $path realpath of a class
     *
     * @return  string  namespace class
     */
    function path_to_namespace( $path )
    {
        $path = str_replace( '\\', '/', $path );

        $class = explode( '/', $path );
        $class = array_map( 'ucfirst', $class );
        $class = array_filter( $class );

        $namespace = implode( '\\', $class );
        $namespace = str_replace( 'O2system', 'O2System', $namespace );

        $path = ROOTPATH . strtolower( $namespace ) . '/';
        $path = str_replace( '\\', '/', $path );

        $psr_namespaces = \O2System::Loader()->get_namespaces();
        $psr_namespace = array_search( $path, $psr_namespaces );

        if( ! empty( $psr_namespace ) )
        {
            $namespace = $psr_namespace;
        }

        return $namespace;
    }
}
// ------------------------------------------------------------------------