<?php

use \behance\Kong\Mandrill;

class MandrillTest extends \PHPUnit_Framework_TestCase {

  public function testMessage() {

    $mandrill = new Mandrill;

    $message = $mandrill->message();

    $this->assertInstanceOf( '\behance\Kong\Model\Mandrill\Message', $message );

  } // testMessage

} // MandrillTest
