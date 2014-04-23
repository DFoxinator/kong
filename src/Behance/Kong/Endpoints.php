<?php namespace Behance\Kong;

class Endpoints {

  /**
   * API Endpoints
   */
  const LISTS                  = 'lists/list.json';
  const LIST_SUBSCRIBE         = 'lists/subscribe.json';
  const LIST_UNSUBSCRIBE       = 'lists/unsubscribe.json';
  const LIST_BATCH_SUBSCRIBE   = 'lists/batch-subscribe.json';
  const LIST_BATCH_UNSUBSCRIBE = 'lists/batch-unsubscribe.json';

  /**
   * Mandrill API Endpoints
   */
  const MANDRILL_SEND          = 'messages/send.json';
  const MANDRILL_SEND_TEMPLATE = 'messages/send-template.json';

  /**
   * Build an endpoint.
   *
   * @param string $endpoint
   * @param string $data_center
   *
   * @return string
   */
  public static function build( $endpoint, $uri, $version, $data_center = null ) {

    $protocol = 'https://';

    if ( !empty( $data_center ) ) {
      $protocol .= $data_center . '.';
    }

    return $protocol . implode( '/', [
        $uri,
        $version,
        $endpoint,
    ] );

  } // build

} // Endpoints
