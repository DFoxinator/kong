<?php

use \Behance\Kong\Endpoints;
use \Behance\Kong\MailChimp;
use \Behance\Kong\Model\MailChimp\MailingList;

class MailingListTest extends \PHPUnit_Framework_TestCase {

  /**
   * ID to use for tests.
   *
   * @var integer
   */
  protected $_list_id = 3;

  /**
   * Test the flow of the subscribe method.
   *
   * @test
   */
  public function testSubscribe() {

    $email = uniqid();

    $expected_params = [
        'id'           => $this->_list_id,
        'email'        => [ 'email' => $email ],
        'double_optin' => false,
        'merge_vars'   => [],
    ];

    $api = $this->getMockBuilder( '\Behance\Kong\Api' )
                ->setMethods( [ 'execute' ] )
                ->getMock();

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->with( $expected_params, Endpoints::LIST_SUBSCRIBE, MailChimp::API_URI, MailChimp::API_VERSION, 'POST' );

    $list = $this->_getMockMailingList( [ '_getApi' ] );

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

    $expected_params = [
        'id'            => $this->_list_id,
        'email'         => [ 'email' => $email ],
        'delete_member' => false,
        'send_goodbye'  => false,
        'notify'        => false,
    ];

    $api = $this->getMockBuilder( '\Behance\Kong\Api' )
            ->setMethods( [ 'execute' ] )
            ->getMock();

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->with( $expected_params, Endpoints::LIST_UNSUBSCRIBE, MailChimp::API_URI, MailChimp::API_VERSION, 'POST' );

    $list = new MailingList( $api, [ 'id' => $this->_list_id ] );

    $list->unsubscribe( $email );

  } // testUnsubscribe

  /**
   * Test the flow of the batch subscribe method.
   *
   * @test
   */
  public function testBatchSubscribe() {

    $users = [ [], [] ];

    $expected_params = [
        'id'    => $this->_list_id,
        'batch' => $users,
    ];

    $api = $this->getMockBuilder( '\Behance\Kong\Api' )
            ->setMethods( [ 'execute' ] )
            ->getMock();

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->with( $expected_params, Endpoints::LIST_BATCH_SUBSCRIBE, MailChimp::API_URI, MailChimp::API_VERSION, 'POST' );

    $list = $this->_getMockMailingList( [ '_getApi' ] );

    $list->expects( $this->once() )
        ->method( '_getApi' )
        ->will( $this->returnValue( $api ) );

    $list->batchSubscribe( $users );

  } // testBatchSubscribe

  /**
   * Test that an exception is thrown when given too many users.
   *
   * @test
   * @expectedException \Behance\Kong\Exception\MaximumExceededException
   */
  public function testBatchSubscribeException() {

    $users = range( 0, MailingList::MAX_BATCH_USERS );

    $list = $this->_getMockMailingList( [ '_execute' ] );

    $list->batchSubscribe( $users );

  } // testBatchSubscribeException

  /**
   * Test the flow of the batch subscribe method.
   *
   * @test
   */
  public function testBatchUnsubscribe() {

    $users = [ [], [] ];

    $expected_params = [
        'id'            => $this->_list_id,
        'batch'         => $users,
        'delete_member' => false,
        'send_goodbye'  => false,
        'notify'        => false,
    ];

    $api = $this->getMockBuilder( '\Behance\Kong\Api' )
            ->setMethods( [ 'execute' ] )
            ->getMock();

    $api->expects( $this->once() )
        ->method( 'execute' )
        ->with( $expected_params, Endpoints::LIST_BATCH_UNSUBSCRIBE, MailChimp::API_URI, MailChimp::API_VERSION, 'POST' );

    $list = $this->_getMockMailingList( [ '_getApi' ] );

    $list->expects( $this->once() )
         ->method( '_getApi' )
         ->will( $this->returnValue( $api ) );

    $list->batchUnsubscribe( $users );

  } // testBatchUnsubscribe

  /**
   * Test that an exception is thrown when given too many users.
   *
   * @test
   * @expectedException \Behance\Kong\Exception\MaximumExceededException
   */
  public function testBatchUnsubscribeException() {

    $users = range( 0, MailingList::MAX_BATCH_USERS );

    $list = $this->_getMockMailingList( [ '_getApi' ] );

    $list->batchUnsubscribe( $users );

  } // testBatchUnsubscribeException

  protected function _getMockMailingList( array $list_methods = [], $api = null ) {

    $list = $this->getMockBuilder( '\Behance\Kong\Model\MailChimp\MailingList' );

    if ( !empty( $api ) ) {
      $list->setConstructorArgs( [ $api ] );
    }
    else {
      $list->disableOriginalConstructor();
    }

    $list = $list->setMethods( $list_methods )
                 ->getMock();

    $list->setData( [ 'id' => 3 ] );

    return $list;

  } // _getMockMailingList

} // MailingListTest
