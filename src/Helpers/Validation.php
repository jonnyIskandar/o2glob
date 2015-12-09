<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 12/3/2015
 * Time: 1:59 PM
 */

namespace O2System\Glob\Helpers;


use O2System\Glob;

class Validation
{
	/**
	 * Validation Rules
	 *
	 * @access  protected
	 * @type    array
	 */
	protected $_rules = array();

	/**
	 * Validation Errors
	 *
	 * @access  protected
	 * @type    array
	 */
	protected $_errors = array();

	/**
	 * Validation Messages
	 *
	 * @access  protected
	 * @type    array
	 */
	protected $_messages = array();

	/**
	 * Source Variables
	 *
	 * @access  protected
	 * @type    array
	 */
	protected $_source_vars = array();

	/**
	 * Class Constructor
	 *
	 * @param array $source_vars
	 */
	public function __construct( array $source_vars = array(), $language = 'en' )
	{
		if ( ! empty( $source_vars ) )
		{
			$this->_source_vars = $source_vars;
		}

		$this->_messages = Glob::load_language( 'validation', $language );
	}

	/**
	 * Set Source
	 *
	 * @param array $source_vars
	 *
	 * @access  public
	 */
	public function set_source( array $source_vars )
	{
		$this->_source_vars = $source_vars;
	}

	// ------------------------------------------------------------------------


	public function set_rule( $field, $label, $rules, $messages = array() )
	{
		$this->_rules[ $field ] = array(
			'field'    => $field,
			'label'    => $label,
			'rules'    => $rules,
			'messages' => $messages,
		);
	}

	public function set_rules( array $rules )
	{
		foreach ( $rules as $rule )
		{
			$this->set_rule( $rule[ 'field' ], $rule[ 'label' ], $rule[ 'rules' ], $rule[ 'messages' ] );
		}
	}

	public function set_message( $field, $message )
	{
		$this->_messages[ $field ] = $message;
	}

	public function validate()
	{
		if ( ! empty( $this->_source_vars ) )
		{
			foreach ( $this->_rules as $field => $rule )
			{
				if ( isset( $this->_source_vars[ $field ] ) )
				{
					if ( is_callable( $rule[ 'rules' ] ) )
					{
						if ( $rule[ 'rules' ]( $this->_source_vars[ $field ] ) )
						{
							if ( ! empty( $rule[ 'messages' ] ) )
							{
								$this->_errors[] = sprintf( $rule[ 'messages' ], $rule[ 'label' ] );
							}
							elseif ( array_key_exists( $field, $this->_messages ) )
							{
								$this->_errors[] = sprintf( $this->_messages[ $field ], $rule[ 'label' ] );
							}
						}
					}
					elseif ( is_string( $rule[ 'rules' ] ) )
					{
						$methods = explode( '|', $rule[ 'rules' ] );

						foreach ( $methods as $method )
						{
							$args = array( trim( $this->_source_vars[ $field ] ) );

							// Is the field name an array? If it is an array, we break it apart
							// into its components so that we can fetch the corresponding POST data later
							if ( preg_match_all( '/\[(.*?)\]/', $method, $matches ) )
							{
								$method = str_replace( $matches[ 0 ], '', $method );
								$args = array_merge( $args, $matches[ 1 ] );
							}

							$method = 'is_' . $method;

							if ( method_exists( __CLASS__, $method ) )
							{
								$call_args = $args;

								if ( $method === 'is_matches' )
								{
									if ( isset( $this->_source_vars[ $args[ 1 ] ] ) )
									{
										$call_args[ 1 ] = $this->_source_vars[ $args[ 1 ] ];
									}
								}

								if ( call_user_func_array( 'self::' . $method, $call_args ) === FALSE )
								{
									$sprintf = array();

									if ( ! empty( $rule[ 'messages' ] ) )
									{
										$sprintf[] = $rule[ 'messages' ];
									}
									elseif ( array_key_exists( $field, $this->_messages ) )
									{
										$sprintf[] = $this->_messages[ $field ];
									}
									elseif ( array_key_exists( strtoupper( $method ), $this->_messages ) )
									{
										$sprintf[] = $this->_messages[ strtoupper( $method ) ];
									}

									$sprintf[] = $rule[ 'label' ];

									array_shift( $args );

									$this->_errors[] = call_user_func_array( 'sprintf', array_merge( $sprintf, $args ) );

									break;
								}
							}
						}
					}
				}
			}
		}

		return empty( $this->_errors ) ? TRUE : FALSE;
	}

	public function get_errors()
	{
		return $this->_errors;
	}

	public static function is_required( $string )
	{
		if ( empty( $string ) OR strlen( $string ) == 0 )
		{
			return FALSE;
		}

		return TRUE;
	}

	public static function is_matches( $string, $match )
	{
		if ( $string === $match )
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Performs a Regular Expression match test.
	 *
	 * @param    string
	 * @param    string    regex
	 *
	 * @return    bool
	 */
	public static function is_regex_match( $string, $regex )
	{
		return (bool) preg_match( $regex, $string );
	}

	// --------------------------------------------------------------------


	public static function is_float( $string )
	{
		return filter_var( $string, FILTER_VALIDATE_FLOAT );
	}

	/**
	 * Minimum Length
	 *
	 * @param    string
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_min_length( $string, $length )
	{
		if ( ! is_numeric( $length ) )
		{
			return FALSE;
		}

		return ( $length <= mb_strlen( $string ) );
	}

	// --------------------------------------------------------------------

	/**
	 * Max Length
	 *
	 * @param    string
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_max_length( $string, $length )
	{
		if ( ! is_numeric( $length ) )
		{
			return FALSE;
		}

		return ( $length >= mb_strlen( $string ) );
	}

	// --------------------------------------------------------------------

	/**
	 * Exact Length
	 *
	 * @param    string
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_exact_length( $string, $length )
	{
		if ( ! is_numeric( $length ) )
		{
			return FALSE;
		}

		return ( mb_strlen( $string ) === (int) $length );
	}

	public static function is_dimension( $string, $format = 'W x H x L' )
	{
		$string = strtolower( $string );
		$string = preg_replace( '/\s+/', '', $string );
		$x_string = explode( 'x', $string );

		$format = strtolower( $format );
		$format = preg_replace( '/\s+/', '', $format );
		$x_format = explode( 'x', $format );

		if ( count( $x_string ) == count( $x_format ) )
		{
			return TRUE;
		}

		return FALSE;
	}

	public static function is_ipv4( $string )
	{
		return filter_var( $string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
	}

	public static function is_ipv6( $string )
	{
		return filter_var( $string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 );
	}


	public static function is_url( $string )
	{
		if ( preg_match( '/^(?:([^:]*)\:)?\/\/(.+)$/', $string, $matches ) )
		{
			if ( empty( $matches[ 2 ] ) )
			{
				return FALSE;
			}
			elseif ( ! in_array( $matches[ 1 ], array( 'http', 'https' ), TRUE ) )
			{
				return FALSE;
			}

			$string = $matches[ 2 ];
		}

		$string = 'http://' . $string;

		return filter_var( $string, FILTER_VALIDATE_URL );
	}

	public static function is_email( $string )
	{
		if ( function_exists( 'idn_to_ascii' ) && $strpos = strpos( $string, '@' ) )
		{
			$string = substr( $string, 0, ++$strpos ) . idn_to_ascii( substr( $string, $strpos ) );
		}

		return (bool) filter_var( $string, FILTER_VALIDATE_EMAIL );
	}

	public static function is_domain( $string )
	{
		return ( preg_match( "/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $string ) //valid chars check
			&& preg_match( "/^.{1,253}$/", $string ) //overall length check
			&& preg_match( "/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $string ) ); //length of each label
	}

	public static function is_bool( $string )
	{
		return filter_var( $string, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Alpha
	 *
	 * @param    string $string
	 *
	 * @return    bool
	 */
	public static function is_alpha( $string )
	{
		return ctype_alpha( $string );
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric
	 *
	 * @param    string $string
	 *
	 * @return    bool
	 */
	public static function is_alpha_numeric( $string )
	{
		return ctype_alnum( (string) $string );
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric w/ spaces
	 *
	 * @param    string $string
	 *
	 * @return    bool
	 */
	public static function is_alpha_numeric_spaces( $string )
	{
		return (bool) preg_match( '/^[A-Z0-9 ]+$/i', $string );
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_alpha_dash( $string )
	{
		return (bool) preg_match( '/^[a-z0-9-]+$/i', $string );
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_alpha_underscore( $string )
	{
		return (bool) preg_match( '/^[a-z0-9_]+$/i', $string );
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_alpha_underscore_dash( $string )
	{
		return (bool) preg_match( '/^[a-z0-9_-]+$/i', $string );
	}

	// --------------------------------------------------------------------

	/**
	 * Numeric
	 *
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_numeric( $str )
	{
		return (bool) preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str );

	}

	// --------------------------------------------------------------------

	/**
	 * Integer
	 *
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_integer( $str )
	{
		return (bool) preg_match( '/^[\-+]?[0-9]+$/', $str );
	}

	// --------------------------------------------------------------------

	/**
	 * Decimal number
	 *
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_decimal( $string )
	{
		return (bool) preg_match( '/^[\-+]?[0-9]+\.[0-9]+$/', $string );
	}

	// --------------------------------------------------------------------

	/**
	 * Greater than
	 *
	 * @param    string
	 * @param    int
	 *
	 * @return    bool
	 */
	public static function is_greater( $string, $min )
	{
		return is_numeric( $string ) ? ( $string > $min ) : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Equal to or Greater than
	 *
	 * @param    string
	 * @param    int
	 *
	 * @return    bool
	 */
	public static function is_greater_equal( $string, $min )
	{
		return is_numeric( $string ) ? ( $string >= $min ) : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Less than
	 *
	 * @param    string
	 * @param    int
	 *
	 * @return    bool
	 */
	public static function is_less( $string, $max )
	{
		return is_numeric( $string ) ? ( $string < $max ) : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Equal to or Less than
	 *
	 * @param    string
	 * @param    int
	 *
	 * @return    bool
	 */
	public static function is_less_equal( $string, $max )
	{
		return is_numeric( $string ) ? ( $string <= $max ) : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Value should be within an array of values
	 *
	 * @param    string
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_listed( $string, $list )
	{
		if ( is_string( $list ) )
		{
			$list = explode( ',', $list );
			$list = array_map( 'trim', $list );
		}

		return in_array( $string, $list, TRUE );
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_natural( $string )
	{
		return ctype_digit( (string) $string );
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 *
	 * @param    string
	 *
	 * @return    bool
	 */
	public static function is_natural_no_zero( $string )
	{
		return ( $string != 0 && ctype_digit( (string) $string ) );
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Base64
	 *
	 * Tests a string for characters outside of the Base64 alphabet
	 * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
	 *
	 * @param    string
	 *
	 * @return    bool
	 */
	public function is_base64( $string )
	{
		return (bool) ( base64_encode( base64_decode( $string ) ) === $string );
	}

	// --------------------------------------------------------------------

	public static function is_md5( $string )
	{
		return preg_match( '/^[a-f0-9]{32}$/i', $string );
	}

	public static function is_msisdn( $string, $leading = '62' )
	{
		return (bool) preg_match( '/^(' . $leading . '[1-9]{1}[0-9]{1,2})[0-9]{6,8}$/', $string );
	}

	public static function is_date( $string, $format = 'Y-m-d' )
	{
		$date_time = \DateTime::createFromFormat( $format, $string );

		return (bool) $date_time !== FALSE && ! array_sum( $date_time->getLastErrors() );
	}

	public static function is_password( $string, $length = 8, $format = 'uppercase, lowercase, number, special' )
	{
		// Length
		if ( self::is_min_length( $string, $length ) === FALSE )
		{
			return FALSE;
		}

		$format = strtolower( $format );
		$format = explode( ',', $format );
		$format = array_map( 'trim', $format );

		foreach ( $format as $type )
		{
			switch ( $type )
			{
				case 'uppercase':
					if ( preg_match_all( '/[A-Z]/', $string, $uppercase ) )
					{
						$valid[ $type ] = count( $uppercase[ 0 ] );
					}
					break;
				case 'lowercase':
					if ( preg_match_all( '/[a-z]/', $string, $lowercase ) )
					{
						$valid[ $type ] = count( $lowercase[ 0 ] );
					}
					break;
				case 'number':
				case 'numbers':
					if ( preg_match_all( '/[0-9]/', $string, $numbers ) )
					{
						$valid[ $type ] = count( $numbers[ 0 ] );
					}
					break;
				case 'special character':
				case 'special-character':
				case 'special':
					// Special Characters
					if ( preg_match_all( '/[!@#$%^&*()\-_=+{};:,<.>]/', $string, $special ) )
					{
						$valid[ $type ] = count( $special[ 0 ] );
					}
					break;
			}
		}

		$diff = array_diff( $format, array_keys( $valid ) );

		return empty( $diff ) ? TRUE : FALSE;
	}
}