<?php namespace Behance\Kong\Model\Mandrill;

use \Behance\Kong\Model;
use \Behance\Kong\Endpoints;
use \Behance\Kong\Exception\InvalidTypeException;
use \Guzzle\Http\Message\Response;

/**
 * Documentation for most functionality in this class can be found at:
 *
 * https://mandrillapp.com/api/docs/messages.html
 */
class Message extends Model {

  /**
   * Supported recipient types.
   *
   * @var array
   */
  protected static $_RECIPIENT_TYPES = [ 'to', 'cc', 'bcc' ];

  /**
   * Client string.
   *
   * @var string
   */
  protected $_client_string = 'Mandrill';

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
   * Specialized email headers.
   *
   * @var array key/value pairs
   */
  protected $_headers = [];

  /**
   * Google analytics domains to attach
   * query strings to.
   *
   * @var array an array of strings
   */
  protected $_analytics_domains = [];

  /**
   * Google analytics campaign to use with query strings.
   * Required with analytics domains.
   *
   * @var string
   */
  protected $_analytics_campaign;

  /**
   * Tags used for statistics on Mandrill.
   *
   * @var array an array of strings
   */
  protected $_tags = [];

  /**
   * Whether to enable background sending on Mandrill's side.
   *
   * @var boolean
   */
  protected $_async = false;

  /**
   * A string in the format of YYYY-MM-DD HH:MM:SS
   * to send the message at. If this is null, the email
   * will send instantly.
   *
   * @var string
   */
  protected $_send_at;

  /**
   * Send this message.
   */
  public function send() {

    $params = [
        'message' => [
            'subject'                   => $this->_subject,
            'from_name'                 => $this->_from_name,
            'from_email'                => $this->_from_email,
            'to'                        => $this->getRecipients(),
            'tags'                      => $this->getTags(),
            'google_analytics_domains'  => $this->getAnalyticsDomains(),
            'google_analytics_campaign' => $this->getAnalyticsCampaign(),
        ],
        'async' => $this->getAsync(),
    ];

    if ( !empty( $this->_merge_vars ) ) {
      $params['message']['merge'] = true;
      $params['message']['merge_vars'] = $this->_merge_vars;
    }

    if ( !empty( $this->_global_merge_vars ) ) {
      $params['message']['global_merge_vars'] = $this->_global_merge_vars;
    }

    if ( !empty( $this->_template_name ) ) {
      return $this->_sendTemplate( $params );
    }

    if ( !empty( $this->_html ) ) {
      $params['message']['html'] = $this->_html;
    }

    if ( !empty( $this->_send_at ) ) {
      $params['send_at'] = $this->_send_at;
    }

    $response = $this->_execute( $params, Endpoints::MANDRILL_SEND, 'POST' );

    return $this->_formatResponse( $response );

  } // send

  /**
   * Set the HTML content of the email. Only used when a template
   * is not specified.
   *
   * @param string $html
   */
  public function setHtml( $html ) {

    $this->_html = $html;

  } // setHtml

  /**
   * Get the HTML content of the email if no template is being used.
   *
   * @return string
   */
  public function getHtml() {

    return $this->_html;

  } // getHtml

  /**
   * Specify the name (slug) of the template to use.
   *
   * @param string $name
   *
   * @return \Behance\Kong\Model\Mandrill\Message
   */
  public function setTemplate( $name ) {

    $this->_template_name = $name;

  } // setTemplate

  /**
   * Sets the template content.
   *
   * @param array $content
   *
   * @return \Behance\Kong\Model\Mandrill\Message
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
   * @return \Behance\Kong\Model\Mandrill\Message
   */
  public function setFrom( $email = null, $name = null ) {

    $this->_from_email = $email;
    $this->_from_name  = $name;

  } // setFrom

  /**
   * Set the recipients array. See documentation on Mandrill's website
   * for what each recipient should look like.
   *
   * https://mandrillapp.com/api/docs/messages.html
   *
   * @param array $recipients
   *
   * @return \Behance\Kong\Model\Mandrill\Message
   */
  public function setRecipients( array $recipients = [] ) {

    $this->_recipients = $recipients;

  } // setRecipients

  /**
   * Add a recipient.
   *
   * @param string $email
   * @param string $name
   * @param array  $merge_vars
   * @param string $type
   *
   * @return \Behance\Kong\Model\Mandrill\Message
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

  } // addRecipient

  /**
   * @return string
   */
  public function getAnalyticsCampaign() {

    return $this->_analytics_campaign;

  } // getAnalyticsCampaign

  /**
   * @param string $campaign
   */
  public function setAnalyticsCampaign( $campaign ) {

    $this->_analytics_campaign = $campaign;

  } // setAnalyticsCampaign

  /**
   * @return array
   */
  public function getAnalyticsDomains() {

    return $this->_analytics_domains;

  } // getAnalyticsDomains

  /**
   * Set the google analytics domains.
   *
   * @param array $domains
   */
  public function setAnalyticsDomains( array $domains ) {

    $this->_analytics_domains = $domains;

  } // setAnalyticsDomains

  /**
   * Add a set of merge vars for a specific user.
   *
   * @param string  $email
   * @param array   $merge_vars key value pairs of merge vars
   * @param boolean $validate whether or not to validate the $email exists as a recipient
   *
   * @return \Behance\Kong\Model\Mandrill\Message
   */
  public function addMergeVars( $email, array $merge_vars, $validate = true ) {

    if ( $validate === true && $this->hasRecipient( $email ) ) {
      return false;
    }

    $vars = $this->_flatten( $merge_vars );

    $this->_merge_vars[] = [
        'rcpt' => $email,
        'vars' => $this->_formatMergeVars( $vars ),
    ];

  } // addMergeVars

  /**
   * Set the subject.
   *
   * @param string $subject
   *
   * @return \Behance\Kong\Model\Mandrill\Message
   */
  public function setSubject( $subject ) {

    $this->_subject = $subject;

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
   * @return array
   */
  public function getRecipients() {

    return $this->_recipients;

  } // getRecipients

  /**
   * @return array
   */
  public function getMergeVars() {

    return $this->_merge_vars;

  } // getMergeVars

  /**
   * Set the email headers.
   *
   * @param array $headers
   */
  public function setHeaders( array $headers ) {

    $this->_headers = $headers;

  } // setHeaders

  public function setGlobalMergeVars( array $vars = [] ) {

    $this->_global_merge_vars = $this->_formatMergeVars( $this->_flatten( $vars ) );

  } // setGlobalMergeVars

  /**
   * @return array
   */
  public function getTags() {

    return $this->_tags;

  } // getTags

  /**
   * Set the analytics tags.
   *
   * @param array $tags
   */
  public function setTags( $tags ) {

    $this->_tags = $tags;

  } // setTags

  /**
   * @return boolean
   */
  public function getAsync() {

    return $this->_async;

  } // getAsync

  /**
   * @param boolean $async
   */
  public function setAsync( $async ) {

    $this->_async = $async;

  } // setAsync

  /**
   * @return string
   */
  public function getSendTime() {

    return $this->_send_at;

  } // getSendTime

  /**
   * A datetime string in the format of YYYY-MM-DD HH:MM:SS
   *
   * @param string $datetime
   */
  public function setSendTime( $datetime ) {

    $this->_send_at = $datetime;

  } // setSendTime

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

    /**
   * Format the raw API response into an array of Models.
   *
   * @param Guzzle\Http\Message\Response $response api response
   *
   * @return array
   */
  protected function _formatResponse( Response $response ) {

    $body = $response->json();

    return $body;

  } // _formatResponse

} // Message
