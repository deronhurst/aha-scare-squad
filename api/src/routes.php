<?php

// Routes

/**
 * Get current progress for a student
 * @param  PSR-7 Request Object $request  [description]
 * @param  PSR-7 Response Object $response [description]
 * @param  Request parameters $args     [description]
 * @return PSR-7 Response Object           [description]
 */
$app->get('/aha_ym18/api/student/register/{teamraiser_id}/{constituent_id}', function ($request,$response,$args) {
  $db = $this->get("db");
  $luminate = $this->get("luminate");

  //check if this registration already exists
  $existing = $db->prepare("SELECT id FROM participants WHERE event_id=? AND constituent_id=?");
  $existing->bind_param("dd", $args['teamraiser_id'], $args['constituent_id']);
  $existing->execute();
  $existing->store_result();

  if($existing->num_rows > 0){
    return;
  }


  $fields = array("event_id","constituent_id");
  $event_id = intval($args['teamraiser_id']);
  $student_id = intval($args['constituent_id']);

  $variable_types = "dd";
  $variable_values = array($event_id, $student_id);

  $params = $request->getQueryParams();

  //Sign in to Luminate as this constituent
  $signon_success = $luminate->signOn(array("cons_id" => $args['constituent_id']));

  //if we don't have team and school as parameters, grab it from Luminate
  $registration = $luminate->getTeamraiserRegistration($event_id);

  if($registration){
    //add the first and last name
    array_push($fields,"first_name","last_name");
    array_push(
      $variable_values,
      $registration['name']['first'],
      $registration['name']['last']
      );
    $variable_types .= "ss";
    
    if(
      !isset($params['team_id']) 
      || !isset($params['school_id'])
      ){
      if(
        !isset($params['team_id'])
        && isset($registration['teamId'])
        ){
        $params['team_id'] = $registration['teamId'];
      }

      if(
        !isset($params['school_id'])
        && isset($registration['companyInformation'])
        && isset($registration['companyInformation']['companyId'])
        ){
        $params['school_id'] = $registration['companyInformation']['companyId'];
      }
    }
  }

  if(isset($params['team_id'])){
    $variable_types .= "d";
    $fields[] = "team_id";
    $variable_values[] = $params['team_id'];
  }

  if(isset($params['school_id'])){
    $variable_types .= "d";
    $fields[] = "school_id";
    $variable_values[] = $params['school_id'];
  }

  //also grab the challenge from Luminate
  $survey = $luminate->getTeamraiserSurveyResponses($event_id);
  if($survey){
    $survey_map = array(
      "ym_hoops_jump_school" => "school",
      "ym_hoops_jump_school_address" => "school_address",
      "ym_hoops_jump_teacher_name" => "teacher",
      "ym_hoops_jump_challenge_taken" => "challenge",
      "ym_hoops_jump_ecards_sent" => "ecards_sent",
      "ym_hoops_jump_ecards_shared" => "ecards_shared",
      "ym_hoops_jump_ecards_open" => "ecards_opened",
      "ym_hoops_jump_ecards_clicked" => "ecards_clicked",
      "ym_hoops_jump_challenge_info" => "challenge_completed"
      );

    $data = array();
    foreach($survey as $question){
      if(isset($survey_map[$question['key']])){
        $data[$survey_map[$question['key']]] = $question['responseValue'];
      }
    }

    if(isset($data['challenge'])){
      $challenge_id = 0;
      preg_match("|([0-9]*)[\.]?(.*)|",$data['challenge'], $challenge);

      $challenge_id = $challenge[1];
      $challenge_text = trim(stripslashes($challenge[2]));

      $variable_types .= "d";
      $fields[] = "challenge_taken";
      $variable_values[] = $challenge_id;

      if($challenge_id){
        //check if the challenge text has changed
        //if so, update it
        $text = $db->prepare("SELECT text FROM dailychallenge_text WHERE id=?");
        $text->bind_param("d",$challenge_id);
        $text->execute();
        $text->bind_result($current_text);
        $text->fetch();
        $text->close();

        if(
          !isset($current_text) 
          || !$current_text
          || $current_text !== $challenge_text
          ){
          $update_text = $db->prepare("INSERT INTO dailychallenge_text (id,text) VALUES(?,?) ON DUPLICATE KEY UPDATE text=?");
          $update_text->bind_param("dss",$challenge_id,$challenge_text,$challenge_text);
          $update_text->execute();
          $update_text->close();
        }
      }
    }
  }

  //call_user_func_array requires variables to be passed by reference
  $student_parameters = array();
  $student_parameters[] = &$variable_types;

  $question_marks = array();
  for($i = 0; $i < count($fields); $i++){
    $question_marks[] = "?";
    $student_parameters[] = &$variable_values[$i];
  }

  $student = $db->prepare("INSERT INTO participants (".join(",",$fields).") VALUES(".join(",",$question_marks).")");
  // $student->bind_param("dd",$event_id,$student_id);
  call_user_func_array(array($student,'bind_param'), $student_parameters);
  $success = $student->execute();

  if($success){
    $data = array(
      "status" => "success"
      );
    $status = 201;

  }
  else{
    $data = array(
      "status" => "error",
      "message" => "Database error"
      );
    $status = 500;
  }

  $newResponse = $response->withJson($data, $status);
  return $newResponse;
// })->add($pixel);
})->add($jsonp);

/**
 * Get the available challenges
 * @param  PSR-7 Request Object $request  [description]
 * @param  PSR-7 Response Object $response [description]
 * @param  Request parameters $args     [description]
 * @return PSR-7 Response Object           [description]
 */
$app->get('/aha_ym18/api/student/challenges/{event_id}/{constituent_id}', function ($request,$response,$args) {
  $db = $this->get("db");
  $select = "SELECT id,text FROM dailychallenge_text";

  $challenges = array();

  $query = $db->prepare($select);
  $query->execute();
  $query->bind_result($id, $text);
  while($query->fetch()){
    $challenges[$id] = $text;
  }

  // $data = array(
  //   "constituent_id" => intval($args['constituent_id']),
  //   "event_id" => intval($args['event_id']),
  //   "challenges" => $challenges
  //   );

  // $newResponse = $this->renderer->render($response, "challenges.phtml", $data);

  $data = array(
    "status" => "success",
    "challenges" => $challenges
    );
  return $response->withJson($data, 201);
})->add($jsonp)->add($auth);

/**
 * Set the user's challenge
 * @param  PSR-7 Request Object $request  [description]
 * @param  PSR-7 Response Object $response [description]
 * @param  Request parameters $args     [description]
 * @return PSR-7 Response Object           [description]
 */
$app->get('/aha_ym18/api/student/challenge/{teamraiser_id}/{constituent_id}', function ($request,$response,$args) {
  $db = $this->get("db");
  $luminate = $this->get("luminate");

  $update = "UPDATE participants SET challenge_taken=? WHERE event_id=? AND constituent_id=?";
  $params = $request->getQueryParams();

  if(!isset($params['challenge'])){
    return $response->withJson(array("status"=>"error","message"=>"Missing challenge"),400);
  }

  $challenge_taken = intval($params['challenge']);

  $challenges = array();

  $query = $db->prepare($update);
  $query->bind_param("ddd", $challenge_taken, $args['teamraiser_id'],$args['constituent_id'] );
  $result = $query->execute();
  $query->close();

  //get the challenge text
  $challenge = $db->prepare("SELECT id,text FROM dailychallenge_text WHERE id=?");
  $challenge->bind_param("d",$challenge_taken);
  $challenge->execute();
  $challenge->bind_result($challenge_id, $challenge_text);
  $challenge->fetch();
  $challenge->close();

  if($challenge_id && $challenge_text){
    $challenge_string = sprintf("%d. %s", $challenge_id, $challenge_text);
    $sync = array(
      "ym_hoops_jump_challenge_taken" => $challenge_string,
      );
    $luminate->signOn(array("cons_id" => $args['constituent_id']));
    $synced = $luminate->updateTeamraiserSurveyResponses($args['teamraiser_id'], $sync);
  }

  $data = array(
    "status" => "success",
    "message" => "Challenge updated"
    );

  $newResponse = $response->withJson($data, 201);
  return $newResponse;
})->add($jsonp)->add($auth);

/**
 * Log an activity for the current user
 * @param  PSR-7 Request Object $request  [description]
 * @param  PSR-7 Response Object $response [description]
 * @param  Request parameters $args     [description]
 * @return PSR-7 Response Object           [description]
 */
$app->get('/aha_ym18/api/student/{teamraiser_id}/{constituent_id}/{choice}', function ($request,$response,$args) {
  $db = $this->get("db");
  $luminate = $this->get("luminate");

  $student = $db->prepare("SELECT id FROM participants WHERE event_id=? AND constituent_id=? LIMIT 1");
  // var_dump($student);
  $student->bind_param("dd",$args['teamraiser_id'],$args['constituent_id']);
  if( $student->execute() ){
    $student->bind_result($student_id);
    $student->fetch();
    $student->close();

    $choice = false;
    preg_match('/^([0-9])/', $args['choice'], $choice);
    $choice = is_array($choice) ? $choice[0] : false;

    $query = $db->prepare("INSERT INTO dailychallenge (participant_id,choice) VALUES (?,?)");
    $query->bind_param("ds",$student_id,$choice);
    $activity_success = $query->execute();
    $query->close();

    $success = ($activity_success && (!isset($team_success) || $team_success));

    $data = array(
      "status" => $success ? "success" : "error"
      );

    //update LO
    $complete = $db->prepare("SELECT COUNT(*) FROM dailychallenge WHERE participant_id=?");
    $complete->bind_param("d",$student_id);
    $complete->execute();
    $complete->bind_result($num_complete);
    $complete->fetch();

    $stats = array(
      "ym_jump_challenge_info" => $num_complete,
      );
    $luminate->signOn(array("cons_id" => $args['constituent_id']));
    $synced = $luminate->updateTeamraiserSurveyResponses($args['teamraiser_id'], $stats);

    $data['synced'] = $synced;
  }

  $newResponse = $response->withJson($data, 201);
  return $newResponse;
})->add($jsonp)->add($auth);

/**
 * Log ecard actvitity to Luminate
 * @param  PSR-7 Request Object $request  [description]
 * @param  PSR-7 Response Object $response [description]
 * @param  Request parameters $args     [description]
 * @return PSR-7 Response Object           [description]
 */
$app->get('/aha_ym18/api/activity/{teamraiser_id}/{constituent_id}/{type}', function($request,$response,$args) {
  $db = $this->get('db');
  $luminate = $this->get('luminate');

  $activity_field = false;
  $field = false;
  $total = false;
  $luminate_key = false;

    // "ym_hoops_jump_ecards_sent" => 3,
    // "ym_hoops_jump_ecards_shared" => 0,
    // "ym_hoops_jump_ecards_open" => 0,
    // "ym_hoops_jump_ecards_clicked" => 0,
    // "ym_hoops_jump_challenge_info" => 0,
    // "ym_hoops_jump_challenge_taken" => 2

  switch($args['type']){
    //has an update script already
    case "ecard_sent":
      $needs_update = false;
      $field = "ecards_sent";
      $luminate_key = "ym_hoops_jump_ecards_sent";
      break;
    case "ecard_opened":
      $needs_update = true;
      $field = "ecards_opened";
      $activity_type = 2;
      $activity_field = "email_open";
      $activity_fieldvalue = 1;
      $luminate_key = "ym_hoops_jump_ecards_open";
      break;
    case "ecard_read":
      $needs_update = true;
      $field = "ecards_read";
      $activity_type = 2;
      $activity_field = "link_visit_open";
      $activity_fieldvalue = 1;
      $luminate_key = "ym_hoops_jump_ecards_clicked";
      break;
    //has an update script already
    case "ecard_shared":
      $needs_update = false;
      $field = "ecards_shared";
      $luminate_key = false;
      $luminate_key = "ym_hoops_jump_ecards_shared";
      break;
    default:
      //if it's not one of the activities, let's stop this
      return;
      break;
  }

  $params = $request->getQueryParams();

  if(isset($needs_update) && $needs_update){
    // $increment = isset($params['increment']) ? intval($params['increment']) : 1;
    // $current_total = 0;
    
    $num = $db->prepare("SELECT COUNT(*) AS total FROM activities WHERE supporter_id=? AND event_id=? AND type=? AND ".$activity_field."=?");
    $num->bind_param("dddd",$args['constituent_id'],$args['teamraiser_id'],$activity_type, $activity_fieldvalue);
    $num->execute();
    $num->bind_result($current_total);
    $num->fetch();
    $num->close();

    $total = $current_total;

    $query = "UPDATE participants SET ".$field."=? WHERE event_id=? AND constituent_id=?";
    $update = $db->prepare($query);
    $update->bind_param("ddd",$total,$args['teamraiser_id'],$args['constituent_id']);
    $update->execute();
    $update->close();
  }

  //for fields we didn't change, let's fetch the result
  if(!$total){
    $result = $db->prepare("SELECT ".$field." FROM participants WHERE event_id=? AND constituent_id=?");
    $result->bind_param("dd",$args['teamraiser_id'],$args['constituent_id']);
    $result->execute();

    $result->bind_result($total);
    $result->fetch();
    $result->close();
  }

  $stat = array();
  $stat[$luminate_key] = $total;
  $luminate->signOn(array("cons_id" => $args['constituent_id']));
  $luminate->updateTeamraiserSurveyResponses($args['teamraiser_id'],$stat);

})->add($auth);

/**
 * Get current progress for a student
 * @param  PSR-7 Request Object $request  [description]
 * @param  PSR-7 Response Object $response [description]
 * @param  Request parameters $args     [description]
 * @return PSR-7 Response Object           [description]
 */
$app->get('/aha_ym18/api/student/{teamraiser_id}/{constituent_id}', function ($request,$response,$args) {

  $db = $this->get('db');
  $query = $db->prepare("SELECT p.id, p.event_id, p.constituent_id, p.ecards_sent, p.ecards_shared, p.challenge_taken, text.text, COUNT(dc.participant_id) AS completed, MAX(dc.date) AS latest "
      ." FROM participants p"
      ." LEFT JOIN dailychallenge dc ON p.id=dc.participant_id"
      ." LEFT JOIN dailychallenge_text text ON p.challenge_taken = text.id"
      ." WHERE p.event_id=? AND p.constituent_id=?"
      ." GROUP BY dc.participant_id, p.id"
      );
  // var_dump($query);
  $query->bind_param("dd", $args['teamraiser_id'], $args['constituent_id']);
  $success = $query->execute();
  $query->store_result();

  if(!$success){
    $http_status = 500;
    $data = array(
      "status"=>"error",
      "message"=> "database error"
    );    
  }
  else{
    if($query->num_rows){
      $query->bind_result($id, $event_id, $constituent_id, $ecards_sent, $ecards_shared, $challenge, $challenge_text, $completed, $latest);
      $query->fetch();

      // var_dump($id, $event_id, $constituent_id, $created, $completed, $latest);
      $yesterday = date('Y-m-d', strtotime('yesterday')) . ' 23:59:59';
      $completedToday = $latest > $yesterday;

      $http_status = 201;

      $data = array(
        "status" => "success",
        "challenges" => array(
          "current" => $challenge,
          "text" => $challenge_text,
          "completed" => $completed,
          "completedToday" => $completedToday
          ),
        "ecards" => array(
          "sent" => $ecards_sent,
          "shared" => $ecards_shared
          )
        );   
    }
    else {
      $http_status = 404;
      $data = array(
        "status" => "error",
        "message" => "Not found"
        );
    }
  }


  $newResponse = $response->withJson($data, $http_status);
  return $newResponse;
})->add($jsonp)->add($auth);


/**
 * Get stats for the program or school
 * @param  PSR-7 Request Object $request  [description]
 * @param  PSR-7 Response Object $response [description]
 * @param  Request parameters $args     [description]
 * @return PSR-7 Response Object           [description]
 */
$app->get('/aha_ym18/api/program[/{type}/[{group_id}]]', function ($request,$response,$args) {
  $db = $this->get("db");
  $select = "SELECT p.challenge_taken, text.text, COUNT(p.challenge_taken) "
    ."FROM participants p "
    ."LEFT JOIN dailychallenge_text text ON p.challenge_taken=text.id";
  $group_by = " GROUP BY challenge_taken";

  if(isset($args['type'])){
    switch($args['type']){
      case "school":
        $where = " WHERE school_id=?";
        break;
      case "team":
        $where = " WHERE team_id=?";
        break;
      case "event":
        $where = " WHERE event_id=?";
        break;        
    }

    $query = $db->prepare($select.$where.$group_by);
    $query->bind_param("d",$args['group_id']);
  }
  else{
    $query = $db->prepare($select.$group_by);
  }

  $results = array();
  $total = 0;

  $query->execute();
  $query->bind_result($challenge, $challenge_text, $count);
  while($query->fetch()){
    $results[$challenge] = array(
      "count" => $count,
      "text" => $challenge_text
      );
    $total += $count;
  }

  $data = array(
    "studentsPledged" => $total,
    "studentsPledgedByActivity" => $results
    );

  $newResponse = $response->withJson($data, 201);
  return $newResponse;
})->add($jsonp)->add($auth);