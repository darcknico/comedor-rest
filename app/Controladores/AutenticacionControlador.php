<?php

namespace App\Controladores;


use App\Modelos\Usuario;
use App\Controladores\Controlador;
use OAuth2;
use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;

class AutenticacionControlador extends Controlador
{
  public function inicio($request,$response){
    return $response->withStatus(404)->withJson(
      [
        'resultado' => 'Ruta Inexistente',
        'salida'=> 'Ingrese a algun recurso correcto',
        'numfilas' =>0
      ]
    );
  }
  public function token($request,$response){
    $oauth2Request = RequestBridge::toOAuth2($request);
    $res = new OAuth2\Response();
    $oauth2Response = $this->server->handleTokenRequest($oauth2Request);
    //return ResponseBridge::fromOAuth2($oauth2Response);
    return $oauth2Response->send();
  }
  public function autorizar($request,$response){
    $oauth2Request = RequestBridge::toOAuth2($request);
    $oauth2Response = new OAuth2\Response();
    if (!$this->server->validateAuthorizeRequest($oauth2Request, $oauth2Response)) {
        return $oauth2Response->send();
    }
    $this->server->handleAuthorizeRequest($oauth2Request, $oauth2Response, true,123);
    //return substr($oauth2Response->getHttpHeader('Location'), strpos($oauth2Response->getHttpHeader('Location'), 'code=')+5, 40);
    //return ResponseBridge::fromOAuth2($oauth2Response);
    return $oauth2Response->send();
  }

  public function recurso($request,$response){
    $respuesta = $this->http->post('token',[
      'json' => [
        "grant_type"=>"client_credentials",
      	"client_id"=>"testclient",
      	"client_secret"=>"testpass"
      ]
    ]);
    $json = json_decode($respuesta->getBody(), true);
    $token = $json['token_type'].' '.$json['access_token'];

    return $token;
  }

  public function acceder($request,$response)
	{
    if($request->getAttribute('has_errors')){
      return $response->withStatus(400)->withJson(
        [
          'resultado' => 'Error en los Datos',
          'salida'=>$request->getAttribute('errors'),
          'numfilas' =>0
        ]
      );
    }
    $input = $request->getParsedBody();
		try {
			$todos = Usuario::where('usu_dni',$input['dni'])->first();
      if($todos) {
        if(password_verify($input['contraseña'],$todos->getContraseña())) {
          return $response->withJson(
            [
              'resultado' => "Exito",
              'salida' => $todos,
              'numfilas' => 1,
              //'Authorization'=>$this->server->grantAccessToken($req, $res)
            ]
          );
        }
        return $response->withStatus(401)->withJson(
          [
            'resultado' => "Contraseña Incorrecta",
            //'salida' => "{}",
            'numfilas' => 0
          ]
        );
      } else {
        return $response->withStatus(401)->withJson(
          [
            'resultado' => "Cuenta Inexistente",
            //'salida' => "{}",
            'numfilas' => 0
          ]
        );
      }
		} catch (\Illuminate\Database\QueryException $e) {
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "Error en Base de Datos",
          'salida' => $e->getMessage(),
          'numfilas' => 0
        ]
      );
		} catch (\Exception $e) {
      return $response->withStatus(501)->withJson(
        [
          'resultado' => "Error no definido",
          'salida' => $e->getMessage(),
          'numfilas' => 0
        ]
      );
		}
	}

	public function registrar($request,$response)
	{
    if($request->getAttribute('has_errors')){
      return $response->withStatus(400)->withJson(
        [
          'resultado' => 'Error en los Datos',
          'salida'=>$request->getAttribute('errors'),
          'numfilas' =>0
        ]
      );
    }
    $input = $request->getParsedBody();
		try{
			$todos = Usuario::create([
        'usu_apellido'=>$input['apellido'],
				'usu_nombre'=>$input['nombre'],
        'usu_dni'=>$input['dni'],
        'usu_contraseña'=> password_hash($input['contraseña'],PASSWORD_DEFAULT),
        'usu_token'=> md5(microtime() . rand())
				]);
        return $response->withStatus(201)->withJson(
          [
            'resultado' => "Creacion con Exito",
            'salida' => $todos,
            'numfilas' => 1
          ]
        );
		} catch (\Illuminate\Database\QueryException $e) {
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "Error en Base de Datos",
          'salida' => $e->getMessage(),
          'numfilas' => 0
        ]
      );
		} catch (\Exception $e) {
      return $response->withStatus(501)->withJson(
        [
          'resultado' => "Error no definido",
          'salida' => $e->getMessage(),
          'numfilas' => 0
        ]
      );
		}
	}
}
