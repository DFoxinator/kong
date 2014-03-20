<?php

class ApiTest extends \PHPUnit_Framework_TestCase {


  public function testExecute() {

    $request = $this->getMockBuilder( 'Guzzle\Http\Message\Request' )
                    ->disableOriginalConstructor()
                    ->setMethods( [ 'send' ] )
                    ->getMock();

    $request->expects( $this->atLeastOnce() )
            ->method( 'send' )
            ->will( $this->returnValue( $response ) );

    $http = $this->getMockBuilder( 'Guzzle\Http\Client' )
                 ->disableOriginalConstructor()
                 ->setMethods( [ 'createRequest' ] )
                 ->getMock();

    $http->expects( $this->atLeastOnce() )
         ->method( 'createRequest' )
         ->will( $this->returnValue( $request ) );

    $kong = $this->getMockBuilder( 'behance\Kong' )
                 ->disableOriginalConstructor()
                 ->setMethods( [] )
                 ->getMock();

    $api = $this->getMockBuilder( 'behance\Kong\Api' )
                ->setConstructorArgs( [ $http ] )
                ->setMethods( [ '_getHttpClient' ] )
                ->getMock();

    $api->expects( $this->atLeastOnce() )
        ->method( '_getHttpClient' )
        ->will( $this->returnValue( $http ) );

    $api->execute( [], '' );

  } // testExecute

} // ApiTest
