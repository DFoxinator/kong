#Kong
*The MailChimp API wrapper to rule them all.*

---
#Documentation

Kong lets you easily interact with both MailChimp and Mandrill API endpoints from your application. There's a few options for you to get started:

###Getting a MailChimp/Mandrill object
#####Using Kong
```php
// Instantiate a Kong object and then grab a MailChimp or Mandrill object from it.
$kong = new Kong();

$mailchimp = $kong->getMailChimp( $mailchimp_api_key );
$mandrill = $kong->getMandrill( $mandrill_api_key );
```
#####Using MailChimp/Mandrill Directly
```php
$mailchimp = new \Behance\Kong\MailChimp( $mailchimp_api_key );
$mandrill = new \Behance\Kong\Mandrill( $mandrill_api_key );
```

###Using MailChimp

#####Getting a List
```php
$list_id = 'abcd1234';

// The second parameter tells it to make the MailChimp API call to hydrate the object with data about the list.
// This is the default behavior.
$list = $mailchimp->getList( $list_id, true ); // Returns an instance of \Behance\Kong\Model\MailChimp\MailingList

// You can access properties directly on the list object.

echo $list->id; // 'abcd1234'
echo $list->web_id; // 6789
echo $list->name; // 'Super Awesome Campaign'

var_dump( $list->toArray() );

// [
//   'id'                  => '237f949cef',
//   'web_id'              => 260033,
//   'name'                => 'Super Awesome Campaign',
//   'date_created'        => '2014-06-04 19:58:45',
//   'email_type_option'   => false,
//   'use_awesomebar'      => true,
//   'default_from_name'   => 'Default From Name',
//   'default_from_email'  => 'asdf@mailinator.com',
//   'default_subject'     => '',
//   'default_language'    => 'en',
//   'list_rating'         => 3.5,
//   'subscribe_url_short' => 'http://shorter.url.com',
//   'subscribe_url_long'  => 'http://this.is.a.subscribe.url.com',
//   'beamer_address'      => 'somethingsomething@inbound.mailchimp.com',
//   'visibility'          => 'pub',
//   'stats'               => [
//     'member_count'                 => 166412,
//     'unsubscribe_count'            => 29282,
//     'cleaned_count'                => 85,
//     'member_count_since_send'      => 0,
//     'unsubscribe_count_since_send' => 380,
//     'cleaned_count_since_send'     => 85,
//     'campaign_count'               => 1,
//     'grouping_count'               => 0,
//     'group_count'                  => 0,
//     'merge_var_count'              => 2,
//     'avg_sub_rate'                 => 0,
//     'avg_unsub_rate'               => 36550,
//     'target_sub_rate'              => 36656,
//     'open_rate'                    => 27,
//     'click_rate'                   => 2,
//     'date_last_campaign'           => '2014-06-05 14:24:34',
//   ],
//   'modules' => [],
// ]

// If you just want to operate on a list without pulling down the list data, you can pass false as the second parameter.
// This is good for when you just want to subscribe or unsubscribe somebody from the list and don't necessarily
// care about the list data specifically.
$list = $mailchimp->getList( $list_id, false );
var_dump( $list->toArray() );

// [
//   'id' => '237f949cef',
// ]
```

#####Subscribe/Unsubscribe a User
```php
$email = 'asdf@mailinator.com';
$merge_vars = [
  'fname' => 'Mark',
  'lname' => 'Dunphy',
  'city' => 'New York City',
];

// Subscribe accepts an optional associative array of merge vars as the second parameter. You'll
// need to configure these merge vars in MailChimp beforehand.
$list->subscribe( $email, $merge_vars );

// The unsubscribe method takes an email address as it's first parameter. It takes others that you should
// be aware of. Check them out in the API documentation
$list->unsubscribe( $email );
```

#####Batch Subscribe/Unsubscribe
Read the [MailChimp documentation here](http://apidocs.mailchimp.com/api/2.0/lists/batch-subscribe.php) for more information on structuring data.
```php
$users = [
  [
      'email' => [ 'email' => 'asdf@mailinator.com' ],
      'merge_vars' => [
        'fname' => 'Mark',
        'lname' => 'Dunphy',
        'city' => 'New York City',
      ]
  ],
  [
      'email' => [ 'email' => 'billnye@mailinator.com' ],
      'merge_vars' => [
        'fname' => 'Bill',
        'lname' => 'Nye',
        'city' => 'New York City',
      ]
  ],
];

// You can subscribe up to 10,000 users in one operation, but it will be rather slow. If you attempt to
// subscribe MORE than that in one operation, a \Behance\Kong\Exception\MaximumExceededException will be thrown.
$list->batchSubscribe( $users );

$users = [
  [ 'email' => [ 'email' => 'asdf@mailinator.com' ] ],
  [ 'email' => [ 'email' => 'billnye@mailinator.com' ] ],
];

// There are more parameters with default values to this method. Reference the Kong API documentation.
$list->batchUnsubscribe( $users );
```
###Using Mandrill

####Messages

The Mandrill client currently supports a limited number of operations on the Messages API.

#####Getting a Message Object
```php
$mandrill = ( new Kong )->getMandrill( 'some_api_key' );
$message = $mandrill->message();
```

#####Sending HTML Messages
```php
$html = '<h1>Hello world</h1>';
$message->setHtml( $html );
$message->setSubject( 'A really enticing subject' );
$message->addRecipient( 'asdf@mailinator.com', 'Mark Dunphy' );
$message->send();
```

#####Sending Mandrill Templated Messages
```php
$merge_vars = [ 'var1' => 'something!', 'var2' => 'something else!' ];
$message->setGlobalMergeVars( $merge_vars );
$message->setTemplate( 'template-on-mandrill' );
$message->setSubject( 'A really enticing subject' );
$message->addRecipient( 'asdf@mailinator.com', 'Mark Dunphy' );
$message->send();
```

#####Set up Google Analytics
```php
$message->setAnalyticsCampaign( 'my-campaign' );
$message->setAnalyticsDomains( [ 'yourwebsite.net', 'somethingelse.com' ] );
```

#####Set Mandrill Tags
```php
$message->setTags( [ 'welcome-email', 'user-engagement' ] );
```

#####Send Asynchronously (via Mandrill async option)
```php
// This is disabled by default
$message->setAsync( true );
```