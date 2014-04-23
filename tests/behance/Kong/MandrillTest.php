<?php

use \Behance\Kong\Mandrill;

class MandrillTest extends \PHPUnit_Framework_TestCase {

  public function testMessage() {

    $mandrill = new Mandrill;

    $message = $mandrill->message();

    $this->assertInstanceOf( '\Behance\Kong\Model\Mandrill\Message', $message );

  } // testMessage

} // MandrillTest
