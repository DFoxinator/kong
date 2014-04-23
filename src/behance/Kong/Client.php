<?php namespace Behance\Kong;

use \Behance\Kong\Api;
use \Behance\Kong\Endpoints;
use \Behance\Kong\Model;
use \Guzzle\Http\Message\Response;

class Client {

  /**
   * API object for abstracting requests.
   *
   * @var Behance\Kong\Api
   */
  protected $_api;

  /**
   * @param string $key The API key to use.
   */
  public function __construct( $key = null ) {

    $this->setApiKey( $key );

  } // __construct

  /**
   * Get the API object.
   *
   * @return \Behance\Kong\Api
   */
  public function getApi() {

    if ( is_null( $this->_api ) ) {
      $this->setApi( new Api( new \Guzzle\Http\Client ) );
    }

    return $this->_api;

  } // getApi

  /**
   * Set the API object.
   *
   * @param \Behance\Kong\Api $api
   */
  public function setApi( Api $api ) {

    $this->_api = $api;

  } // setApi

  /**
   * Set the API key on the API object.
   *
   * @param string $key
   */
  public function setApiKey( $key = null ) {

    if ( is_null( $key ) ) {
      return;
    }

    $this->getApi()->setApiKey( $key );

  } // setApiKey

  /**
   * Execute an api call.
   *
   * @param array $params
   * @param string $endpoint
   * @param string $method http method to use
   *
   * @return Guzzle\Http\Response
   */
  protected function _execute( array $params, $endpoint, $method = 'GET' ) {

    return $this->getApi()->execute( $params, $endpoint, static::API_URI, static::API_VERSION, $method );

  } // _execute

  /**
   * Format the raw API response into an array of Models.
   *
   * @param Guzzle\Http\Message\Response $response api response
   * @param string $model the name of the class to instantiate
   *
   * @return array
   */
  protected function _formatResponse( Response $response, $model ) {

    $body = $response->json();

    $collection = [];
    $client = Model::getClientString( $model );
    $model  = "\Behance\Kong\Model\\$client\\$model";

    foreach ( $body['data'] as $item ) {
      $collection[] = new $model( $this->getApi(), $item );
    } // foreach body[data]

    return $collection;

  } // _formatResponse

  /**
   * Get an empty instance of the supplied model.
   *
   * @param string $name
   *
   * @return mixed
   */
  protected function _getEmptyModel( $name ) {

    $client = Model::getClientString( $name );

    $model = "\Behance\Kong\Model\\$client\\$name";

    return new $model( $this->getApi() );

  } // _getEmptyModel

} // Client
