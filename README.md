#Kong
---
*The MailChimp API wrapper to rule them all.*

Here's some stupid easy things you can do with this bad mamajama:

```
// Grab all of your MailChimp lists in one fell swoop
$kong = new Kong( 'mysecretkey-us1' );
$lists = $kong->getLists();

// Subscribe a user to all of your lists
foreach ( $lists as $list ) {
  $list->subscribe( 'dunphy@adobe.com' );
}

// Subscribe mad users at once to a list (or, why not, all of them):
$users = [
    [
        'email'      => [ 'email' => 'dunphy@adobe.com' ],
        'merge_vars' => [ 'FNAME' => 'Mark', 'LNAME' => 'Dunphy' ],
    ],
    [
        'email'      => [ 'email' => 'markdunphy@gmail.com' ],
        'merge_vars' => [ 'FNAME' => 'Queso', 'LNAME' => 'Fresh' ],
    ],
];

foreach ( $lists as $list ) {
  $list->batchSubscribe( $users );
}

```