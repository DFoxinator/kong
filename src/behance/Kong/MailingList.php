<?php namespace behance\Kong;

use \behance\Kong\Model;
use \behance\Kong\Exception\MaximumExceededException;

class MailingList extends Model {

  /**
   * The maximum amount of users allowed in a single batch
   * subscribe request.
   *
   * @var int
   */
  const MAX_BATCH_USERS = 10000;

  /**
   * Subscribe a user with the provided profile
   * data.
   *
   * http://apidocs.mailchimp.com/api/2.0/lists/subscribe.php
   *
   * @param string $email
   * @param array  $data
   */
  public function subscribe( $email, array $data = [] ) {

    $params = [
        'id' => $this->__get( 'id' ),
        'email' => [ 'email' => $email ],
        'double_optin' => false,
    ];

    $this->_getApi()->execute( $params, Endpoints::LIST_SUBSCRIBE, 'POST' );

  } // subscribe

  /**
   * Batch subscribe users to a list.
   *
   * http://apidocs.mailchimp.com/api/2.0/lists/batch-subscribe.php
   *
   * @param array $users See above docs above for array structure
   * @param array options
   *
   */
  public function batchSubscribe( array $users, array $options = [] ) {

    if ( count( $users ) > static::MAX_BATCH_USERS ) {
      throw new MaximumExceededException( 'Can not batch subscribe more than ' . static::MAX_BATCH_USERS . ' at a time.' );
    }

    $params = [
        'id'    => $this->__get( 'id' ),
        'batch' => $users
    ];

    $params = array_merge( $params, $options );

    $this->_getApi()->execute( $params, Endpoints::LIST_BATCH_SUBSCRIBE, 'POST' );

  } // batchSubscribe

} // MailingList
