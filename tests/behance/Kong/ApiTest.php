<?php

use \behance\Kong\Api;
use \behance\Kong\Endpoints;
use \behance\Kong\MailChimp;

class ApiTest extends \PHPUnit_Framework_TestCase {

  public function testSetApiKey() {

    $key = uniqid();
    $data_center = 'us1';
    $compiled = "{$key}-{$data_center}";

    $api = new Api;

    $api->setApiKey( $compiled );

    $this->assertEquals( $compiled, $api->getApiKey() );
    $this->assertEquals( $data_center, $api->getDataCenter() );

  } // testSetApiKey

  public function testExecuteGet() {

    $params = [ 'hello' => 'world' ];
    $endpoint = Endpoints::LISTS;
    $uri = MailChimp::API_URI;
    $version = MailChimp::API_VERSION;
    $method = 'GET';
    $data_center = 'us1';
    $headers = null;
    $body = null;

    $expected_full_endpoint = "https://{$data_center}.{$uri}/{$version}/{$endpoint}";
    $expected_options = [
        'query' => array_merge( $params, [ 'key' => null ] ),
        'exceptions' => false,
    ];

    $request = $this->getMockBuilder( '\Guzzle\Http\Message\Request' )
                    ->disableOriginalConstructor()
                    ->setMethods( [ 'send' ] )
                    ->getMock();

    $request->expects( $this->once() )
            ->method( 'send' );

    $http_client = $this->getMockBuilder( '\Guzzle\Http\Client' )
                        ->setMethods( [ 'createRequest' ] )
                        ->getMock();

    $http_client->expects( $this->once() )
                ->method( 'createRequest' )
                ->with( $method, $expected_full_endpoint, $headers, $body, $expected_options )
                ->will( $this->returnValue( $request ) );

    $api = $this->getMockBuilder( '\behance\Kong\Api' )
                ->setConstructorArgs( [ $http_client ] )
                ->setMethods( [ 'getDataCenter' ] )
                ->getMock();

    $api->expects( $this->once() )
        ->method( 'getDataCenter' )
        ->will( $this->returnValue( $data_center ) );

    $api->execute( $params, $endpoint, $uri, $version, $method );

  } // testExecuteGet

  public function testExecutePost() {

    $params      = [ 'hello' => 'world' ];
    $endpoint    = Endpoints::LISTS;
    $uri         = MailChimp::API_URI;
    $version     = MailChimp::API_VERSION;
    $method      = 'POST';
    $data_center = 'us1';
    $headers     = null;

    $expected_body = array_merge( $params, [ 'key' => null ] );
    $expected_full_endpoint = "https://{$data_center}.{$uri}/{$version}/{$endpoint}";
    $expected_options = [
        'body' => $expected_body,
        'exceptions' => false,
    ];

    $request = $this->getMockBuilder( '\Guzzle\Http\Message\Request' )
                    ->disableOriginalConstructor()
                    ->setMethods( [ 'send' ] )
                    ->getMock();

    $request->expects( $this->once() )
            ->method( 'send' );

    $http_client = $this->getMockBuilder( '\Guzzle\Http\Client' )
                        ->setMethods( [ 'createRequest' ] )
                        ->getMock();

    $http_client->expects( $this->once() )
                ->method( 'createRequest' )
                ->with( $method, $expected_full_endpoint, $headers, $expected_body, $expected_options )
                ->will( $this->returnValue( $request ) );

    $api = $this->getMockBuilder( '\behance\Kong\Api' )
                ->setConstructorArgs( [ $http_client ] )
                ->setMethods( [ 'getDataCenter' ] )
                ->getMock();

    $api->expects( $this->once() )
        ->method( 'getDataCenter' )
        ->will( $this->returnValue( $data_center ) );

    $api->execute( $params, $endpoint, $uri, $version, $method );

  } // testExecutePost

} // ApiTest
