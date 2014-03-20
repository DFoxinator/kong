<?php namespace behance\Kong;

class Endpoints {

  /**
   * Base information
   */
  const API_URI     = 'api.mailchimp.com';
  const API_VERSION = '2.0';

  /**
   * API Endpoints
   */
  const LISTS                = 'lists/list.json';
  const LIST_SUBSCRIBE       = 'lists/subscribe.json';
  const LIST_BATCH_SUBSCRIBE = 'lists/batch-subscribe.json';

  /**
   * Build an endpoint.
   *
   * @param string $endpoint
   * @param string $data_center
   *
   * @return string
   */
  public static function build( $endpoint, $data_center ) {

    return $data_center . '.' . implode( '/', [
        static::API_URI,
        static::API_VERSION,
        $endpoint,
    ] );

  } // build

} // Endpoints
