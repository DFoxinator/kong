<?php namespace Behance\Kong;

use Behance\Kong\Api;
use Behance\Kong\Exception\PropertyNotFoundException;
use Behance\Kong\Exception\InvalidModelException;

class Model {

  /**
   * MailChimp model names.
   *
   * @var string
   */
  const MAILCHIMP_LIST = '\Behance\Kong\Model\MailChimp\MailingList';
  const MAILCHIMP_LIST_MEMBER = '\Behance\Kong\Model\MailChimp\MailingList\Member';

  /**
   * Mandrill model names.
   *
   * @var string
   */
  const MANDRILL_MESSAGE = '\Behance\Kong\Model\Mandrill\Message';

  /**
   * MailChimp models
   *
   * @var array
   */
  protected static $_MAILCHIMP_MODELS = [
      self::MAILCHIMP_LIST,
      self::MAILCHIMP_LIST_MEMBER,
  ];

  /**
   * Mandrill models
   *
   * @var array
   */
  protected static $_MANDRILL_MODELS = [
      self::MANDRILL_MESSAGE,
  ];

  /**
   * This must be implemented an all models extending this class.
   * It is the class name of the \Behance\Kong\Client pertaining to the model.
   *
   * @var string either MailChimp or Mandrill
   */
  protected $_client_string;

  /**
   * @var \Behance\Kong\Client
   */
  protected $_client;

  /**
   * The data for the model.
   *
   * @var array
   */
  protected $_data  = [];

  /**
   * HTTP object for cURL requests.
   *
   * @var Guzzle\Http\Client
   */
  protected $_api;

  /**
   * @param \Behance\Kong\Client $client
   * @param array $data data to hydrate the model with
   */
  public function __construct( Client $client, array $data = [] ) {

    $this->setClient( $client );
    $this->setApi( $client->getApi() ); // TODO: This is only for backwards compatibility. Remove in future.
    $this->setData( $data );

  } // __construct

  public function __get( $property ) {

    if ( isset( $this->_data[ $property ] ) ) {
      return $this->_data[ $property ];
    }

    throw new PropertyNotFoundException( 'Could not find property ' . $property . ' in class ' . get_class( $this ) );

  } // __get

  public function setData( array $data = [] ) {

    $this->_data = $data;

  } // setData

  public function setApi( Api $api ) {

    $this->_api  = $api;

  } // setApi

  public function toArray() {

    return $this->_data;

  } // toArray

  /**
   * @return \Behance\Kong\Client
   */
  public function getClient() {

    return $this->_client;

  } // getClient

  /**
   * @param \Behance\Kong\Client $client
   */
  public function setClient( Client $client ) {

    $this->_client = $client;

  } // setClient

  /**
   * Determines if $model is a MailChimp model or Mandrill model and
   * returns a string with the result.
   *
   * @param string $model the model name to get the client string for
   *
   * @return string either MailChimp or Mandrill
   */
  public static function getClientString( $model ) {

    if ( in_array( $model, static::$_MAILCHIMP_MODELS ) ) {
      return 'MailChimp';
    }

    if ( in_array( $model, static::$_MANDRILL_MODELS ) ) {
      return 'Mandrill';
    }

    throw new InvalidModelException( "[$model] does not exist as a MailChimp or Mandrill model." );

  } // getClientString

  protected function _getApi() {

    return $this->_api;

  } // _getApi

  /**
   * Wrapper for calling the API. Will automatically detect which client
   * is being used and use the appropriate version/uri.
   *
   * @param array $params
   * @param string $endpoint
   * @param string $method
   *
   * @return
   */
  protected function _execute( array $params, $endpoint, $method ) {

    $client = "\Behance\Kong\\{$this->_client_string}";

    return $this->_getApi()->execute( $params, $endpoint, $client::API_URI, $client::API_VERSION, $method );

  } // _execute

} // Model
