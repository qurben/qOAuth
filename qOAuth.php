<?php
require_once('curl.php');

class QOAuth {
  private $consumer_key;
  private $consumer_secret;

  private $config = array();

  private $useragent;

  const twitter = 90001;
  const tumblr = 90002;
  const google = 90003;

  private $configs = array(
    90001 => array(
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
      ),
    90002 => array(
      'host' => 'www.tumblr.com',
      'oauth' => array(
        'authorize_url' => 'http://www.tumblr.com/oauth/authorize',
        'access_token_url' => 'http://www.tumblr.com/oauth/access_token',
        'request_token_url' => 'http://www.tumblr.com/oauth/request_token'
        )
      ),
    90003 => array(
      'oauth' => array(
        'authorize_url' => 'https://')
      )
    );

  function __construct($consumer_key, $consumer_secret, $config) {
    $this->consumer_key = $consumer_key;
    $this->consumer_secret = $consumer_secret;

    if(is_numeric($config)) {
      $this->config = $this->configs[$config];
    } else {

      $this->oauth['authorize_url'] = $config['oauth']['authorize_url'];
      $this->oauth['access_token_url'] = $config['oauth']['access_token_url'];
      $this->oauth['request_token_url'] = $config['oauth']['request_token_url'];

      if(isset($config['oauth2'])) {
        $this->oauth2['bearer_token_url'] = $config['oauth2']['bearer_token_url'];
        $this->oauth2['invalidate_token_url'] = $config['oauth2']['invalidate_token_url'];
      }
    }
  }

  public function getBearerToken($grant_type='client_credentials') {
    $encoded_consumer_key = urlencode($this->consumer_key);
    $encoded_consumer_secret = urlencode($this->consumer_secret);

    $bearer_token = $encoded_consumer_key.':'.$encoded_consumer_secret;

    $base64_encoded_bearer_token = base64_encode($bearer_token);

    $headers = array( 
        "POST /oauth2/token HTTP/1.1", 
        "Host: api.twitter.com", 
        "User-Agent: ".$this->useragent,
        "Authorization: Basic ".$base64_encoded_bearer_token."",
        "Content-Type: application/x-www-form-urlencoded;charset=UTF-8", 
        "Content-Length: 29",
        "Accept-Encoding: gzip"
    ); 

    $ch = new CURL($this->config['oauth2']['bearer_token_url'],'POST',array('grant_type'=>$grant_type));

    $ch->setHeader($headers);

    $ch->setOption(CURLOPT_RETURNTRANSFER,TRUE);
    $ch->setOption(CURLOPT_POSTFIELDS,"grant_type=$grant_type");
    $ch->setOption(CURLOPT_HEADER,FALSE);

    $result = $ch->exec()->getResponse();

    $ch->close();

    $data = json_decode($result);
    $bearer = $data->access_token;

    $this->config['bearer_token'] = $bearer;

    return $bearer;
  }

  function fetch($url,$parameters = '') {
    $headers = array(
      "GET /1.1/statuses/user_timeline.json HTTP/1.1",
      "Host: ".$this->config['host'],
      "User-Agent: My Twitter App v1.0.23",
      "Authorization: Bearer ".$this->config['bearer_token'],
      "Accept-Encoding: gzip"
      );

    $ch = new CURL($url,'GET',$parameters);

    $ch->setHeader($headers);

    $ch->setOption(CURLOPT_RETURNTRANSFER,true);
    $ch->setOption(CURLOPT_HEADER,false);

    $result = $ch->exec()->getResponse();

    return $result;
  }
}