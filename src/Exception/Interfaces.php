<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 11/12/2015
 * Time: 8:45 AM
 */

namespace O2System\Glob\Exception;


class Interfaces extends \Exception
{
	/**
	 *
	 *
	 * @access  protected
	 * @type
	 */
	protected static $_handler;

	/**
	 * Class Constructor
	 *
	 * @param string $message
	 * @param int    $code
	 *
	 * @access  public
	 */
	public function __construct( $message, $code = 0 )
	{
		parent::__construct($message, $code);
		
		if(! isset(static::$_handler) )
		{
			if ( class_exists( 'O2System' ) )
			{
				static::$_handler = \O2System::Exception();
			}
			else
			{
				static::$_handler = new Handler();
			}
		}
	}

	/**
	 * Magic function __call
	 *
	 * @param       $method
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function __call( $method, $args = array() )
	{
		if(method_exists(static::$_handler, $method))
		{
			return call_user_func_array( [ static::$_handler, $method ], $args );
		}
	}
}