<?php namespace behance\Kong;

use \behance\Kong\Endpoints;

class Mandrill extends \behance\Kong\Client {

  const API_URI     = 'mandrillapp.com/api';
  const API_VERSION = '1.0';

  /**
   * Get an empty model instance.
   *
   * @return \behance\Kong\Model\Mandrill\Messgae
   */
  public function message() {

    return $this->_getEmptyModel( Model::MANDRILL_MESSAGE );

  } // message

} // Mandrill
