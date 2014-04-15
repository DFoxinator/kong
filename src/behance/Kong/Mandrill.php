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

  /**
   * Send a transactional email to a single user.
   *
   * @param string $email
   */
  public function send() {

    $params = [
        'message' => [
            'text' => 'Hello world',
            'subject' => 'Test subject',
            'from_name' => 'King Kong',
            'from_email' => 'dunphy@adobe.com',
            'to' => [
                [ 'email' => 'markdunphy@gmail.com', 'name' => 'Mark Dunphy' ]
            ],
        ],
    ];

    $response = $this->_execute( $params, Endpoints::MANDRILL_SEND );

    return $this->_formatResponse( $response );

  } // send

} // Mandrill
