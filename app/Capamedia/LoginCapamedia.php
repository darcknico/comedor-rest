<?php

namespace App\Capamedia;

use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;
/**
*
*/
class LoginCapamedia extends Capamedia{

  public function __invoke($request, $response, $next)
	{
    $oauth2Request = RequestBridge::toOAuth2($request);
    if (!$this->container->server->verifyResourceRequest($oauth2Request)) {
        $oauth2Response = $this->container->server->getResponse();
        return ResponseBridge::fromOAuth2($oauth2Response);
    }
  	return $next($request, $response);
	}
}
