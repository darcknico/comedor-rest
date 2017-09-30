<?php

namespace App\Controladores;


use App\Modelos\Usuario;
use App\Controladores\Controlador;
use OAuth2;
use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;

/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="http://vps142351.vps.ovh.ca/proyectos/comedor-rest/public",
 *     basePath="/",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="AppComedor REST",
 *         description="Este es un simple servidor para la gestion del Ticket en la compra de menus en el comedor Universitario.",
 *         @SWG\Contact(
 *             email="nicolasrl2005@gmail.com"
 *         )
 *     )
 * )
 */

/**
 * @SWG\SecurityScheme(
 *   securityDefinition="comedor_auth",
 *   type="oauth2",
 *   authorizationUrl="http://localhost/proyectos/comedor-rest/public/authorize",
 *   tokenUrl="http://localhost/proyectos/comedor-rest/public/token",
 *   flow="password",
 *   scopes={
 *     "basico": "Alumno",
 *     "medio": "?",
 *     "completo": "Administrador"
 *   }
 * )
 */

 /**
 * @SWG\Tag(
 *   name="autenticacion",
 *   description="Sistema de autenticacion en la api"
 * )
 * @SWG\Tag(
 *   name="usuario",
 *   description="Procedmientos para el usuario"
 * )
 * @SWG\Tag(
 *   name="menu",
 *   description="Todo lo que tenga que ver acerca de la minuta"
 * )
 * @SWG\Tag(
 *   name="ticket",
 *   description="Controlador de los tickets comprados por los menus"
 * )
 * @SWG\Tag(
 *   name="transaccion",
 *   description="Operaciones acerca del pago o recarga de saldo"
 * )
 */
class AutenticacionControlador extends Controlador
{
  public function inicio($request,$response){
    return $this->view->render($response,'index.twig');
  }
  public function swagger($request,$response){
    $swagger = \Swagger\scan(__DIR__. '/../');
    header('Content-Type: application/json');
    return $response->withJson($swagger);
  }
  public function token($request,$response){
    $oauth2Request = RequestBridge::toOAuth2($request);
    $oauth2Response = $this->server->handleTokenRequest($oauth2Request);
    return ResponseBridge::fromOAuth2($oauth2Response);
  }
  public function authorize($request,$response){
    $oauth2Request = RequestBridge::toOAuth2($request);
    $oauth2Response  = new OAuth2\Response();
    $this->server->handleAuthorizeRequest($oauth2Request,$oauth2Response,true);
    return ResponseBridge::fromOAuth2($oauth2Response);
  }

  public function autorizar($request,$response){
    $oauth2Request = RequestBridge::toOAuth2($request);
    $token = $this->server->getAccessTokenData($oauth2Request);
    return $token['user_id'];
  }

  public function recurso($request,$response){
    $respuesta = $this->http->post('token',[
      'json' => [
        "grant_type"=>"password",
      	"client_id"=>"testclient",
      	"client_secret"=>"testpass",
        "username"=>"someuser",
        "password"=>"somepassword"
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
          $respuesta = $this->http->post('token',[
            'json' => [
              "grant_type"=>"password",
            	"client_id"=>"testclient",
            	"client_secret"=>"testpass",
              "username"=>$input['dni'],
              "password"=>$input['contraseña']
            ]
          ]);
          $json = json_decode($respuesta->getBody(), true);
          $token = $json['token_type'].' '.$json['access_token'];
          return $response->withJson(
            [
              'resultado' => "Exito",
              'salida' => $todos,
              'numfilas' => 1,
              'Authorization'=> $token,
              'refresh_token'=> $json['refresh_token']
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

  /**
   * @SWG\Post(
   *   path="/registrar",
   *   summary="Agregar un nuevo usuario",
   *   description="Api para crear un nuevo usuario",
   *   @SWG\Response(
   *     response=200,
   *     description="Usuario agregado"
   *   ),
   *   @SWG\Response(
   *     response=400,
   *     description="Error en el ingreso de datos"
   *   )
   * )
   */
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
        'usu_apellido'=>isset($input['apellido'])?$input['apellido']:"",
				'usu_nombre'=>$input['nombre'],
        'usu_dni'=>$input['dni'],
        'usu_contraseña'=> password_hash($input['contraseña'],PASSWORD_DEFAULT),
			]);
      $this->server->getStorage('user_credentials')->setUser($input['dni'],$input['contraseña'],$input['nombre'],$input['apellido']);
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
