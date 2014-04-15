<?php namespace behance;

use \behance\Kong\Api;
use \behance\Kong\Mandrill;
use \behance\Kong\MailChimp;
use \behance\Kong\Endpoints;
use \behance\Kong\Model;
use \Guzzle\Http\Message\Response;

class Kong extends \behance\Kong\Client {

  const API_URI     = 'api.mailchimp.com';
  const API_VERSION = '2.0';

  /**
   * Mandrill client for transactional sends.
   *
   * @var behance\Kong\Mandrill
   */
  protected $_mandrill;

  /**
   * MailChimp client.
   *
   * @var behance\Kong\MailChimp
   */
  protected $_mailchimp;

  public function getMailChimp( $key = null ) {

    if ( is_null( $this->_mailchimp ) ) {
      $this->setMailChimp( new MailChimp( $key ) );
    }

    return $this->_mailchimp;

  } // getMailChimp

  /**
   * Retrieve the Mandrill client.
   *
   * @param string $key The Mandrill API key to use.
   *
   * @return \behance\Kong\Mandrill
   */
  public function getMandrill( $key = null ) {

    if ( is_null( $this->_mandrill ) ) {
      $this->setMandrill( new Mandrill( $key ) );
    }

    return $this->_mandrill;

  } // getMandrill

  /**
   * Set the Mandrill client.
   *
   * @param \behance\Kong\Mandrill $mandrill
   */
  public function setMandrill( Mandrill $mandrill ) {

    $this->_mandrill = $mandrill;

  } // setMandrill

  /**
   * Throw the appropriate exception based on the given errors.
   *
   * Not yet implemented.
   *
   * @param Guzzle\Http\Message\Response $response
   * @param array $errors
   */
  protected function _handleErrors( Response $response, array $errors ) {


  } // _handleErrors

} // Kong
