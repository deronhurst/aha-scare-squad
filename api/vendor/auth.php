<?php
/**
 * JSONP Middleware Class for the Slim Framework
 *
 * @author  Senning Luk
 * 
 */

$auth = function ($request, $response, $next) {
    $params = $request->getQueryParams();
    $apiKey = $this->get("settings")['key']['api'];

    if(!isset($params['key']) || $params['key'] !== $apiKey){
        $error = array(
            'success' => false,
            'error' => "Not authorized"
            );
        $newResponse = $response->withJson($error,403);
        return $newResponse;
    }
    else{
        $response = $next($request,$response);

        return $response;
    }
};
