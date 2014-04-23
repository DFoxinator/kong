<?php namespace Behance\Kong;

class MailChimp extends \Behance\Kong\Client {

  const API_URI = 'api.mailchimp.com';
  const API_VERSION = 'v2';

  /**
   * Retrieve a single list
   *
   * @param string $id the list id
   *
   * @return \Behance\Kong\Model\MailChimp\MailingList|boolean
   */
  public function getList( $id ) {

    $params = [
        'filters' => [
            'list_id' => $id,
        ]
    ];

    $response = $this->_execute( $params, Endpoints::LISTS );
    $array    = $this->_formatResponse( $response, Model::MAILCHIMP_LIST );

    if ( !empty( $array[0] ) ) {
      return $array[0];
    }

    return false;

  } // getList

  /**
   * Retrieve an array of list objects
   *
   * @param array $params
   *
   * @return array an array of \Behance\Kong\Model\MailChimp\MailingList
   */
  public function getLists( array $params = [] ) {

    $response = $this->_execute( $params, Endpoints::LISTS );

    return $this->_formatResponse( $response, Model::MAILCHIMP_LIST );

  } // getLists

} // MailChimp
