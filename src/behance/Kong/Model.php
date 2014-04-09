<?php namespace behance\Kong;

use \behance\Kong\Api;
use \behance\Kong\Exception\PropertyNotFoundException;

class Model {

  protected $_data  = [];
  protected $_dirty = [];

  /**
   * HTTP object for cURL requests.
   *
   * @var Guzzle\Http\Client
   */
  protected $_api;

  /**
   * @param behance\Kong\Api $api
   * @param array $data data to hydrate the model with
   */
  public function __construct( Api $api, array $data = [] ) {

    $this->setApi( $api );
    $this->setData( $data );

  } // __construct

  public function __get( $property ) {

    if ( isset( $this->_dirty[ $property ] ) ) {
      return $this->_dirty[ $property ];
    }

    if ( isset( $this->_data[ $property ] ) ) {
      return $this->_data[ $property ];
    }

    throw new PropertyNotFoundException( 'Could not find property ' . $property . ' in class ' . get_class( $this ) );

  } // __get

  public function __set( $property, $value ) {

    if ( !isset( $this->_data[ $property ] ) ) {
      throw new PropertyNotFoundException( 'Can not set non-existent property ' . $property . ' in class ' . get_class( $this ) );
    }

    $this->_dirty[ $property ] = $value;

  } // __set

  public function setData( array $data = [] ) {

    $this->_data = $data;

  } // setData

  public function setApi( Api $api ) {

    $this->_api  = $api;

  } // setApi

  public function toArray() {

    return array_merge( $this->_data, $this->_dirty );

  } // toArray

  protected function _getApi() {

    return $this->_api;

  } // _getApi

} // Model
