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

namespace O2System\Glob\Factory;

// ------------------------------------------------------------------------

use O2System\Cache;

/**
 * System Registry
 *
 * @package       O2System
 * @subpackage    system/core
 * @category      Core Class
 * @author        Steeven Andrian Salim
 * @link          http://o2system.center/framework/user-guide/core/router.html
 */
class Registry extends \ArrayObject
{
	private static $cacheHandler = NULL;
	
	public function __construct( array $data = array() )
	{
		parent::__construct( $data, \ArrayObject::ARRAY_AS_PROPS );
	}
	
	public function setCacheHandler( Cache $cache )
	{
		self::$cacheHandler = $cache;
	}
	
	protected function getCache( $key )
	{
		if ( self::$cacheHandler instanceof Cache )
		{
			if ( $cache = self::$cacheHandler->get( $key ) )
			{
				parent::__construct( $cache, \ArrayObject::ARRAY_AS_PROPS );
				
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	protected function saveCache( $key )
	{
		if ( self::$cacheHandler instanceof Cache )
		{
			$cache = $this->getArrayCopy();
			
			if ( ! empty( $cache ) )
			{
				return self::$cacheHandler->save( $key, $cache, FALSE );
			}
		}
		
		return FALSE;
	}

	public function deleteCache( $key )
	{
		if ( self::$cacheHandler instanceof Cache )
		{
			return self::$cacheHandler->delete( $key );
		}
		
		return FALSE;
	}

	public function __call( $method, $args )
	{
		if ( method_exists( $this, $method ) )
		{
			return call_user_func_array( array( $this, $method ), $args );
		}
		elseif ( empty( $args ) )
		{
			return $this->offsetGet( $method );
		}
		elseif ( $this->offsetExists( $method ) )
		{
			// Let's get the registry values
			$registry = $this->offsetGet( $method );
			
			// List arguments
			@list( $index, $action ) = $args;

			if ( isset( $registry->{$index} ) )
			{
				$value = $registry->{$index};
			}
			elseif ( isset( $registry[ $index ] ) )
			{
				$value = $registry[ $index ];
			}
			
			if ( isset( $action ) )
			{
				if ( is_callable( $action ) )
				{
					return $action( $value );
				}
				elseif ( function_exists( $action ) )
				{
					$value = is_object( $value ) ? get_object_vars( $value ) : $value;

					return array_map( $action, $value );
				}
				elseif ( in_array( $action, [ 'array', 'object', 'keys', 'values' ] ) )
				{
					switch ( $action )
					{
						default:
						case 'array':
							$value = ( is_array( $value ) ? $value : (array) $value );
							break;
						case 'object':
							$value = ( is_object( $value ) ? $value : (object) $value );
							break;
						case 'keys':
							$value = is_object( $value ) ? get_object_vars( $value ) : $value;
							$value = array_keys( $value );
							break;
						case 'values':
							$value = is_object( $value ) ? get_object_vars( $value ) : $value;
							$value = array_values( $value );
							break;
					}
					
					if ( isset( $args[ 2 ] ) )
					{
						if ( is_callable( $args[ 2 ] ) )
						{
							return $args[ 2 ]( $value );
						}
						elseif ( function_exists( $args[ 2 ] ) )
						{
							return array_map( $args[ 2 ], $value );
						}
					}
					else
					{
						return $value;
					}
				}
				elseif ( in_array( $action, [ 'json', 'serialize', 'flatten', 'flatten_keys', 'flatten_values' ] ) )
				{
					switch ( $action )
					{
						default:
						case 'json':
							return json_encode( $value );
							break;
						case 'serialize':
							return serialize( $value );
							break;
						case 'flatten':
							$value = is_object( $value ) ? get_object_vars( $value ) : $value;
							$glue = isset( $args[ 2 ] ) ? $args[ 2 ] : ', ';
							
							foreach ( $value as $key => $val )
							{
								if ( is_bool( $val ) )
								{
									$val = $val === TRUE ? 'true' : 'false';
								}
								
								if ( is_numeric( $key ) )
								{
									$result[] = $val;
								}
								elseif ( is_string( $key ) )
								{
									if ( is_array( $val ) )
									{
										$val = implode( $glue, $val );
									}
									
									$result[] = $key . ' : ' . $val;
								}
							}
							
							return implode( $glue, $result );
							
							break;
						case 'flatten_keys':
							$value = is_object( $value ) ? get_object_vars( $value ) : $value;
							$glue = isset( $args[ 2 ] ) ? $args[ 2 ] : ', ';
							
							return implode( $glue, array_keys( $value ) );
							break;
						case 'flatten_values':
							$value = is_object( $value ) ? get_object_vars( $value ) : $value;
							$glue = isset( $args[ 2 ] ) ? $args[ 2 ] : ', ';
							
							foreach ( array_values( $value ) as $val )
							{
								if ( is_bool( $val ) )
								{
									$val = $val === TRUE ? 'true' : 'false';
								}
								
								$result[] = $val;
							}
							
							return implode( $glue, $result );
							break;
					}
				}
			}
			elseif ( isset( $value ) )
			{
				return $value;
			}
			else
			{
				return $registry;
			}
		}
	}

	public function __toString()
	{
		return json_encode( $this->getArrayCopy() );
	}

	public function __toArray()
	{
		return $this->getArrayCopy();
	}
}