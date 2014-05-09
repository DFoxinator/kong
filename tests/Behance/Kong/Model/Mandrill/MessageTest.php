<?php

use \Behance\Kong;
use \Behance\Kong\Endpoints;

class MessageTest extends \PHPUnit_Framework_TestCase {

  public function testAddMergeVarsValidateFailure() {

    $email = uniqid();
    $vars  = [];

    $message = $this->getMockBuilder( '\Behance\Kong\Model\Mandrill\Message' )
                    ->disableOriginalConstructor()
                    ->setMethods( [ 'hasRecipient' ] )
                    ->getMock();

    $message->expects( $this->once() )
            ->method( 'hasRecipient' )
            ->with( $email )
            ->will( $this->returnValue( true ) );

    $this->assertFalse( $message->addMergeVars( $email, $vars ) );

  } // testAddMergeVarsValidateFailure

  public function testAddMergeVarsValidateSuccessful() {

    $email = uniqid();
    $vars = [
        'hello' => 'world',
        'array' => [
            'of'  => 'things',
            'and' => 'stuff',
        ],
    ];

    $flattened_vars = [
        'hello'       => 'world',
        'array_0_of'  => 'things',
        'array_1_and' => 'stuff',
    ];

    $message = $this->getMockBuilder( '\Behance\Kong\Model\Mandrill\Message' )
                    ->disableOriginalConstructor()
                    ->setMethods( [ 'hasRecipient' ] )
                    ->getMock();

    $message->expects( $this->once() )
            ->method( 'hasRecipient' )
            ->with( $email )
            ->will( $this->returnValue( false ) );

    $message->addMergeVars( $email, $vars );

    $retrieved = $message->getMergeVars()[0];

    $this->assertEquals( $email, $retrieved['rcpt'] );

    foreach ( $flattened_vars as $name => $content ) {

      foreach ( $retrieved['vars'] as $var ) {
        if ( $var['name'] === $name ) {
          $this->assertEquals( $content, $var['content'] );
        }
      }

    } // foreach flattened vars

  } // testAddMergeVarsValidateSuccessful

  public function testAddRecipientWithMergeVars() {

    $email = uniqid();
    $name  = uniqid();
    $vars  = [ 'hello' => 'world' ];

    $message = $this->getMockBuilder( '\Behance\Kong\Model\Mandrill\Message' )
                    ->disableOriginalConstructor()
                    ->setMethods( [ 'addMergeVars' ] )
                    ->getMock();

    $message->expects( $this->once() )
            ->method( 'addMergeVars' )
            ->with( $email, $vars, false );

    $message->addRecipient( $email, $name, $vars );

  } // testAddRecipientWithMergeVars

  public function testAddRecipientWithoutMergeVars() {

    $message = $this->getMockBuilder( '\Behance\Kong\Model\Mandrill\Message' )
                    ->disableOriginalConstructor()
                    ->setMethods( [ 'addMergeVars' ] )
                    ->getMock();

    $message->expects( $this->never() )
            ->method( 'addMergeVars' );

    $email = uniqid();
    $name  = uniqid();
    $vars  = [];
    $type  = 'bcc';

    $message->addRecipient( $email, $name, $vars, $type );

    $recipient = $message->getRecipients()[0];

    $this->assertEquals( $email, $recipient['email'] );
    $this->assertEquals( $name, $recipient['name'] );
    $this->assertEquals( $type, $recipient['type'] );

  } // testAddRecipientWithoutMergeVars

  /**
   * @expectedException \Behance\Kong\Exception\InvalidTypeException
   */
  public function testAddRecipientException() {

    $message = ( new Kong )->getMandrill()->message();

    $message->addRecipient( uniqid(), uniqid(), [], 'NOT A TYPE' );

  } // testAddRecipientException

  public function testHasRecipientFailure() {

    $message = ( new Kong )->getMandrill()->message();

    $message->addRecipient( 'exists' );

    $this->assertFalse( $message->hasRecipient( 'does_not_exist' ) );

  } // testHasRecipientFailure

  public function testHasRecipientSuccessful() {

    $recipient = uniqid();

    $message = ( new Kong )->getMandrill()->message();

    $message->addRecipient( $recipient );

    $this->assertTrue( $message->hasRecipient( $recipient ) );

  } // testHasRecipientSuccessful

  public function testSendWithTemplate() {

    $type                = 'to';
    $method              = 'POST';
    $subject             = uniqid();
    $from_name           = uniqid();
    $from_email          = uniqid();
    $template_name       = uniqid();
    $template_content    = [ uniqid() => uniqid() ];
    $multiple_recipients = [];

    foreach ( range( 0, 3 ) as $to ) {

      $multiple_recipients[] = [
          'email' => uniqid(),
          'name'  => uniqid(),
          'type'  => $type,
      ];

    } // foreach range

    $single_recipient = array_pop( $multiple_recipients );
    $merged           = array_merge( $multiple_recipients, [ $single_recipient ] );

    $expected_params = [
        'message' => [
            'subject'                   => $subject,
            'from_name'                 => $from_name,
            'from_email'                => $from_email,
            'to'                        => array_values( $merged ),
            'tags'                      => [],
            'google_analytics_domains'  => [],
            'google_analytics_campaign' => null,
        ],
        'template_name'    => $template_name,
        'template_content' => $template_content,
        'async'            => true,
    ];

    $response = $this->_getMockResponse();

    $message = $this->getMockBuilder( '\Behance\Kong\Model\Mandrill\Message' )
                    ->disableOriginalConstructor()
                    ->setMethods( [ '_execute', '_formatResponse' ] )
                    ->getMock();

    $message->expects( $this->once() )
            ->method( '_execute' )
            ->with( $expected_params, Endpoints::MANDRILL_SEND_TEMPLATE, $method )
            ->will( $this->returnValue( $response ) );

    $message->expects( $this->once() )
            ->method( '_formatResponse' )
            ->with( $response );

    $message->setSubject( $subject );
    $message->setFrom( $from_email, $from_name );
    $message->setRecipients( $multiple_recipients );
    $message->setTemplate( $template_name );
    $message->setTemplateContent( $template_content );
    $message->addRecipient( $single_recipient['email'], $single_recipient['name'] );
    $message->setHeaders( [ 'Reply-To' => uniqid() ] );
    $message->setAsync( true );

    $message->send();

  } // testSendWithTemplate

  public function testSendWithoutTemplate() {

    $type                = 'to';
    $method              = 'POST';
    $subject             = uniqid();
    $from_name           = uniqid();
    $from_email          = uniqid();
    $vars                = [ 'hello' => 'world' ];

    $multiple_recipients = [];

    foreach ( range( 0, 3 ) as $to ) {

      $multiple_recipients[] = [
          'email' => uniqid(),
          'name'  => uniqid(),
          'type'  => $type,
      ];

    } // foreach range

    $single_recipient = array_pop( $multiple_recipients );
    $merged           = array_merge( $multiple_recipients, [ $single_recipient ] );

    $expected_vars = [
        'rcpt' => $single_recipient['email'],
        'vars' => [
            [
                'name' => 'hello',
                'content' => 'world',
            ],
        ],
    ];

    $expected_global_vars = [
        [ 'name' => 'hello', 'content' => 'world' ],
    ];

    $tags = [ uniqid() ];
    $domains = [ uniqid() ];
    $campaign = uniqid();

    $expected_params = [
        'message' => [
            'subject'           => $subject,
            'from_name'         => $from_name,
            'from_email'        => $from_email,
            'to'                => array_values( $merged ),
            'merge'             => true,
            'merge_vars'        => [ $expected_vars ],
            'global_merge_vars' => $expected_global_vars,
            'tags'              => $tags,
            'google_analytics_domains'  => $domains,
            'google_analytics_campaign' => $campaign,
        ],
        'async' => false,
    ];

    $response = $this->_getMockResponse();

    $message = $this->getMockBuilder( '\Behance\Kong\Model\Mandrill\Message' )
                    ->disableOriginalConstructor()
                    ->setMethods( [ '_execute', '_sendTemplate' ] )
                    ->getMock();

    $message->expects( $this->once() )
            ->method( '_execute' )
            ->with( $expected_params, Endpoints::MANDRILL_SEND, $method )
            ->will( $this->returnValue( $response ) );

    $message->expects( $this->never() )
            ->method( '_sendTemplate' );

    $message->setSubject( $subject );
    $message->setFrom( $from_email, $from_name );
    $message->setRecipients( $multiple_recipients );
    $message->addRecipient( $single_recipient['email'], $single_recipient['name'], $vars );
    $message->setGlobalMergeVars( $vars );
    $message->setTags( $tags );
    $message->setAnalyticsCampaign( $campaign );
    $message->setAnalyticsDomains( $domains );

    $message->send();

  } // testSendWithoutTemplate

  protected function _getMockResponse( $data = [], $errors = [] ) {

    $response = $this->getMockBuilder( 'Guzzle\Http\Message\Response' )
                     ->disableOriginalConstructor()
                     ->setMethods( [ 'json' ] )
                     ->getMock();

    $response->expects( $this->any() )
             ->method( 'json' )
             ->will( $this->returnValue( [] ) );

    return $response;

  } // _getMockResponse

} // MessageTest
