<?php

use \Behance\Kong;

class KongTest extends \PHPUnit_Framework_TestCase {

  public function testGetMailChimp() {

    $kong = new Kong;

    $mailchimp = $kong->getMailChimp();

    $this->assertInstanceOf( '\Behance\Kong\MailChimp', $mailchimp );

  } // testGetMailChimp

  public function testSetMailChimp() {

    $kong = new Kong;
    $mailchimp = $this->getMock( '\Behance\Kong\MailChimp' );

    $kong->setMailChimp( $mailchimp );

    $this->assertInstanceOf( '\Behance\Kong\MailChimp', $kong->getMailChimp() );

  } // testSetMailChimp

  public function testGetMandrill() {

    $kong = new Kong;

    $mandrill = $kong->getMandrill();

    $this->assertInstanceOf( '\Behance\Kong\Mandrill', $mandrill );

  } // testGetMandrill

  public function testSetMandrill() {

    $kong = new Kong;
    $mandrill = $this->getMock( '\Behance\Kong\Mandrill' );

    $kong->setMandrill( $mandrill );

    $this->assertInstanceOf( '\Behance\Kong\Mandrill', $kong->getMandrill() );

  } // testSetMandrill

} // KongTest
