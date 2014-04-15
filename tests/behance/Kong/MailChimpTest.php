<?php

class MailChimpTest extends \PHPUnit_Framework_TestCase {

  /**
   * Test a successful call to getLists
   */
  public function testGetListsSuccessful() {

    $response = $this->_getMockResponse( [ [], [], [] ], [] );

    $kong = $this->getMockBuilder( 'behance\Kong\MailChimp' )
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
      $this->assertInstanceOf( '\behance\Kong\Model\MailChimp\MailingList', $list );
    }

  } // testGetListsSuccessful

  public function testGetListsFailure() {

    $response = $this->_getMockResponse( [], [] );

    $kong = $this->getMockBuilder( 'behance\Kong\MailChimp' )
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

    $kong->getLists();

  } // testGetListsFailure


  protected function _getMockApi() {

    return $this->getMockBuilder( 'behance\Kong\Api' )
                ->disableOriginalConstructor()
                ->setMethods( [ 'execute' ] )
                ->getMock();

  } // _getMockApi

  protected function _getMockResponse( $data = [], $errors = [] ) {

    $response = $this->getMockBuilder( 'Guzzle\Http\Message\Response' )
                     ->disableOriginalConstructor()
                     ->setMethods( [ 'json' ] )
                     ->getMock();

    $response->expects( $this->atLeastOnce() )
             ->method( 'json' )
             ->will( $this->returnValue( [
                 'total'  => count( $data ),
                 'data'   => $data,
                 'errors' => $errors,
             ] ) );

    return $response;

  } // _getMockResponse

} // MailChimpTest
