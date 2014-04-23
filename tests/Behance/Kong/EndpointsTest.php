<?php

use \Behance\Kong\Endpoints;
use \Behance\Kong\MailChimp;

class EndpointsTest extends \PHPUnit_Framework_TestCase {

  public function testBuild() {

    $endpoint    = Endpoints::LISTS;
    $uri         = MailChimp::API_URI;
    $version     = MailChimp::API_VERSION;
    $data_center = 'us1';

    $expected = "https://{$data_center}.{$uri}/{$version}/{$endpoint}";

    $compiled = Endpoints::build( $endpoint, $uri, $version, $data_center );

    $this->assertEquals( $expected, $compiled );

  } // testBuild

} // EndpointsTest
