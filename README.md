qOAuth
======

Usage
-----
This script currently only supports fetching an OAuth 2.0 bearer token and fetching a page with it.

### Configuration

This script comes with some standard configurations

* `QOAuth::twitter`
* `QOAuth::tumblr`

You can also use an array as config, this array must have the following values:

```PHP
$config => array( // Twitter example
  'host' => 'api.twitter.com',
  'oauth' => array(
      'authorize_url' => 'https://api.twitter.com/oauth/authorize',
      'access_token_url' => 'https://api.twitter.com/oauth/access_token',
      'request_token_url' => 'https://api.twitter.com/oauth/request_token',
    ),
  'oauth2' => array(
      'bearer_token_url' => 'https://api.twitter.com/oauth2/token',
      'invalidate_token_url' => 'https://api.twitter.com/oauth2/invalidate_token_url'
    )
  )
```

### Constructor

The constructor takes three arguments, the consumer key, the consumer secret and the configuration, the consumer key and secret are obtained from your OAuth provider's website.

### Fetching a token

To fetch a bearer token use the getBearerToken function, the first argument is the type of access you want, for this check the provider's documentation

### Fetching a page

To fetch a page use the fetch function, the first argument is the url to fetch.

### Example

```PHP
$o = new QOAuth($conskey, $conssec, QOAuth::twitter);

$token = $o->getBearerToken('client_credentials');

$url = "https://api.twitter.com/1.1/statuses/user_timeline.json?count=100&screen_name=twitter";

$result = $o->fetch($url);

$json = json_decode($result);

foreach($json as $tweet) {
  echo "<p>",$tweet->text,"</p>";
}
```

Notes
-----

This script is highly unstable and far from finished, do not use it unless you want to just play with OAuth.
