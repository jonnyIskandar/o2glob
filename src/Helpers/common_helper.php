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