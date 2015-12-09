<?php

namespace O2System;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'Common.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'Convension.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'Inflector.php';

class Glob
{
	public static function load_language( $file, $lang = 'en' )
	{
		if ( file_exists( __DIR__ . '/Languages/' . $lang . '/' . $file . '.ini' ) )
		{
			return parse_ini_file( __DIR__ . '/Languages/' . $lang . '/' . $file . '.ini' );
		}

		return array();
	}
}