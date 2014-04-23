<?php

use \Behance\Kong;

class KongTest extends \PHPUnit_Framework_TestCase {

  public function testGetMailChimp() {

    $kong = new Kong;

    $mailchimp = $kong->getMailChimp();

    $this->assertInstanceOf( '\behance\Kong\MailChimp', $mailchimp );

  } // testGetMailChimp

  public function testSetMailChimp() {

    $kong = new Kong;
    $mailchimp = $this->getMock( '\behance\Kong\MailChimp' );

    $kong->setMailChimp( $mailchimp );

    $this->assertInstanceOf( '\behance\Kong\MailChimp', $kong->getMailChimp() );

  } // testSetMailChimp

  public function testGetMandrill() {

    $kong = new Kong;

    $mandrill = $kong->getMandrill();

    $this->assertInstanceOf( '\behance\Kong\Mandrill', $mandrill );

  } // testGetMandrill

  public function testSetMandrill() {

    $kong = new Kong;
    $mandrill = $this->getMock( '\behance\Kong\Mandrill' );

    $kong->setMandrill( $mandrill );

    $this->assertInstanceOf( '\behance\Kong\Mandrill', $kong->getMandrill() );

  } // testSetMandrill

} // KongTest
