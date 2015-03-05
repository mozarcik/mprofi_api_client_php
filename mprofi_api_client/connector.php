<?php

class InvalidTokenException extends Exception {}

class MprofiAPIConnector{


  // Base URL for public API
  public $url_base = 'https://api.mprofi.pl';

  // API version
  public $api_version = '1.0';

  // Name of send endpoint
  public $send_endpoint = 'send';
  
  // Name of sendbulk endpoint
  public $sendbulk_endpoint = 'sendbulk';

  // Name of status endpoint
  public $status_endpoint = 'status';



  // API Token
  private $api_token = '';

  // Store for messages to send
  private $payload = array();

  // CURL instance
  private $curl = NULL;

  // Public constructor
  public function __construct($api_token){
    // check if we can use curl
    if(!function_exists('curl_version')){
      throw new Exception('It seems curl is not installed. Please install php-curl and try again');
    }
    
    // init curl
    $this->curl = curl_init();
    

    $this->api_token = $api_token;


  }

  public function __destruct(){
    if($this->curl){
      curl_close($this->curl);
    }
  }

  public function addMessage($recipient, $message){
    if(!$recipient) {
      throw new InvalidArgumentException('recipient cannot be empty');
    }

    if(!$message){
      throw new InvalidArgumentException('message cannot be empty');
    }

    array_push($this->payload, array('recipient' => $recipient, 'message' => $message));
  }

  public function send($reference=NULL){
    if(count($this->payload) == 1){
      $bulk = false;
      $used_endpoint = $this->send_endpoint;
      $this->payload[0]['reference'] = $reference;
      $this->payload[0]['apikey'] = $this->api_token;
      $encoded_payload = json_encode($this->payload[0]); 

    } elseif(count($this->payload) > 1){
      $bulk = true;
      $used_endpoint = $this->sendbulk_endpoint;
      foreach($this->payload as $key => $value){
        $this->payload[$key]['reference'] = $reference;
      }
      $encoded_payload = json_encode(array(
        'apikey' => $this->api_token,
        'messages' => $this->payload
      )); 
        
    } else {
      throw new Exception('Empty payload. Please use add_message first.');
    }

    $full_url = join('/', array($this->url_base, $this->api_version, $used_endpoint, ''));

  
    curl_setopt($this->curl, CURLOPT_URL, $full_url);
    curl_setopt($this->curl, CURLOPT_POST, true);
    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $encoded_payload);
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($this->curl);
    $httpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    
    if($httpcode != "200"){
      switch($httpcode){
        case "401":
          throw new Exception('API call failed with HTTP ' . $httpcode . ' - make sure the supplied API Token is valid');
          break;

        default:
          throw new Exception('API call failed with HTTP ' . $httpcode);
      }
    }

    $decoded_response = json_decode($response, true);
    
    if($bulk){
      $ids = array();
      foreach($decoded_response['result'] as $result){
        array_push($ids, $result['id']);
      }
      return $ids;
    } else {
      return array($decoded_response['id']);
    }
 
  } 
  
  public function get_status($id){
    $full_url = join('/', array($this->url_base, $this->api_version, $this->status_endpoint, ''));
    $full_url .= '?apikey=' . $this->api_token . '&id=' . $id;
     
 
    curl_setopt($this->curl, CURLOPT_URL, $full_url);
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($this->curl);
    $httpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    
    if($httpcode != "200"){
      switch($httpcode){
        case "401":
          throw new Exception('API call failed with HTTP ' . $httpcode . ' - make sure the supplied API Token is valid');
          break;

        default:
          throw new Exception('API call failed with HTTP ' . $httpcode);
      }
    }

    $decoded_response = json_decode($response, true);
   
    return $decoded_response; 
  }


}

?>
