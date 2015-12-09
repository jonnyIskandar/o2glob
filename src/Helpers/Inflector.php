<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 11/11/2015
 * Time: 5:59 PM
 */

namespace O2System\Glob\Helpers;


class Inflector
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
	public static function singularize( $string )
	{
		$result = strval( $string );

		if ( ! static::is_countable( $result ) )
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

	// ------------------------------------------------------------------------

	/**
	 * Plural
	 *
	 * Takes a singular word and makes it plural
	 *
	 * @param    string $string Input string
	 *
	 * @return    string
	 */
	public static function pluralize( $string )
	{
		$result = strval( $string );

		if ( ! static::is_countable( $result ) )
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

	// ------------------------------------------------------------------------

	/**
	 * Convert a value to studly caps case (StudlyCapCase).
	 *
	 * @param  string $string
	 *
	 * @return string
	 */
	public static function studlycase( $string )
	{
		$string = ucwords( str_replace( array( '-', '_' ), ' ', $string ) );

		return str_replace( ' ', '', $string );
	}

	// ------------------------------------------------------------------------

	/**
	 * Camelize
	 *
	 * Takes multiple words separated by spaces or underscores and camelizes them
	 *
	 * @param    string $string Input string
	 *
	 * @return    string
	 */
	public static function camelize( $string )
	{
		return strtolower( $string[ 0 ] ) . substr( str_replace( ' ', '', ucwords( preg_replace( '/[\s_]+/', ' ', $string ) ) ), 1 );
	}

	// ------------------------------------------------------------------------

	public static function decamelize( $string )
	{
		$string = preg_replace( '/(?<=\\b)(?=[A-Z])/', "_$1", $string );

		return trim( strtolower( $string ), '_' );
	}

	// ------------------------------------------------------------------------

	/**
	 * Underscore
	 *
	 * Takes multiple words separated by spaces and underscores them
	 *
	 * @param    string $string Input string
	 *
	 * @return    string
	 */
	public static function underscore( $string )
	{
		$string = strtolower( trim( $string ) );
		$string = preg_replace( "/[^A-Za-z0-9 ]/", '', $string );

		return preg_replace( '/[ -]+/', '_', $string );
	}

	// ------------------------------------------------------------------------

	/**
	 * Dash
	 *
	 * Takes multiple words separated by spaces and underscores them
	 *
	 * @param    string $string Input string
	 *
	 * @access  public
	 * @return  string
	 */
	public static function dash( $string )
	{
		$string = strtolower( trim( $string ) );
		$string = preg_replace( "/[^A-Za-z0-9 ]/", '', $string );

		return preg_replace( '/[ _]+/', '-', $string );
	}

	// ------------------------------------------------------------------------

	/**
	 * Humanize
	 *
	 * Takes multiple words separated by the separator and changes them to spaces
	 *
	 * @param    string $string    Input string
	 * @param    string $separator Input separator
	 *
	 * @access  public
	 * @return  string
	 */
	public static function humanize( $string, $separator = '_' )
	{
		return ucwords( preg_replace( '/[' . $separator . ']+/', ' ', trim( $string ) ) );
	}

	// ------------------------------------------------------------------------

	/**
	 * Checks if the given word has a plural version.
	 *
	 * @param    string $string Word to check
	 *
	 * @access  public
	 * @return  bool
	 */
	public static function is_countable( $string )
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