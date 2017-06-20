<?php

namespace Luminate;

class Rest {
  public $debug = false;
  public $endpoint = array(
    'constituent' => "SRConsAPI",
    'teamraiser' => "CRTeamraiserAPI"
    );
  private $auth = false;


  /**
   * [__construct description]
   * @param [type] $config [description]
   */
  function __construct($config){
    $default = array(
      "url_base" => false, //i.e. "https://secure3.convio.net/heartdev/site/"
      "api_key" => false,
      "response_format" => "json",
      "v" => "1.0"
      );

    if(!is_array($config)){
      $this->settings = $default;
      return false;
    }
    else {
      $this->settings = array_merge($default, $config);
    }
  }

  private function constructParameters($params){
    $default = array(
      'api_key' => $this->settings['api_key'],
      'response_format' => $this->settings['response_format'],
      'v' => $this->settings['v']
      );

    return array_merge($default, $params);
  }

  /**
   * [signon description]
   * @param  [type] $config [description]
   * @return [type]         [description]
   */
  function signOn($config){
    $default = array(
      "method" => "getSingleSignOnToken",
      "login_name" => $this->settings['login_name'],
      "login_password" => $this->settings['login_password']
      );

    if(!is_array($config)){
      return false;
    }

    $url = $this->settings['url_base'] . $this->endpoint['constituent'];
    $params = $this->constructParameters(array_merge($default, $config));
    $response = $this->curlIt($url, $params);
    if(isset($response['getSingleSignOnTokenResponse'])){

      $this->auth = $response['getSingleSignOnTokenResponse'];
      return true;
    }
    else{
      return false;
    }
  }

  /**
   * [getTeamraiserRegistration description]
   * @param  [type] $teamraiser_id [description]
   * @return [type]                [description]
   */
  function getTeamraiserRegistration($teamraiser_id){

  }

  /**
   * Get a signed-in user's survey responses
   * A token is expected, created by the signOn() method first
   * 
   * @param  Int $teamraiser_id The Luminate ID for the Teamraiser event
   * @return Array              All current survey responses
   */
  function getTeamraiserSurveyResponses($teamraiser_id){
    if(
      !$this->auth
      || !isset($this->auth['token'])
      ){
      return false;
    }

    $config = array(
      "method" => "getSurveyResponses",
      "fr_id" => intval($teamraiser_id),
      "sso_auth_token" => $this->auth['token']
      );

    $url = $this->settings['url_base'] . $this->endpoint['teamraiser'];
    $params = $this->constructParameters($config);
    $response = $this->curlIt($url, $params);

    if(
      isset($response['getSurveyResponsesResponse'])
      && isset($response['getSurveyResponsesResponse']['responses'])
      ){
      return $response['getSurveyResponsesResponse']['responses'];
    }
    else{
      return false;
    }
  }

  /**
   * Update a signed-in users's survey responses
   * A token is expected, created by the signOn() method first
   * 
   * @param  [type] $teamraiser_id [description]
   * @param  [type] $responses     [description]
   * @return [type]                [description]
   */
  function updateTeamraiserSurveyResponses($teamraiser_id, $responses){
    if(
      !$this->auth
      || !isset($this->auth['token'])
      ){
      return false;
    }

    $teamraiser_id = intval($teamraiser_id);

    $config = array(
      "method" => "updateSurveyResponses",
      "fr_id" => $teamraiser_id,
      "sso_auth_token" => $this->auth['token']
      );

    //Luminate requires all keyed survey questions to be sent, even ones that aren't being changed
    //Get the current survey responses and add to the parameters
    $current_responses = $this->getTeamraiserSurveyResponses($teamraiser_id);

    if(is_array($current_responses)){
      foreach($current_responses as $response){
        if(isset($response['key'])){
          $this->prependQuestionKey($config, $response['key'], $response['responseValue']);
        }
      }
    }

    foreach($response as $key => $value){
      $this->prependQuestionKey($config, $key, $value);
    }

    //boilerplate
    $url = $this->settings['url_base'] . $this->endpoint['teamraiser'];
    $params = $this->constructParameters($config);
    $response = $this->curlIt($url,$params);

    if($response && isset($response['updateSurveyResponsesResponse'])){
      return $response['updateSurveyResponsesResponse']['success'] === "true";
    }
    else{
      return false;
    }

  }

  function prependQuestionKey(&$list, $key, $value){
    $list['question_key_'.$key] = $value;
  }

  /**
   * [curlIt description]
   * @param  [type] $url    [description]
   * @param  [type] $params [description]
   * @return [type]         [description]
   */
  function curlIt($url, $params){
    $query_params = http_build_query($params);  

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_params);
    $response = curl_exec($ch);

    if($this->debug){
      echo "<hr />";
      var_dump($response);
      echo "<hr />";
      var_dump(curl_getinfo($ch));
      echo "<br />";
      var_dump(curl_error($ch));
      echo "<hr />";
    }

    curl_close($ch);

    if(
      isset($params['response_format'])
      && $params['response_format'] === "json"
      ){
      $response = json_decode($response, true);

      if(!is_array($response)){
        $response = array();
      }
    }

    return $response;    
  }
}