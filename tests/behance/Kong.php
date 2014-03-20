<?php

use \behance\Kong;
use \behance\Kong\Model;
use \behance\Kong\MailingList;
use \behance\Kong\Endpoints;

class KongTest extends \PHPUnit_Framework_TestCase {

  /**
   * The data center to use.
   *
   * @var string
   */
  protected $_data_center;

  /**
   * The api key without the data center at the end
   *
   * @var string
   */
  protected $_key;

  public function setUp() {

    $this->_key = 'thisisanarbitrarymadeupkey';
    $this->_data_center = 'us1';

  } // setUp

  /**
   * Test the api key setter method.
   */
  public function testSetApiKey() {

    $key = $this->_key . '-' . $this->_data_center;

    $kong = new Kong;
    $kong->setApiKey( $key );

    $api = $kong->getApi();

    $this->assertEquals( $key, $api->getApiKey() );
    $this->assertEquals( $this->_data_center, $api->getDataCenter() );

  } // testSetApiKey

  /**
   * Test a successful call to getLists
   */
  public function testGetListsSuccessful() {

    $response = $this->_getMockResponse( 200, [ [], [], [] ], [] );

    $kong = $this->getMockBuilder( 'behance\Kong' )
                 ->disableOriginalConstructor()
                 ->setMethods( [ 'getApi' ] )
                 ->getMock();

    $api = $this->_getMockApi();

    $api->expects( $this->atLeastOnce() )
        ->method( 'execute' )
        ->will( $this->returnValue( $response ) );

    $kong->expects( $this->atLeastOnce() )
         ->method( 'getApi' )
         ->will( $this->returnValue( $api ) );

    $lists = $kong->getLists();

    $this->assertInternalType( 'array', $lists );

    foreach ( $lists as $list ) {
      $this->assertInstanceOf( 'behance\Kong\MailingList', $list );
    }

  } // testGetListsSuccessful

  public function testGetListsFailure() {

    $response = $this->_getMockResponse( 500, [], [] );

    $kong = $this->getMockBuilder( 'behance\Kong' )
                 ->disableOriginalConstructor()
                 ->setMethods( [ 'getApi', '_handleErrors' ] )
                 ->getMock();

    $api = $this->_getMockApi();

    $api->expects( $this->atLeastOnce() )
        ->method( 'execute' )
        ->will( $this->returnValue( $response ) );

    $kong->expects( $this->atLeastOnce() )
         ->method( 'getApi' )
         ->will( $this->returnValue( $api ) );

    $kong->expects( $this->once() )
         ->method( '_handleErrors' )
         ->will( $this->returnValue( null ) );

    $kong->getLists();

  } // testGetListsFailure

  protected function _getMockApi() {

    return $this->getMockBuilder( 'behance\Kong\Api' )
                ->disableOriginalConstructor()
                ->setMethods( [ 'execute' ] )
                ->getMock();

  } // _getMockApi

  protected function _getMockResponse( $status_code = 200, $data = [], $errors = [] ) {

    $response = $this->getMockBuilder( 'Guzzle\Http\Message\Response' )
                     ->disableOriginalConstructor()
                     ->setMethods( [ 'json', 'getStatusCode' ] )
                     ->getMock();

    $response->expects( $this->atLeastOnce() )
             ->method( 'json' )
             ->will( $this->returnValue( [
                 'total'  => count( $data ),
                 'data'   => $data,
                 'errors' => $errors,
             ] ) );

    $response->expects( $this->atLeastOnce() )
             ->method( 'getStatusCode' )
             ->will( $this->returnValue( $status_code ) );

    return $response;

  } // _getMockResponse

} // KongTest
