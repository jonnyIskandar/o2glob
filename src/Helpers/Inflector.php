<?php
/**
 * O2Glob
 *
 * Global Common Class Libraries for PHP 5.4 or newer
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014, .
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
 * @copyright      Copyright (c) 2005 - 2014, .
 * @license        http://circle-creative.com/products/o2glob/license.html
 * @license        http://opensource.org/licenses/MIT	MIT License
 * @link           http://circle-creative.com
 * @since          Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------
if ( ! function_exists( 'singular' ) )
{
	/**
	 * Singular
	 *
	 * Takes a plural word and makes it singular
	 *
	 * @param    string $string Input string
	 *
	 * @return    string
	 */
	function singular( $string )
	{
		$result = strval( $string );
		if ( ! is_countable( $result ) )
		{
			return $result;
		}
		$singular_rules = array(
			'/(matr)ices$/'                                                   => '\1ix',
			'/(vert|ind)ices$/'                                               => '\1ex',
			'/^(ox)en/'                                                       => '\1',
			'/(alias)es$/'                                                    => '\1',
			'/([octop|vir])i$/'                                               => '\1us',
			'/(cris|ax|test)es$/'                                             => '\1is',
			'/(shoe)s$/'                                                      => '\1',
			'/(o)es$/'                                                        => '\1',
			'/(bus|campus)es$/'                                               => '\1',
			'/([m|l])ice$/'                                                   => '\1ouse',
			'/(x|ch|ss|sh)es$/'                                               => '\1',
			'/(m)ovies$/'                                                     => '\1\2ovie',
			'/(s)eries$/'                                                     => '\1\2eries',
			'/([^aeiouy]|qu)ies$/'                                            => '\1y',
			'/([lr])ves$/'                                                    => '\1f',
			'/(tive)s$/'                                                      => '\1',
			'/(hive)s$/'                                                      => '\1',
			'/([^f])ves$/'                                                    => '\1fe',
			'/(^analy)ses$/'                                                  => '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/' => '\1\2sis',
			'/([ti])a$/'                                                      => '\1um',
			'/(p)eople$/'                                                     => '\1\2erson',
			'/(m)en$/'                                                        => '\1an',
			'/(s)tatuses$/'                                                   => '\1\2tatus',
			'/(c)hildren$/'                                                   => '\1\2hild',
			'/(n)ews$/'                                                       => '\1\2ews',
			'/([^us])s$/'                                                     => '\1',
		);
		foreach ( $singular_rules as $rule => $replacement )
		{
			if ( preg_match( $rule, $result ) )
			{
				$result = preg_replace( $rule, $replacement, $result );
				break;
			}
		}
		return $result;
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists( 'plural' ) )
{
	/**
	 * Plural
	 *
	 * Takes a singular word and makes it plural
	 *
	 * @param    string $string Input string
	 *
	 * @return    string
	 */
	function plural( $string )
	{
		$result = strval( $string );
		if ( ! is_countable( $result ) )
		{
			return $result;
		}
		$plural_rules = array(
			'/^(ox)$/'                => '\1\2en',     // ox
			'/([m|l])ouse$/'          => '\1ice',      // mouse, louse
			'/(matr|vert|ind)ix|ex$/' => '\1ices',     // matrix, vertex, index
			'/(x|ch|ss|sh)$/'         => '\1es',       // search, switch, fix, box, process, address
			'/([^aeiouy]|qu)y$/'      => '\1ies',      // query, ability, agency
			'/(hive)$/'               => '\1s',        // archive, hive
			'/(?:([^f])fe|([lr])f)$/' => '\1\2ves',    // half, safe, wife
			'/sis$/'                  => 'ses',        // basis, diagnosis
			'/([ti])um$/'             => '\1a',        // datum, medium
			'/(p)erson$/'             => '\1eople',    // person, salesperson
			'/(m)an$/'                => '\1en',       // man, woman, spokesman
			'/(c)hild$/'              => '\1hildren',  // child
			'/(buffal|tomat)o$/'      => '\1\2oes',    // buffalo, tomato
			'/(bu|campu)s$/'          => '\1\2ses',    // bus, campus
			'/(alias|status|virus)$/' => '\1es',       // alias
			'/(octop)us$/'            => '\1i',        // octopus
			'/(ax|cris|test)is$/'     => '\1es',       // axis, crisis
			'/s$/'                    => 's',          // no change (compatibility)
			'/$/'                     => 's',
		);
		foreach ( $plural_rules as $rule => $replacement )
		{
			if ( preg_match( $rule, $result ) )
			{
				$result = preg_replace( $rule, $replacement, $result );
				break;
			}
		}
		return $result;
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists( 'studlycapcase' ) )
{
	/**
	 * Convert a value to studly caps case (StudlyCapCase).
	 *
	 * @param  string $string
	 *
	 * @return string
	 */
	function studlycapcase( $string )
	{
		$string = ucwords( str_replace( array( '-', '_' ), ' ', $string ) );
		return str_replace( ' ', '', $string );
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists( 'camelcase' ) )
{
	/**
	 * Camelize
	 *
	 * Takes multiple words separated by spaces or underscores and camelizes them
	 *
	 * @param    string $string Input string
	 *
	 * @return    string
	 */
	function camelcase( $string )
	{
		return strtolower( $string[ 0 ] ) . substr( str_replace( ' ', '', ucwords( preg_replace( '/[\s_]+/', ' ', $string ) ) ), 1 );
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists( 'decamelcase' ) )
{
	function decamelcase( $string )
	{
		$string = preg_replace( '/(?<=\\b)(?=[A-Z])/', "_$1", $string );
		return trim( strtolower( $string ), '_' );
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists( 'underscore' ) )
{
	/**
	 * Underscore
	 *
	 * Takes multiple words separated by spaces and underscores them
	 *
	 * @param    string $string Input string
	 *
	 * @return    string
	 */
	function underscore( $string )
	{
		$string = strtolower( trim( $string ) );
		$string = preg_replace( "/[^A-Za-z0-9 ]/", '', $string );
		return preg_replace( '/[ -]+/', '_', $string );
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists( 'dash' ) )
{
	/**
	 * Dash
	 *
	 * Takes multiple words separated by spaces and dashes them
	 *
	 * @param    string $string Input string
	 *
	 * @access  public
	 * @return  string
	 */
	function dash( $string )
	{
		$string = strtolower( trim( $string ) );
		$string = preg_replace( "/[^A-Za-z0-9 ]/", '', $string );
		return preg_replace( '/[ _]+/', '-', $string );
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists( 'readable' ) )
{
	/**
	 * Readable
	 *
	 * Takes multiple words separated by the separator and changes them to spaces
	 *
	 * @param    string $string    Input string
	 * @param    string $separator Input separator
	 *
	 * @access  public
	 * @return  string
	 */
	function readable( $string )
	{
		return ucwords( preg_replace( '/[-]+[_]+/', ' ', trim( $string ) ) );
	}
	// ------------------------------------------------------------------------
}
if ( ! function_exists( 'is_countable' ) )
{
	/**
	 * Checks if the given word has a plural version.
	 *
	 * @param    string $string Word to check
	 *
	 * @access  public
	 * @return  bool
	 */
	function is_countable( $string )
	{
		return ! in_array(
			strtolower( $string ),
			array(
				'equipment', 'information', 'rice', 'money',
				'species', 'series', 'fish', 'meta',
			)
		);
	}
}