<?php

namespace App\Capamedia;

/**
*
*/
class LoginCapamedia extends Capamedia{

  public function __invoke($request, $response, $next)
	{
    // Validate the user credentials
  	//$userId = MyUserService::getUserIdIfValidCredentials($request);
  	//if ($userId === false) {
  		//return $response->withStatus(303);
  	//}

  	//Put user_id into the route parameters
  	$route = $request->getAttribute('route');
  	$route->setArgument('user_id', 1234);

  	//Credentials are valid, continue so the authorization code can be sent to the clients callback_uri
  	return $next($request, $response);
	}
}
