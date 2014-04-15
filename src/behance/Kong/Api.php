<?php namespace behance\Kong;

use \behance\Kong;
use \Guzzle\Http\Client as HttpClient;

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

  public function __construct( HttpClient $client = null ) {

    $this->_setHttpClient( $client );

  } // __construct

  /**
   * Execute a call to the API.
   *
   * @param array  $params parameters to build a query string with
   * @param string $endpoint
   * @param string $method which HTTP method to call
   *
   * @return \Guzzle\Http\Message\Response;
   */
  public function execute( array $params, $endpoint, $uri, $version, $method = 'GET' ) {

    $options  = [ 'exceptions' => false ];
    $body     = null;
    $headers  = null;
    $endpoint = $this->_constructEndpoint( $endpoint, $uri, $version );
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
  protected function _setHttpClient( HttpClient $client = null ) {

    $this->_http = $client;

  } // _setHttpClient

  /**
   * Retrieve/lazily-create an http client.
   *
   * @return \Guzzle\Http\Client
   */
  protected function _getHttpClient() {

    if ( !$this->_client ) {
      $this->_setHttpClient( new HttpClient );
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

    // This helps make the distinction between MailChimp and Mandrill
    // API calls. MailChimp uses _data_center while Mandrill doesn't.
    // Unfortunately, they use different array keys to describe the
    // API key during calls.
    $api_key_index = ( is_null( $this->_data_center ) )
                     ? 'key'
                     : 'apikey';

    return array_merge( [ $api_key_index => $this->_key ], $params );

  } // _constructQuery

  /**
   * Construct and endpoint to hit on the API.
   *
   * @param string $endpoint a constant from \behance\Kong\Endpoints
   *
   * @return string
   */
  protected function _constructEndpoint( $endpoint, $uri, $version ) {

    return Endpoints::build( $endpoint, $uri, $version, $this->_data_center );

  } // _constructEndpoint

  /**
   * Set the data center based on the current api key.
   */
  protected function _setDataCenter() {

    $parts = explode( '-', $this->_key );

    if ( count( $parts ) > 1 ) {
      $this->_data_center = array_pop( $parts );
    }

  } // _setDataCenter

} // Api
