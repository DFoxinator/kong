<?php namespace behance;

use \behance\Kong\Api;
use \behance\Kong\Endpoints;
use \Guzzle\Http\Message\Response;

class Kong {

  const MAILCHIMP_API_URI     = 'api.mailchimp.com';
  const MAILCHIMP_API_VERSION = '2.0';

  /**
   * Model class names
   */
  const MODEL_LIST = 'MailingList';

  /**
   * API object for abstracting requests.
   *
   * @var behance\Kong\Api
   */
  protected $_api;

  /**
   * @param string $key The MailChimp API key to use.
   */
  public function __construct( $key = null ) {

    $this->setApiKey( $key );

  } // __construct

  public function __get( $property ) {

    if ( isset( $this->$property ) ) {
      return $this->$property;
    }

    return null;

  } // __get

  /**
   * Retrieve an array of list objects
   *
   * @param array $params
   *
   * @return array an array of \behance\Kong\MailingList
   */
  public function getLists( array $params = [] ) {

    $response = $this->getApi()->execute( $params, Endpoints::LISTS );

    return $this->_formatResponse( $response, static::MODEL_LIST );

  } // getLists

  /**
   * Retrieve a single list
   *
   * @param string $id the list id
   *
   * @return \behance\Kong\MailingList
   */
  public function getList( $id ) {

    $params = [
        'filters' => [
            'list_id' => $id,
        ]
    ];

    $response = $this->getApi()->execute( $params, Endpoints::LISTS );
    $array    = $this->_formatResponse( $response, static::MODEL_LIST );

    if ( !empty( $array[0] ) ) {
      return $array[0];
    }

  } // getList

  /**
   * Get the API object.
   *
   * @return behance\Kong\Api
   */
  public function getApi() {

    if ( is_null( $this->_api ) ) {
      $this->setApi( new Api );
    }

    return $this->_api;

  } // getApi

  /**
   * Set the API object.
   *
   * @param behance\Kong\Api $api
   */
  public function setApi( Api $api ) {

    $this->_api = $api;

  } // setApi

  /**
   * Set the API key and update the data center.
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
   * Format the raw API response into an array of Models.
   *
   * @param Guzzle\Http\Message\Request $response api response
   * @param string $model the name of the class to instantiate
   *
   * @return array
   */
  protected function _formatResponse( Response $response, $model ) {

    $body = $response->json();

    if ( $response->getStatusCode() !== 200 ) {
      $this->_handleErrors( $response, $body );
    }

    $collection = [];
    $model      = "\behance\Kong\\$model";

    foreach ( $body['data'] as $item ) {
      $collection[] = new $model( $this->getApi(), $item );
    } // foreach body[data]

    return $collection;

  } // _formatResponse

  /**
   * Throw the appropriate exception based on the given errors.
   *
   * @param Guzzle\Http\Message\Response $response
   * @param array $errors
   *
   * @throw
   */
  protected function _handleErrors( Response $response, array $errors ) {



  } // _handleErrors

} // Kong
