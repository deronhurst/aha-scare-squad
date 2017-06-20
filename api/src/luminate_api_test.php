<?php

$base = "https://secure3.convio.net/heartdev/site";

//$signon = $base."/SRConsAPI?method=getSingleSignOnToken&v=1.0&login_name=zuriapi&login_password=heart123&api_key=wDB09SQODRpVIOvX&cons_id=3196123&response_format=json";

$signon = $base ."/SRConsAPI";

$default_params = array(
  "api_key" => "wDB09SQODRpVIOvX",
  "response_format" => "json",
  "v" => "1.0"
  );

$signon_params = array(
  "method" => "getSingleSignOnToken",
  "login_name" => "zuriapi",
  "login_password" => "heart123",
  "cons_id" => "3196123"
  );

$signon_response = curlIt($signon, array_merge($default_params, $signon_params));
$auth = json_decode($signon_response);

if(isset($auth->getSingleSignOnTokenResponse)){
  $default_params['sso_auth_token'] = $auth->getSingleSignOnTokenResponse->token;

  $teamraiser = $base."/CRTeamraiserAPI";

  $current_params = array(
    "method" => "getSurveyResponses",
    "fr_id" => "2520"
    );

  $current_response = curlIt($teamraiser, array_merge($default_params, $current_params));

  $current_survey = json_decode($current_response, true);

  $update_params = array(
    "method" => "updateSurveyResponses",
    "fr_id" => "2520"
    );

  if(isset($current_survey['getSurveyResponsesResponse']) && isset($current_survey['getSurveyResponsesResponse']['responses'])){
    foreach($current_survey['getSurveyResponsesResponse']['responses'] as $response){
      if(isset($response['key'])){
        $update_params['question_key_'.$response['key']] = $response['responseValue'];
      }
    }
  }

  $update_params["question_key_ym_hoops_jump_ecards_sent"] = 5;

  $update_response = curlIt($teamraiser, array_merge($default_params, $update_params));

  var_dump("success?",$update_response); 
}
else{
  echo "no connection";
}

function curlIt($url, $params){

  $query_params = http_build_query($params);  

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER,0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_POST,true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $query_params);
  $response = curl_exec($ch);
  curl_close($ch);

  echo "<hr />";
  var_dump(curl_getinfo($ch));
  echo "<br />";
  var_dump(curl_error($ch));

  return $response;
}


// echo "<hr />";

// ini_set("display_errors",1);
// error_reporting(E_ALL);

// echo "<pre>";
//$url = dirname(__FILE__)."/Convio.wsdl";
// $client = new SoapClient("https://webservices.cluster3.convio.net/1.0/heartdev/wsdl");

// $login = $client->Login(array("UserName"=>"zuriapi","Password"=>"heart123"));
// if(isset($login->Result->SessionId)){
//   $headerBody = array("SessionId"=>$login->Result->SessionId,"PartitionId"=>1001);
//   $header = new SoapHeader("urn:soap.convio.com","Session",$headerBody);
//   $client->__setSoapHeaders($header);
//   //var_dump($header);

//   $response = $client->Query(array(
// //    "QueryString" => "select * from TeamRaiserRegistration where EventId=2520 and Participant.ConsId=3196123",
//     "QueryString" => "select * from TeamRaiserRegistration where EventId=2520",
//     "Page"=> 1,
//     "PageSize"=> 200
//   ));

//   var_dump($response);
  
// }
// else{
//   echo "could not login";
//   var_dump($login);
// }
// echo "</pre>";
