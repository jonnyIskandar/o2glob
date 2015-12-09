<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 12/3/2015
 * Time: 2:39 PM
 */

namespace O2System\Glob\Helpers;


class Sanitize
{
	public static function email( $string )
	{
		$string = preg_replace( '((?:\n|\r|\t|%0A|%0D|%08|%09)+)i', '', $string );

		return (string) filter_var( $string, FILTER_SANITIZE_EMAIL );
	}

	public static function url( $string )
	{
		return (string) filter_var( $string, FILTER_SANITIZE_URL );
	}

	public static function numeric( $string )
	{
		return (int) filter_var( $string, FILTER_SANITIZE_NUMBER_INT );
	}

	protected function string( $string )
	{
		return (string) filter_var( $string, FILTER_SANITIZE_STRING );
	}
}