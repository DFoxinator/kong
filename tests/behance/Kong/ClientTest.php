<?php

use \Behance\Kong\Client;

class ClientTest extends \PHPUnit_Framework_TestCase {

  public function testSetApiKey() {

    $key = uniqid();

    $api = $this->getMockBuilder( '\Behance\Kong\Api' )
                ->disableOriginalConstructor()
                ->setMethods( [ 'setApiKey' ] )
                ->getMock();

    $api->expects( $this->once() )
        ->method( 'setApiKey' )
        ->with( $key );

    $client = $this->getMockBuilder( '\Behance\Kong\Client' )
                   ->disableOriginalConstructor()
                   ->setMethods( [ 'getApi' ] )
                   ->getMock();

    $client->expects( $this->once() )
           ->method( 'getApi' )
           ->will( $this->returnValue( $api ) );

    $client->setApiKey( $key );

  } // testSetApiKey

} // ClientTest
