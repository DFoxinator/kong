<?php namespace Behance;

use \Behance\Kong\Api;
use \Behance\Kong\Mandrill;
use \Behance\Kong\MailChimp;
use \Behance\Kong\Endpoints;
use \Behance\Kong\Model;
use \Guzzle\Http\Message\Response;

class Kong {

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

  /**
   * Retrieve the MailChimp client.
   *
   * @param string $key The MailChimp API key to use.
   *
   * @return \behance\Kong\MailChimp
   */
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
   * Set the MailChimp client.
   *
   * @param \behance\Kong\MailChimp $mailchimp
   */
  public function setMailchimp( MailChimp $mailchimp ) {

    $this->_mailchimp = $mailchimp;

  } // setMailchimp

} // Kong
