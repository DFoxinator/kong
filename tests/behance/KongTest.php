<?php

use \behance\Kong;
use \behance\Kong\Model;
use \behance\Kong\MailingList;

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

} // KongTest
