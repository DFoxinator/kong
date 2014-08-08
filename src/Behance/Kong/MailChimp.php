<?php namespace Behance\Kong;

class MailChimp extends \Behance\Kong\Client {

  const API_URI     = 'api.mailchimp.com';
  const API_VERSION = '2.0';

  /**
   * Retrieve a single list
   *
   * @param string  $id      the list id
   * @param boolean $hydrate whether to call to the API to get data, or just get an instance with an id set
   *
   * @return \Behance\Kong\Model\MailChimp\MailingList|boolean
   */
  public function getList( $id, $hydrate = true ) {

    if ( $hydrate === false ) {
      return $this->_getEmptyModel( Model::MAILCHIMP_LIST, [ 'id' => $id ] );
    }

    $params = [
        'filters' => [
            'list_id' => $id,
        ]
    ];

    $response = $this->_execute( $params, Endpoints::LISTS );
    $array    = $this->formatResponse( $response, Model::MAILCHIMP_LIST );

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

    return $this->formatResponse( $response, Model::MAILCHIMP_LIST );

  } // getLists

} // MailChimp
