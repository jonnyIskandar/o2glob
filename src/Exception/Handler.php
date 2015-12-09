<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 11/12/2015
 * Time: 8:47 AM
 */

namespace O2System\Glob\Exception;


use O2System\Gears\Logger;
use O2System\Gears\Tracer;
use O2System\Glob\Helpers\Inflector;

class Handler
{
	/**
	 * Nesting level of the output buffering mechanism
	 *
	 * @var    int
	 */
	protected static $_ob_level;

	protected static $_view_paths = array();

	protected static $_logger = NULL;

	/**
	 * List of available error levels
	 *
	 * @var    array
	 */
	protected static $_error_levels = array(
		E_ERROR           => 'Error',
		E_WARNING         => 'Warning',
		E_PARSE           => 'Parsing Error',
		E_NOTICE          => 'Notice',
		E_CORE_ERROR      => 'Core Error',
		E_CORE_WARNING    => 'Core Warning',
		E_COMPILE_ERROR   => 'Compile Error',
		E_COMPILE_WARNING => 'Compile Warning',
		E_USER_ERROR      => 'User Error',
		E_USER_WARNING    => 'User Warning',
		E_USER_NOTICE     => 'User Notice',
		E_STRICT          => 'Runtime Notice',
	);

	protected static $_standard_exceptions = array(
		'LogicException'           => array(
			'header'      => 'Logic Exception',
			'description' => 'Exception that represents error in the program logic. This kind of exception should lead directly to a fix in your code.',
		),
		'BadFunctionCallException' => array(
			'header'      => 'Bad Function Call Exception',
			'description' => 'Exception thrown if a callback refers to an undefined function or if some arguments are missing.',
		),
		'BadMethodCallException'   => array(
			'header'      => 'Bad Method Call Exception',
			'description' => 'Exception thrown if a callback refers to an undefined method or if some arguments are missing.',
		),
		'DomainException'          => array(
			'header'      => 'Domain Exception',
			'description' => 'Exception thrown if a value does not adhere to a defined valid data domain.',
		),
		'InvalidArgumentException' => array(
			'header'      => 'Invalid Argument Exception',
			'description' => 'Exception thrown if an argument is not of the expected type.',
		),
		'LengthException'          => array(
			'header'      => 'Length Exception',
			'description' => 'Exception thrown if a length is invalid.',
		),
		'OutOfRangeException'      => array(
			'header'      => 'Out of Range Exception',
			'description' => 'Exception thrown when an illegal index was requested. This represents errors that should be detected at compile time.',
		),
		'RuntimeException'         => array(
			'header'      => 'Runtime Exception',
			'description' => 'Exception thrown if an error which can only be found on runtime occurs.',
		),
		'OutOfBoundsException'     => array(
			'header'      => 'Out of Bounds Exception',
			'description' => 'Exception thrown if a value is not a valid key. This represents errors that cannot be detected at compile time.',
		),
		'OverflowException'        => array(
			'header'      => 'Overflow Exception',
			'description' => 'Exception thrown when adding an element to a full container.',
		),
		'RangeException'           => array(
			'header'      => 'Range Exception',
			'template'    => 'run_time_exception',
			'description' => 'Exception thrown to indicate range errors during program execution. Normally this means there was an arithmetic error other than under/overflow. This is the runtime version of DomainException.',
		),
		'UnderflowException'       => array(
			'header'      => 'Underflow Exception',
			'description' => 'Exception thrown when performing an invalid operation on an empty container, such as removing an element.',
		),
		'UnexpectedValueException' => array(
			'header'      => 'Unexpected Value Exception',
			'description' => 'Exception thrown if a value does not match with a set of values. Typically this happens when a function calls another function and expects the return value to be of a certain type or value not including arithmetic or buffer related errors.',
		),
	);

	protected static $_status_headers = array(
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',

		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',

		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',

		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
	);
	
	public $language = 'en';

	/**
	 * Class constructor
	 *
	 * @access  public
	 */
	public function __construct()
	{
		static::$_ob_level = ob_get_level();
		
		// Set default view paths
		if( empty( self::$_view_paths ) )
		{
			self::$_view_paths = array(
				__DIR__ . '/views/',
			);
		}
	}

	// --------------------------------------------------------------------
	
	public function set_language( $code_iso )
	{
		$this->language = $code_iso;	
	}
	
	public function set_logger_handler( Logger $logger )
	{
		if ( ! isset( static::$_logger ) )
		{
			static::$_logger = $logger;
		}
	}

	public function register_view_paths( $path )
	{
		if ( is_array( $path ) )
		{
			foreach ( $path as $dir )
			{
				$this->register_view_paths( $dir );
			}
		}

		if ( is_string( $path ) )
		{
			if ( is_dir( $path ) )
			{
				static::$_view_paths[] = $path;
			}
		}
	}

	public function register_handler()
	{
		// Register Error Handler
		$this->register_error_handler();

		// Register Exception Handler
		$this->register_exception_handler();

		// Register Shutdown Handler
		$this->register_shutdown_handler();
	}

	public function register_error_handler()
	{
		set_error_handler( '\O2System\Glob\Exception\Handler::error' );
	}

	public function register_exception_handler()
	{
		set_exception_handler( '\O2System\Glob\Exception\Handler::exception' );
	}

	public function register_shutdown_handler()
	{
		register_shutdown_function( '\O2System\Glob\Exception\Handler::shutdown' );
	}

	/**
	 * Set HTTP Status Header
	 *
	 * @param    int    the status code
	 * @param    string
	 *
	 * @return    void
	 */
	public static function set_status_header( $code = 200, $text = '' )
	{
		if ( PHP_SAPI === 'cli' )
		{
			return;
		}

		if ( empty( $code ) OR ! is_numeric( $code ) )
		{
			static::error( 500, 'Status codes must be numeric' );
		}

		if ( empty( $text ) )
		{
			is_int( $code ) OR $code = (int) $code;

			if ( isset( static::$_status_headers[ $code ] ) )
			{
				$text = static::$_status_headers[ $code ];
			}
			elseif ( isset( static::$_error_levels[ $code ] ) )
			{
				$text = static::$_error_levels[ $code ];
			}
			else
			{
				static::error( 500, 'No status text available. Please check your status code number or supply your own message text.' );
			}
		}

		if ( strpos( PHP_SAPI, 'cgi' ) === 0 )
		{
			header( 'Status: ' . $code . ' ' . $text, TRUE );
		}
		else
		{
			$server_protocol = isset( $_SERVER[ 'SERVER_PROTOCOL' ] ) ? $_SERVER[ 'SERVER_PROTOCOL' ] : 'HTTP/1.1';
			@header( $server_protocol . ' ' . $code . ' ' . $text, TRUE, $code );
		}
	}

	/**
	 * Native PHP error handler
	 *
	 * @param   int    $severity Integer error number representative of the PHP error level
	 * @param   string $message  String description of the error
	 * @param   string $filepath File in which the error occurred
	 * @param   int    $line     Line number in the file that the error occurred
	 * @param   mixed  $context  Context of the area, including an array of each variable in scope
	 *
	 * @static
	 * @access  public
	 */
	public static function error( $severity = 500, $message, $filepath = NULL, $line = NULL, $metadata = NULL )
	{
		$is_error = ( ( ( E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR ) & $severity ) === $severity );

		// When an error occurred, set the status header to '500 Internal Server Error'
		// to indicate to the client something went wrong.
		// This can't be done within the $_error->show_php_error method because
		// it is only called when the display_errors flag is set (which isn't usually
		// the case in a production environment) or when errors are ignored because
		// they are above the error_reporting threshold.
		if ( $is_error )
		{
			static::set_status_header( 500 );
		}

		// Should we ignore the error? We'll get the current error_reporting
		// level and add its bits with the severity bits to find out.
		if ( ( $severity & error_reporting() ) !== $severity )
		{
			return;
		}

		$tracer = new Tracer();

		static::_log_error( $severity, $message, $filepath, $line );

		// Should we display the error?
		if ( str_ireplace( array( 'off', 'none', 'no', 'false', 'null' ), '', ini_get( 'display_errors' ) ) )
		{
			if ( is_cli() )
			{
				$severity = isset( static::$_error_levels[ $severity ] ) ? static::$_error_levels[ $severity ] : $severity;
				$message = "\t" . ( is_array( $message ) ? implode( "\n\t", $message ) : $message );
				$filename = 'cli' . DIRECTORY_SEPARATOR . 'error.php';
			}
			else
			{
				static::set_status_header( $severity );
				$severity = isset( static::$_error_levels[ $severity ] ) ? static::$_error_levels[ $severity ] : $severity;
				$message = '<p>' . ( is_array( $message ) ? implode( '</p><p>', $message ) : $message ) . '</p>';
				$filename = 'html' . DIRECTORY_SEPARATOR . 'error.php';
			}

			$filename = static::_find_view( $filename );

			if ( ob_get_level() > static::$_ob_level + 1 )
			{
				ob_end_flush();
			}

			ob_start();
			include( $filename );
			$buffer = ob_get_contents();
			ob_end_clean();

			echo $buffer;
		}

		// If the error is fatal, the execution of the script should be stopped because
		// errors can't be recovered from. Halting the script conforms with PHP's
		// default error handling. See http://www.php.net/manual/en/errorfunc.constants.php
		if ( $is_error )
		{
			exit( 1 ); // EXIT_ERROR
		}
	}

	public static function exception( \Exception $e )
	{
		$severity = get_class( $e );

		if ( isset( static::$_standard_exceptions[ $severity ] ) )
		{
			extract( static::$_standard_exceptions[ $severity ] );
		}

		$severity = str_replace( [ 'O2System', '\\' ], [ '', '_' ], $severity );

		static::_log_error( $severity, $e->getMessage(), $e->getFile(), $e->getLine() );

		$tracer = new Tracer( (array) $e->getTrace() );

		if ( ! isset( $header ) )
		{
			$header = Inflector::humanize( $severity );
		}

		// Should we display the error?
		if ( str_ireplace( array( 'off', 'none', 'no', 'false', 'null' ), '', ini_get( 'display_errors' ) ) )
		{
			if ( is_cli() )
			{
				$message = "\t" . $e->getMessage();

				$filenames = array(
					'cli' . DIRECTORY_SEPARATOR . Inflector::decamelize( $severity ) . '.php',
					'cli' . DIRECTORY_SEPARATOR . 'exception.php',
				);
			}
			else
			{
				static::set_status_header( 500 );
				$message = '<p>' . $e->getMessage() . '</p>';
				$filenames = array(
					'html' . DIRECTORY_SEPARATOR . Inflector::decamelize( $severity ) . '.php',
					'html' . DIRECTORY_SEPARATOR . 'exception.php',
				);
			}

			$filename = static::_find_view( $filenames );

			if ( ob_get_level() > static::$_ob_level + 1 )
			{
				ob_end_flush();
			}

			ob_start();
			include( $filename );
			$buffer = ob_get_contents();
			ob_end_clean();

			echo $buffer;
		}

		exit( 1 ); // EXIT_ERROR
	}

	public static function shutdown()
	{
		$throw_error = FALSE;

		if ( $error = error_get_last() )
		{
			switch ( $error[ 'type' ] )
			{
				case E_ERROR:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					$throw_error = TRUE;
					break;
			}
		}

		if ( $throw_error === TRUE )
		{
			static::error( $error[ 'type' ], $error[ 'message' ], $error[ 'file' ], $error[ 'line' ] );
		}
	}

	/**
	 * Exception Logger
	 *
	 * Logs PHP generated error messages
	 *
	 * @param    int    $severity Log level
	 * @param    string $message  Error message
	 * @param    string $filepath File path
	 * @param    int    $line     Line number
	 *
	 * @return    void
	 */
	protected static function _log_error( $severity, $message, $filepath, $line )
	{
		if ( isset( static::$_logger ) )
		{
			$severity = isset( static::$_error_levels[ $severity ] ) ? static::$_error_levels[ $severity ] : $severity;
			static::$_logger->error( 'Severity: ' . Inflector::humanize( $severity ) . ' --> ' . $message . ' ' . $filepath . ' ' . $line );
		}
	}

	// ---------------------------------------------------------------x-----

	protected static function _find_view( $filenames )
	{
		foreach ( array_reverse( static::$_view_paths ) as $view_path )
		{
			if ( is_string( $filenames ) )
			{
				if ( file_exists( $view_path . $filenames ) )
				{
					return $view_path . $filenames;
				}
			}
			elseif ( is_array( $filenames ) )
			{
				foreach ( $filenames as $filename )
				{
					if ( file_exists( $view_path . $filename ) )
					{
						return $view_path . $filename;
					}
				}
			}
		}
	}
}