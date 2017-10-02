<?php

namespace App\Controladores;


use App\Modelos\Usuario;
use App\Controladores\Controlador;
use OAuth2;
use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;

class AutenticacionControlador extends Controlador
{
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

/**
 * @SWG\Post(
 *   path="/acceder",
 *   tags={"usuario"},
 *   summary="Autenticacion de un usuario",
 *   description="El usuario accede a sus datos y el token de autorizacion, solo proporcionando su dni y contraseña",
 *   operationId="acceder",
 *   consumes={"application/json"},
 *   produces={"application/json"},
 *   @SWG\Parameter(
 *     name="Body",
 *     in="body",
 *     description="Cuerpo del json a enviar",
 *     required=true,
 *     @SWG\Schema(
 *        @SWG\Property(
 *            property="dni",
 *            type="string",
 *        ),
 *        @SWG\Property(
 *            property="salida",
 *            type="string"
 *        ),
 *     ),
 *   ),
 *
 *   @SWG\Response(
 *         response=200,
 *         description="Se a logrado registrar un nuevo usuario con exito, devolviendo el nuevo usuario",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                property="resultado",
 *                type="string",
 *                example="Creacion con Exito"
 *             ),
 *             @SWG\Property(
 *                 property="salida",
 *                 ref="#/definitions/Usuario"
 *             ),
 *             @SWG\Property(
 *                 property="numfilas",
 *                 type="integer",
 *                 example="1"
 *             ),
 *             @SWG\Property(
 *                property="Authorization",
 *                type="string",
 *             ),
 *             @SWG\Property(
 *                property="refresh_token",
 *                type="string",
 *             ),
 *         ),
 *     ),
 *   @SWG\Response(
 *         response=400,
 *         description="Se a encontrado al menos un error en la validacion de los datos de entrada",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                property="resultado",
 *                type="string",
 *                example="Error en los Datos",
 *             ),
 *             @SWG\Property(
 *                property="salida",
 *                type="object",
 *                @SWG\Property(
 *                   property="dni",
 *                   type="array",
 *                   @SWG\Items(type="string")
 *                ),
 *                @SWG\Property(
 *                   property="contraseña",
 *                   type="array",
 *                   @SWG\Items(type="string")
 *                ),
 *             ),
 *             @SWG\Property(
 *                property="numfilas",
 *                type="integer",
 *                example="0"
 *             ),
 *         ),
 *     ),
 *   @SWG\Response(
 *         response=401,
 *         description="Errores en la autenticacion del usuario",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                property="resultado",
 *                enum={"Cuenta Inexistente","Contraseña Incorrecta"},
 *                type="string",
 *             ),
 *             @SWG\Property(
 *                property="numfilas",
 *                type="integer",
 *                example="0"
 *             ),
 *         ),
 *     ),
 *   @SWG\Response(
 *         response=403,
 *         description="Error encontrado al ejecutarse con la base de datos",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                property="resultado",
 *                type="string",
 *                example="Error en Base de Datos",
 *             ),
 *             @SWG\Property(
 *                property="salida",
 *                type="string",
 *             ),
 *             @SWG\Property(
 *                property="numfilas",
 *                type="integer",
 *                example="0"
 *             ),
 *         ),
 *     ),
 *   @SWG\Response(
 *         response=501,
 *         description="Errores del lado del servidor",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                property="resultado",
 *                type="string",
 *                example="Error no definido",
 *             ),
 *             @SWG\Property(
 *                property="salida",
 *                type="string",
 *             ),
 *             @SWG\Property(
 *                property="numfilas",
 *                type="integer",
 *                example="0"
 *             ),
 *         ),
 *     ),
 *   security={{
 *     "comedor_auth": {"basico"}
 *   }}
 * )
 */
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
 *   tags={"usuario"},
 *   summary="Registra un nuevo usuario",
 *   description="Crea un usuario en el sistema con parametros por defecto si no son ingresados. Tambien se de de alta en el servidor de autenticacion",
 *   operationId="registrar",
 *   consumes={"application/json"},
 *   produces={"application/json"},
 *   @SWG\Parameter(
 *     name="Body",
 *     in="body",
 *     description="Cuerpo del json a enviar",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/Usuario"),
 *   ),
 *
 *   @SWG\Response(
 *         response=201,
 *         description="Se a logrado registrar un nuevo usuario con exito, devolviendo el nuevo usuario",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                property="resultado",
 *                type="string",
 *                example="Creacion con Exito"
 *             ),
 *             @SWG\Property(
 *                 property="salida",
 *                 ref="#/definitions/Usuario"
 *             ),
 *             @SWG\Property(
 *                 property="numfilas",
 *                 type="integer",
 *                 example="1"
 *             ),
 *         ),
 *     ),
 *   @SWG\Response(
 *         response=400,
 *         description="Se a encontrado al menos un error en la validacion de los datos de entrada",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                property="resultado",
 *                type="string",
 *                example="Error en los Datos",
 *             ),
 *             @SWG\Property(
 *                property="salida",
 *                type="object",
 *                @SWG\Property(
 *                   property="dni",
 *                   type="array",
 *                   @SWG\Items(type="string")
 *                ),
 *                @SWG\Property(
 *                   property="contraseña",
 *                   type="array",
 *                   @SWG\Items(type="string")
 *                ),
 *                @SWG\Property(
 *                   property="nombre",
 *                   type="array",
 *                   @SWG\Items(type="string")
 *                ),
 *                @SWG\Property(
 *                   property="apellido",
 *                   type="array",
 *                   @SWG\Items(type="string")
 *                ),
 *             ),
 *             @SWG\Property(
 *                property="numfilas",
 *                type="integer",
 *                example="0"
 *             ),
 *         ),
 *     ),
 *   @SWG\Response(
 *         response=403,
 *         description="Error encontrado al ejecutarse con la base de datos",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                property="resultado",
 *                type="string",
 *                example="Error en Base de Datos",
 *             ),
 *             @SWG\Property(
 *                property="salida",
 *                type="string",
 *             ),
 *             @SWG\Property(
 *                property="numfilas",
 *                type="integer",
 *                example="0"
 *             ),
 *         ),
 *     ),
 *   @SWG\Response(
 *         response=501,
 *         description="Errores del lado del servidor",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                property="resultado",
 *                type="string",
 *                example="Error no definido",
 *             ),
 *             @SWG\Property(
 *                property="salida",
 *                type="string",
 *             ),
 *             @SWG\Property(
 *                property="numfilas",
 *                type="integer",
 *                example="0"
 *             ),
 *         ),
 *     ),
 *   security={{
 *     "comedor_auth": {"basico"}
 *   }}
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
