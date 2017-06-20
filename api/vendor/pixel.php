<?php
/**
 * Image Middleware Class
 *
 * @author  Tom van Oorschot <tomvanoorschot@gmail.com>
 * @since  17-12-2012
 *
 * Simple class to wrap the response of the application in a JSONP callback function.
 * The class is triggered when a get parameter of callback is found   
 *
 * Usage
 * ====
 * 
 * $app = new \Slim\Slim();
 * $app->add(new \Slim\Extras\Middleware\JSONPMiddleware());
 * 
 */

$pixel = function ($request, $response, $next) {
    $response = $next($request,$response);
    $contentType = $response->getHeader('Content-type');

    $image = file_get_contents('../src/tracker.gif');

    $responseBody = $response->getBody();
    $responseBody->rewind();
    $responseBody->write($image);
    return $response->withHeader("Content-Type","image/gif");

    return $response;
};