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

namespace O2System\Glob;

// ------------------------------------------------------------------------

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
    protected $_name = NULL;

    public function __construct( array $data = array(), $registry_name = NULL )
    {
        parent::__construct( $data, \ArrayObject::ARRAY_AS_PROPS );

        if( isset( $registry_name ) )
        {
            $this->set_name( $registry_name );
        }
    }

    public function set_name( $name )
    {
        $this->_name = str_replace( [ '\\', '-', '_', '/' ], '.', strtolower( $name ) );
    }


    public function __get( $offset )
    {
        if( $offset === 'storage' )
        {
            return $this->getArrayCopy();
        }
        elseif( $this->offsetExists( $offset ) )
        {
            return $this->offsetGet( $offset );
        }
    }

    public function __call( $method, $args )
    {
        if( empty( $args ) )
        {
            return $this->__get( $method );
        }
        elseif( method_exists( $this, $method ) )
        {
            return call_user_func_array( array( $this, $method ), $args );
        }
        else
        {
            $entry = $this->__get( $method );

            if( $this->offsetExists( $args[ 0 ] ) )
            {
                $data = $entry->__get( $args[ 0 ] );
                $action = isset( $args[ 1 ] ) ? $args[ 1 ] : FALSE;
                $params = isset( $args[ 2 ] ) ? $args[ 2 ] : array();
            }
            else
            {
                $data = $entry;
                $action = isset( $args[ 0 ] ) ? $args[ 0 ] : FALSE;
                $params = isset( $args[ 1 ] ) ? $args[ 1 ] : array();
            }

            if( in_array( $action, array( 'array', 'object', 'keys' ) ) )
            {
                switch( $action )
                {
                    default:
                    case 'object';
                        if( is_array( $data ) )
                        {
                            $object = new \stdClass();

                            foreach( $data as $key => $value )
                            {
                                $object->{str_replace( '.', '_', $key )} = $value;
                            }

                            return $object;
                        }

                        return $data;
                        break;
                    case 'array';
                        return (array)$data;
                        break;
                    case 'keys';
                        return array_keys( (array)$data );
                        break;
                }
            }
            elseif( in_array( strtolower( $action ), array( 'json', 'serialize' ) ) )
            {
                switch( $action )
                {
                    default:
                    case 'json':
                        return json_encode( $data );
                        break;
                    case 'serialize';
                        if( method_exists( $data, 'serialize' ) )
                        {
                            return $data->serialize();
                        }
                        else
                        {
                            return serialize( $data );
                        }
                        break;
                }
            }
            elseif( method_exists( $data, $action ) )
            {
                return call_user_func_array( $data, $params );
            }
            elseif( $action !== FALSE )
            {
                if( isset( $data->{$action} ) )
                {
                    return $data->{$action};
                }
                elseif( isset( $data[ $action ] ) )
                {
                    return $data[ $action ];
                }
            }
            elseif( $action === FALSE )
            {
                return $data;
            }
        }
    }

    public function __toString()
    {
        return $this->serialize();
    }

    public function __toArray()
    {
        return $this->getArrayCopy();
    }
}