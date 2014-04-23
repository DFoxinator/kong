<?php

use \Behance\Kong\Model;

class ModelTest extends \PHPUnit_Framework_TestCase {

  /**
   * @expectedException \behance\Kong\Exception\PropertyNotFoundException
   */
  public function testGetBadProperty() {

    $model = $this->getMockBuilder( '\behance\Kong\Model' )
                  ->disableOriginalConstructor()
                  ->setMethods( [ 'setApi' ] )
                  ->getMock();

    $model->setData( [] );

    $model->does_not_exist;

  } // testGetBadProperty

  public function testToArray() {

    $model = $this->getMockBuilder( '\behance\Kong\Model' )
                  ->disableOriginalConstructor()
                  ->setMethods( [ 'setApi' ] )
                  ->getMock();

    $data = [ 'hello' => 'world' ];

    $model->setData( $data );

    $this->assertEquals( $data, $model->toArray() );

  } // testToArray

  /**
   * @expectedException \behance\Kong\Exception\InvalidModelException
   */
  public function testGetClientStringFailure() {

    Model::getClientString( uniqid() );

  } // testGetClientStringFailure

  public function testGetClientStringSuccessful() {

    $client = Model::getClientString( Model::MAILCHIMP_LIST );
    $this->assertEquals( 'MailChimp', $client );

    $client = Model::getClientString( Model::MANDRILL_MESSAGE );
    $this->assertEquals( 'Mandrill', $client );

  } // testGetClientStringSuccessful

} // ModelTest
