<?php namespace behance\Kong\Model\Mandrill;

use \behance\Kong\Model;
use \behance\Kong\Endpoints;
use \behance\Kong\Exception\InvalidTypeException;

class Message extends Model {

  /**
   * Supported recipient types.
   *
   * @var array
   */
  protected static $_RECIPIENT_TYPES = [ 'to', 'cc', 'bcc' ];

  /**
   * The sender's email
   */
  protected $_from_email;
  protected $_from_name;

  /**
   * An array of recipients
   *
   * @var array
   */
  protected $_recipients = [];

  /**
   * An array of merge vars.
   *
   * @var array
   */
  protected $_merge_vars = [];

  /**
   * The name (slug) of the template to use.
   *
   * @var string
   */
  protected $_template_name;

  /**
   * The content to send to the template.
   *
   * @var array
   */
  protected $_template_content = [ [ 'name' => 'dummy', 'content' => 'data' ] ];

  /**
   * The email subject.
   *
   * @var string
   */
  protected $_subject;

  /**
   * Send this message.
   */
  public function send() {

    $params = [
        'message' => [
            'subject'    => $this->_subject,
            'from_name'  => $this->_from_name,
            'from_email' => $this->_from_email,
            'to'         => $this->_recipients
        ],
    ];

    if ( !empty( $this->_merge_vars ) ) {
      $params['message']['merge'] = true;
      $params['message']['merge_vars'] = $this->_merge_vars;
    }

    if ( !empty( $this->_template_name ) ) {
      return $this->_sendTemplate( $params );
    }

    $response = $this->_execute( $params, Endpoints::MANDRILL_SEND, 'POST' );

    return $this->_formatResponse( $response );

  } // send

  /**
   * Specify the name (slug) of the template to use.
   *
   * @param string $name
   *
   * @return \behance\Kong\Model\Mandrill\Message
   */
  public function setTemplate( $name ) {

    $this->_template_name = $name;

    return $this;

  } // setTemplate

  /**
   * Sets the template content.
   *
   * @param array $content
   *
   * @return \behance\Kong\Model\Mandrill\Message
   */
  public function setTemplateContent( array $content ) {

    $this->_template_content = $content;

  } // setTemplateContent

  /**
   * Set the user this is being sent from.
   *
   * @param string $email
   * @param string $name
   *
   * @return \behance\Kong\Model\Mandrill\Message
   */
  public function setFrom( $email = null, $name = null ) {

    $this->_from_email = $email;
    $this->_from_name  = $name;

    return $this;

  } // setFrom

  /**
   * Set the recipients array. See documentation on Mandrill's website
   * for what each recipient should look like.
   *
   * https://mandrillapp.com/api/docs/messages.html
   *
   * @param array $recipients
   *
   * @return \behance\Kong\Model\Mandrill\Message
   */
  public function setRecipients( array $recipients = [] ) {

    $this->_recipients = $recipients;

    return $this;

  } // setRecipients

  /**
   * Add a recipient.
   *
   * @param string $email
   * @param string $name
   * @param array  $merge_vars
   * @param string $type
   *
   * @return \behance\Kong\Model\Mandrill\Message
   */
  public function addRecipient( $email, $name = null, array $merge_vars = [], $type = 'to' ) {

    if ( !$this->_validateRecipientType( $type ) ) {
      throw new InvalidTypeException( '[type] must be set to one of "' . implode( ', ', static::$_RECIPIENT_TYPES ) . '." Received "' . $type . '"' );
    }

    $this->_recipients[] = [
        'email' => $email,
        'name'  => $name,
        'type'  => $type,
    ];

    if ( !empty( $merge_vars ) ) {
      $this->addMergeVars( $email, $merge_vars, false );
    }

    return $this;

  } // addRecipient

  /**
   * Add a set of merge vars for a specific user.
   *
   * @param string  $email
   * @param array   $merge_vars key value pairs of merge vars
   * @param boolean $validate whether or not to validate the $email exists as a recipient
   *
   * @return \behance\Kong\Model\Mandrill\Message
   */
  public function addMergeVars( $email, array $merge_vars, $validate = true ) {

    if ( $validate && !$this->hasRecipient( $email ) ) {
      return false;
    }

    $vars = $this->_flatten( $merge_vars );

    $this->_merge_vars[] = [
        'rcpt' => $email,
        'vars' => $this->_formatMergeVars( $vars ),
    ];

    return $this;

  } // addMergeVars

  /**
   * Set the subject.
   *
   * @param string $subject
   *
   * @return \behance\Kong\Model\Mandrill\Message
   */
  public function setSubject( $subject ) {

    $this->_subject = $subject;

    return $this;

  } // setSubject

  /**
   * Check if a recipient exists.
   *
   * @param string $email
   *
   * @return boolean
   */
  public function hasRecipient( $email ) {

    foreach ( $this->_recipients as $recipient ) {
      if ( $recipient['email'] === $email ) {
        return true;
      }
    }

    return false;

  } // hasRecipient

  /**
   * Send a transactional email using a hosted template.
   *
   * @param array $params
   *
   * @return
   */
  protected function _sendTemplate( array $params ) {

    $params['template_name']    = $this->_template_name;
    $params['template_content'] = $this->_template_content;

    $response = $this->_execute( $params, Endpoints::MANDRILL_SEND_TEMPLATE, 'POST' );
    die( var_dump( $response->json() ) );
    return $this->_formatResponse( $response );

  } // _sendTemplate

  /**
   * Validate a recipient type.
   *
   * @param string $type
   *
   * @return boolean
   */
  protected function _validateRecipientType( $type ) {

    return ( in_array( $type, static::$_RECIPIENT_TYPES ) );

  } // _validateRecipientType

  /**
   * Format an associative array into an array of merge vars.
   *
   * @param array $merge_vars
   */
  protected function _formatMergeVars( array $merge_vars ) {

    $formatted = [];

    foreach ( $merge_vars as $name => $content ) {
      $formatted[] = [
          'name'    => $name,
          'content' => $content,
      ];
    } // foreach merge vars

    return $formatted;

  } // _formatMergeVars

  /**
   * Recursively flattens arrays of any depth into a single level array
   * so that they can be used as merge vars.
   *
   * An array like [ 'hello' => [ 'world', 'friend', 'mom' ] ] will be
   * flattened into [ 'hello_0' => 'world', 'hello_1' => 'friend', 'hello_2' => 'mom' ]
   *
   * @param array $data
   *
   * @return array
   */
  protected function _flatten( array $data, $key = null ) {

    $flattened = [];

    foreach ( $data as $index => $value ) {

      if ( is_array( $value ) ) {

        $new_key = ( !is_null( $key ) )
                   ? $key . '_' . $index
                   : $index;

        $results = $this->_flatten( $value, $new_key );
        $flattened = array_merge( $results, $flattened );

      } // if is array value

      elseif ( !empty( $key ) ) {
        $flattened[ $key . '_' . $index ] = $value;
      }

      else {
        $flattened[ $index ] = $value;
      }

    } // foreach data

    return $flattened;

  } // _flatten

} // Message
