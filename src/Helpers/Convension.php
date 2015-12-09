<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 11/12/2015
 * Time: 5:27 PM
 */

namespace O2System\Glob\Helpers;


class Convension
{
	/**
	 * Return the namespace from class
	 *
	 * @param   string $path Class name
	 *
	 * @return  string  namespace class
	 */
	public static function get_namespace( $class )
	{
		$class = is_object( $class ) ? get_class( $class ) : self::to_classname( $class );

		$x_class = explode( '\\', $class );
		$x_class = array_slice( $x_class, 0, count( $x_class ) - 1 );

		return implode( '\\', $x_class ) . '\\';
	}

	/**
	 * Return the class name of namespace class
	 *
	 * @param   string $path Class name
	 *
	 * @return  string  namespace class
	 */
	public static function get_classname( $class )
	{
		$class = is_object( $class ) ? get_class( $class ) : self::to_classname( $class );
		$x_class = explode( '\\', $class );

		return end( $x_class );
	}

	/**
	 * Returns Fixed Class Name according to O2System Class Name standards
	 *
	 * @param    $class    String class name
	 *
	 * @return   mixed
	 */
	public static function to_classname( $class )
	{
		$class = str_replace( [ '/', DIRECTORY_SEPARATOR, '.php' ], [ '\\', '\\', '' ], $class );
		$class = trim( $class );

		$segments = explode( '\\', $class );

		foreach ( $segments as $segment )
		{
			$patterns = array(
				'/[\s]+/',
				'/[-]+/',
				'/[_]+/',
			);

			$segment = preg_replace( $patterns, '_', $segment );

			if ( strpos( $segment, '_' ) !== FALSE )
			{
				$x_segment = array_map( 'ucfirst', explode( '_', $segment ) );
				$x_class[] = implode( '_', $x_segment );
			}
			else
			{
				$x_class[] = ucfirst( $segment );
			}
		}

		return implode( '\\', $x_class );
	}

	/**
	 * Returns Fixed Class Name according to O2System Class Name standards
	 *
	 * @param    $class    String class name
	 *
	 * @return   mixed
	 */
	public static function to_filename( $filename )
	{
		$filename = str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $filename );
		$filename = trim( $filename );

		$segments = explode( DIRECTORY_SEPARATOR, $filename );

		foreach ( $segments as $segment )
		{
			$patterns = array(
				'/[\s]+/',
				'/[-]+/',
				'/[_]+/',
			);

			$segment = preg_replace( $patterns, '_', $segment );

			if ( strpos( $segment, '_' ) !== FALSE )
			{
				$x_segment = array_map( 'ucfirst', explode( '_', $segment ) );
				$x_class[] = implode( '_', $x_segment );
			}
			else
			{
				$x_class[] = ucfirst( $segment );
			}
		}

		return implode( DIRECTORY_SEPARATOR, $x_class );
	}

	/**
	 * Return a valid namespace class
	 *
	 * @param    string $class class name with namespace
	 *
	 * @return   string     valid string namespace
	 */
	public static function to_namespace( $class, $get_namespace = TRUE )
	{
		return ( $get_namespace === TRUE ? self::get_namespace( $class ) : self::to_classname( $class ) );
	}
}