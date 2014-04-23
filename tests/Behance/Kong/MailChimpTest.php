<?php

use \Behance\Kong\Model;
use \Behance\Kong\Endpoints;

class MailChimpTest extends \PHPUnit_Framework_TestCase {

  public function testGetListSuccessful() {

    $response = $this->_getMockResponse( [ [], [], [] ], [] );
    $api = $this->_getMockApi();

    $kong = $this->getMockBuilder( 'Behance\Kong\MailChimp' )
                 ->disableOriginalConstructor()
                 ->setMethods( [ 'getApi' ] )
                 ->getMock();

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->will( $this->returnValue( $response ) );

    $kong->expects( $this->atLeastOnce() )
         ->method( 'getApi' )
         ->will( $this->returnValue( $api ) );

    $kong->getList( uniqid() );


  } // testGetListSuccessful

  public function testGetListFailure() {

    $response = $this->_getMockResponse( [ [], [], [] ], [] );
    $api = $this->_getMockApi();

    $kong = $this->getMockBuilder( 'Behance\Kong\MailChimp' )
                 ->disableOriginalConstructor()
                 ->setMethods( [ 'getApi', '_formatResponse' ] )
                 ->getMock();

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->will( $this->returnValue( $response ) );

    $kong->expects( $this->atLeastOnce() )
         ->method( 'getApi' )
         ->will( $this->returnValue( $api ) );

    $kong->expects( $this->once() )
         ->method( '_formatResponse' )
         ->with( $this->equalTo( $response ), $this->equalTo( Model::MAILCHIMP_LIST ) )
         ->will( $this->returnValue( [] ) );

    $this->assertFalse( $kong->getList( uniqid() ) );

  } // testGetListFailure

  /**
   * Test a successful call to getLists
   */
  public function testGetListsSuccessful() {

    $response = $this->_getMockResponse( [ [], [], [] ], [] );
    $api = $this->_getMockApi();

    $kong = $this->getMockBuilder( 'Behance\Kong\MailChimp' )
                 ->disableOriginalConstructor()
                 ->setMethods( [ 'getApi' ] )
                 ->getMock();

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->will( $this->returnValue( $response ) );

    $kong->expects( $this->atLeastOnce() )
         ->method( 'getApi' )
         ->will( $this->returnValue( $api ) );

    $lists = $kong->getLists();

    $this->assertInternalType( 'array', $lists );

    foreach ( $lists as $list ) {
      $this->assertInstanceOf( '\Behance\Kong\Model\MailChimp\MailingList', $list );
    }

  } // testGetListsSuccessful

  public function testGetListsFailure() {

    $response = $this->_getMockResponse( [], [] );
    $api = $this->_getMockApi();

    $kong = $this->getMockBuilder( 'Behance\Kong\MailChimp' )
                 ->disableOriginalConstructor()
                 ->setMethods( [ 'getApi' ] )
                 ->getMock();

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->will( $this->returnValue( $response ) );

    $kong->expects( $this->once() )
         ->method( 'getApi' )
         ->will( $this->returnValue( $api ) );

    $kong->getLists();

  } // testGetListsFailure

  protected function _getMockApi() {

    return $this->getMockBuilder( 'Behance\Kong\Api' )
                ->disableOriginalConstructor()
                ->setMethods( [ 'execute' ] )
                ->getMock();

  } // _getMockApi

  protected function _getMockResponse( $data = [], $errors = [] ) {

    $response = $this->getMockBuilder( 'Guzzle\Http\Message\Response' )
                     ->disableOriginalConstructor()
                     ->setMethods( [ 'json' ] )
                     ->getMock();

    $response->expects( $this->any() )
             ->method( 'json' )
             ->will( $this->returnValue( [
                 'total'  => count( $data ),
                 'data'   => $data,
                 'errors' => $errors,
             ] ) );

    return $response;

  } // _getMockResponse

} // MailChimpTest
