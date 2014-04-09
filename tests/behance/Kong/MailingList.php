<?php

use \behance\Kong\Endpoints;
use \behance\Kong\MailingList;

class MailingListTest extends \PHPUnit_Framework_TestCase {

  /**
   * Test the flow of the subscribe method.
   *
   * @test
   */
  public function testSubscribe() {

    $email = 'dunphy@adobe.com';
    $list = $this->_getMockMailingList( [ '_getApi' ] );

    $api = $this->_getMockApi( [ 'execute' ] );

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->with( $this->callback( function( $subject ) {
          return is_array( $subject );
        } ), Endpoints::LIST_SUBSCRIBE, 'POST' )
        ->will( $this->returnValue( true ) );

    $list->expects( $this->once() )
         ->method( '_getApi' )
         ->will( $this->returnValue( $api ) );

    $list->subscribe( $email );

  } // testSubscribe

  /**
   * Test the flow of the unsubscribe method.
   *
   * @test
   */
  public function testUnsubscribe() {

    $email = 'dunphy@adobe.com';
    $list = $this->_getMockMailingList( [ '_getApi' ] );

    $api = $this->_getMockApi( [ 'execute' ] );

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->with( $this->callback( function( $subject ) {
          return is_array( $subject );
        } ), Endpoints::LIST_UNSUBSCRIBE, 'POST' )
        ->will( $this->returnValue( true ) );

    $list->expects( $this->once() )
         ->method( '_getApi' )
         ->will( $this->returnValue( $api ) );

    $list->unsubscribe( $email );

  } // testUnsubscribe

  /**
   * Test the flow of the batch subscribe method.
   *
   * @test
   */
  public function testBatchSubscribe() {

    $users = [ [], [] ];

    $list = $this->_getMockMailingList( [ '_getApi' ] );

    $api = $this->_getMockApi( [ 'execute' ] );

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->with( $this->callback( function( $subject ) use ( $users ) {
          return is_array( $subject ) && ( count( $subject['batch'] ) === count( $users ) );
        } ), Endpoints::LIST_BATCH_SUBSCRIBE, 'POST' )
        ->will( $this->returnValue( true ) );

    $list->expects( $this->once() )
         ->method( '_getApi' )
         ->will( $this->returnValue( $api ) );

    $list->batchSubscribe( $users );

  } // testBatchSubscribe

  /**
   * Test that an exception is thrown when given too many users.
   *
   * @test
   * @expectedException \behance\Kong\Exception\MaximumExceededException
   */
  public function testBatchSubscribeException() {

    $users = range( 0, MailingList::MAX_BATCH_USERS );

    $list = $this->_getMockMailingList( [ '_getApi' ] );

    $list->batchSubscribe( $users );

  } // testBatchSubscribeException

  /**
   * Test the flow of the batch subscribe method.
   *
   * @test
   */
  public function testBatchUnsubscribe() {

    $users = [ [], [] ];

    $list = $this->_getMockMailingList( [ '_getApi' ] );

    $api = $this->_getMockApi( [ 'execute' ] );

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->with( $this->callback( function( $subject ) use ( $users ) {
          return is_array( $subject ) && ( count( $subject['batch'] ) === count( $users ) );
        } ), Endpoints::LIST_BATCH_UNSUBSCRIBE, 'POST' )
        ->will( $this->returnValue( true ) );

    $list->expects( $this->once() )
         ->method( '_getApi' )
         ->will( $this->returnValue( $api ) );

    $list->batchUnsubscribe( $users );

  } // testBatchUnsubscribe

  /**
   * Test that an exception is thrown when given too many users.
   *
   * @test
   * @expectedException \behance\Kong\Exception\MaximumExceededException
   */
  public function testBatchUnsubscribeException() {

    $users = range( 0, MailingList::MAX_BATCH_USERS );

    $list = $this->_getMockMailingList( [ '_getApi' ] );

    $list->batchUnsubscribe( $users );

  } // testBatchUnsubscribeException

  protected function _getMockMailingList( array $list_methods = [], array $api_methods = [] ) {

    $list = $this->getMockBuilder( '\behance\Kong\MailingList' )
                 ->disableOriginalConstructor()
                 ->setMethods( $list_methods )
                 ->getMock();

    $list->setData( [ 'id' => 3 ] );

    return $list;

  } // _getMockMailingList

  protected function _getMockApi( array $methods = [] ) {

    $api = $this->getMockBuilder( '\behance\Kong\Api' )
                ->disableOriginalConstructor();

    if ( !empty( $methods ) ) {
        $api->setMethods( $methods );
    }

    return $api->getMock();

  } // _getMockApi

} // MailingListTest
