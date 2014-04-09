<?php namespace behance\Kong;

use \behance\Kong;
use \Guzzle\Http\Client;

class Api {

  /**
   * HTTP object for cURL requests.
   *
   * @var Guzzle\Http\Client
   */
  protected $_http;

  /**
   * The MailChimp API key.
   *
   * @var string
   */
  protected $_key;

  /**
   * The MailChimp data center to access.
   *
   * @var string
   */
  protected $_data_center;

  /**
   * The HTTP client.
   *
   * @var Guzzle\Http\Client description
   */
  protected $_client;

  public function __construct( Client $client = null ) {

    $this->_setHttpClient( $client );

  } // __construct

  /**
   * Execute a call to the API.
   *
   * @param array  $params parameters to build a query string with
   * @param string $endpoint
   * @param string $method which HTTP method to call
   *
   * @return array an array of class $model
   */
  public function execute( $params, $endpoint, $method = 'GET' ) {

    $options  = [];
    $body     = null;
    $headers  = null;
    $endpoint = 'https://' . $this->_constructEndpoint( $endpoint );
    $query    = $this->_constructQuery( $params );

    $index = ( $method === 'GET' )
             ? 'query'
             : 'body';

    $options[ $index ] = $query;

    if ( $method !== 'GET' ) {
      $body = $query;
    }

    $request = $this->_getHttpClient()->createRequest( $method, $endpoint, $headers, $body, $options );

    return $request->send();

  } // execute

  /**
   * Set the API key and update the data center.
   *
   * @param string $key
   */
  public function setApiKey( $key = null ) {

    if ( is_null( $key ) ) {
      return;
    }

    $this->_key = $key;
    $this->_setDataCenter();

  } // setApiKey

  public function getApiKey() {

    return $this->_key;

  } // getApiKey

  public function getDataCenter() {

    return $this->_data_center;

  } // getDataCenter

  /**
   * Set the http client.
   *
   * @param \Guzzle\Http\Client $client
   */
  protected function _setHttpClient( Client $client = null ) {

    $this->_http = $client;

  } // _setHttpClient

  /**
   * Retrieve/lazily-create an http client.
   *
   * @return \Guzzle\Http\Client
   */
  protected function _getHttpClient() {

    if ( !$this->_client ) {
      $this->_setHttpClient( new Client );
    }

    return $this->_http;

  } // _getHttpClient


  /**
   * Build a query string to use in api requests.
   *
   * @param array $params
   *
   * @return string
   */
  protected function _constructQuery( array $params = [] ) {

    return array_merge( [ 'apikey' => $this->_key ], $params );

  } // _constructQuery

  /**
   * Construct and endpoint to hit on the API.
   *
   * @param string $endpoint a constant from \behance\Kong\Endpoints
   *
   * @return string
   */
  protected function _constructEndpoint( $endpoint ) {

    return Endpoints::build( $endpoint, $this->_data_center );

  } // _constructEndpoint

  /**
   * Set the data center based on the current api key.
   */
  protected function _setDataCenter() {

    $parts = explode( '-', $this->_key );

    $this->_data_center = array_pop( $parts );

  } // _setDataCenter

} // Api
