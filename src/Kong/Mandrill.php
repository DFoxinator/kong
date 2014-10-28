<?php namespace Behance\Kong;

use \Behance\Kong\Endpoints;

class Mandrill extends \Behance\Kong\Client {

  const API_URI     = 'mandrillapp.com/api';
  const API_VERSION = '1.0';

  /**
   * Get an empty model instance.
   *
   * @return \Behance\Kong\Model\Mandrill\Messgae
   */
  public function message() {

    return $this->_getEmptyModel( Model::MANDRILL_MESSAGE );

  } // message

} // Mandrill
