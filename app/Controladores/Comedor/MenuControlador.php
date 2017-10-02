<?php

namespace App\Controladores\Comedor;

use App\Modelos\Usuario;
use App\Modelos\Comedor\Menu;
use App\Modelos\Comedor\Ticket;
use App\Controladores\Controlador;
use Illuminate\Support\Facades\DB;
use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;
use Monolog\Handler\NullHandler;


class MenuControlador extends Controlador{
/**
* @SWG\Get(
*   path="/menu",
*   tags={"menu"},
*   summary="Listado de todos los menus, o menus sin comprar, o menu de la fecha",
*   description="Para obtener una lista de menus registrados debe estar autenticado como Administrador. En otro caso la respuesta es otro listado de menus que aun no compro el usuario a partir de la fecha actual",
*   operationId="getList",
*   consumes={"application/json"},
*   produces={"application/json"},
*   @SWG\Parameter(
*     name="fecha",
*     in="query",
*     description="Fecha del menu a buscar",
*     type="string"
*   ),
*
*   @SWG\Response(
*         response=200,
*         description="Listado de menus. Si ingreso una fecha la salida sera solo un objeto y no un array",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Exito"
*             ),
*             @SWG\Property(
*                 property="salida",
*                 type="array",
*                 @SWG\Items(type="object",ref="#/definitions/Menu"),
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
*         description="Ocurrio un error al buscar los menus. el resultado varia",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                enum={"No tiene Menus disponibles","No existe Menu restantes","No existe Menu para la Fecha: "},
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
  public function getList($request,$response){
    $todos = Menu::all();
    $oauth2Request = RequestBridge::toOAuth2($request);
    // obtiendo las credenciales del token
    $token = $this->server->getAccessTokenData($oauth2Request);
    // buscando al usuario del comedor
    $us = Usuario::where('usu_dni',$token['user_id'])->first();
    if($us->tus_id!=0){
        if ($us->tickets > 0) {
          $ticket = Ticket::where('usu_id',$us->id)->pluck('men_id');
          $todos = Menu::whereNotIn('men_id',$ticket)->
            whereDate('men_fecha','>=',date('Y-m-d'))->
            where('men_finalizado','0')->get();
        } else {
          return $response->withStatus(400)->withJson(
            [
              'resultado' => "No tiene Menus disponibles",
              'numfilas' => 0
            ]
          );
        }
        if(!$todos){
          return $response->withStatus(400)->withJson(
            [
              'resultado' => "No existe Menu restantes",
              'numfilas' => 0
            ]
          );
        }
    }elseif($request->hasHeader('fecha')){
      $date = strtotime($request->getHeader('fecha')[0]);
      $todos = Menu::where('men_fecha',date('Y-m-d',$date))->first();
      if(!$todos) {
        return $response->withStatus(400)->withJson(
          [
            'resultado' => "No existe Menu para la Fecha: ".$request->getHeader('fecha')[0],
            'numfilas' => 0
          ]
        );
      } else {
        return $response->withJson(
          [
            'resultado' => "Exito",
            'salida' => $todos,
            'numfilas' => 1
          ]
        );
      }
    }
    return $response->withJson(
      [
        'resultado' => "Exito",
        'salida' => $todos,
        'numfilas' => $todos->count()
      ]
    );
  }
/**
* @SWG\Get(
*   path="/menu/{id}",
*   tags={"menu"},
*   summary="Obtener un Menu",
*   description="Busca el menu por el id ingresado en el path",
*   operationId="get",
*   consumes={"application/json"},
*   produces={"application/json"},
*   @SWG\Parameter(
*     name="id",
*     in="path",
*     description="Numero identificatorio del menu",
*     type="integer"
*   ),
*
*   @SWG\Response(
*         response=200,
*         description="",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Exito"
*             ),
*             @SWG\Property(
*                 property="salida",
*                 type="object",
*                 ref="#/definitions/Menu",
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
*         description="Ingreso un id de menu inexistente",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                example="No existe el Menu",
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
  public function get($request,$response,$args)
	{
		$todos = Menu::where('men_id',$args['id'])->first();
    if($todos){
      return $response->withJson(
        [
          'resultado' => "Exito",
          'salida' => $todos,
          'numfilas' => 1
        ]
      );
    }
    return $response->withStatus(400)->withJson(
      [
        'resultado' => "No existe el Menu",
        'numfilas' => 0
      ]
    );
	}

/**
* @SWG\Post(
*   path="/menu",
*   tags={"menu"},
*   summary="Agrega un menu",
*   description="",
*   operationId="post",
*   consumes={"application/json"},
*   produces={"application/json"},
*   @SWG\Parameter(
*     name="Body",
*     in="body",
*     description="Cuerpo del json a enviar",
*     required=true,
*     @SWG\Schema(
*        @SWG\Property(
*            property="fecha",
*            type="string",
*        ),
*        @SWG\Property(
*            property="cantidad",
*            type="integer"
*        ),
*        @SWG\Property(
*            property="precio",
*            type="string",
*        ),
*        @SWG\Property(
*            property="descripcion",
*            type="string"
*        ),
*     ),
*   ),
*
*   @SWG\Response(
*         response=201,
*         description="",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Creacion con Exito"
*             ),
*             @SWG\Property(
*                 property="salida",
*                 type="object",
*                 ref="#/definitions/Menu",
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
*         description="Se a encontrado al menos un error en la validacion de los datos de entrada, por lo que devuelve un array de String de errores por cada dato ingresado",
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
*                   property="fecha",
*                   type="array",
*                   @SWG\Items(type="string")
*                ),
*                @SWG\Property(
*                   property="cantidad",
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
	public function post($request,$response)
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
		try{
      $input = $request->getParsedBody();
			$todos = Menu::create([
				'men_fecha' => date('Y-m-d',strtotime($input['fecha'])) ,
        'men_cantidad' => $input['cantidad'],
        'men_precio' => $input['precio']?$input['precio']:5,
        'men_descripcion' => $input['descripcion']
				]);
      $todos = Menu::where('men_id',$todos->id)->first();
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
          'salida' => $e->errorInfo[2],
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

	public function delete($request,$response,$args)
	{
    try{
			$input = $request->getParsedBody();
			$todos = Menu::where('men_id',$args['id'])->first();
      if ($todos) {
        $todos->delete();
        return $response->withJson(
          [
            'resultado' => "Eliminacion con Exito",
            'numfilas' => 1
          ]
        );
      }
      return $response->withStatus(400)->withJson(
        [
          'resultado' => "Eliminacion sin Exito",
          'numfilas' => 0
        ]
      );
    } catch (\Illuminate\Database\QueryException $e) {
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "Error en Base de Datos",
          'salida' => $e->errorInfo[2],
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
*   path="/menu/{id}",
*   tags={"menu"},
*   summary="Cierra el menu",
*   description="Durante el dia del menu se puede finalizar el menu para no poder seguir ingresando validacion de tickets. Y aquellos tickets sin validar que esten como activos pasan a estar vencidos",
*   operationId="post",
*   consumes={"application/json"},
*   produces={"application/json"},
*   @SWG\Parameter(
*     name="id",
*     in="path",
*     description="Numero identificatorio del menu",
*     type="integer"
*   ),
*
*   @SWG\Response(
*         response=200,
*         description="",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Finalizacion con Exito"
*             ),
*             @SWG\Property(
*                 property="salida",
*                 type="object",
*                 ref="#/definitions/Menu",
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
*         description="Esta accion tiene que ejecutarse en el dia de la fecha del menu y en una franja horaria especificada",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Error en los Datos",
*             ),
*             @SWG\Property(
*                property="salida",
*                type="string",
*                example="Modificacion sin Exito",
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
	public function finalizar($request,$response,$args)
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
    try{
      $todos = Menu::where('men_id',$args['id'])->first();
      if($todos) {
        /*
        Falta agregar logica de reglas de finalizacion del menu
        por ejemplo si estamos en el dia del menu, o si son las 3 de la tarde
        */
  			$todos->update([
  				'finalizado' => true
  				]);
        $tickets = Ticket::where('men_id',$args['id'])->get();
        $tickets->update([
  				'tic_estado' => "Vencido"
  				]);
        return $response->withStatus(200)->withJson(
            [
              'resultado' => "Finalizacion con Exito",
              'salida' => $todos,
              'numfilas' => 1
            ]
          );
      }
      return $response->withStatus(400)->withJson(
        [
          'resultado' => "Modificacion sin Exito",
          'numfilas' => 0
        ]
      );
		} catch (\Illuminate\Database\QueryException $e) {
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "Error en Base de Datos",
          'salida' => $e->errorInfo[2],
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
* @SWG\Put(
*   path="/menu/{id}",
*   tags={"menu"},
*   summary="Modifica un menu",
*   description="",
*   operationId="put",
*   consumes={"application/json"},
*   produces={"application/json"},
*   @SWG\Parameter(
*     name="id",
*     in="path",
*     description="Numero identificatorio del menu",
*     type="integer"
*   ),
*   @SWG\Parameter(
*     name="Body",
*     in="body",
*     description="Cuerpo del json a enviar",
*     required=true,
*     @SWG\Schema(
*        @SWG\Property(
*            property="cantidad",
*            type="integer"
*        ),
*        @SWG\Property(
*            property="precio",
*            type="string",
*        ),
*        @SWG\Property(
*            property="descripcion",
*            type="string"
*        ),
*     ),
*   ),
*
*   @SWG\Response(
*         response=200,
*         description="",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Modificacion con Exito"
*             ),
*             @SWG\Property(
*                 property="salida",
*                 type="object",
*                 ref="#/definitions/Menu",
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
*         description="Se a encontrado al menos un error en la validacion de los datos de entrada, por lo que devuelve un array de String de errores por cada dato ingresado. Atencion puede exister otro error de no encontrar el menu por lo que en ese caso solo devuelve en la salida un String",
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
*                   property="cantidad",
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
  public function put($request,$response,$args)
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
      $todos = Menu::where('men_id',$args['id'])->first();
      if($todos) {
  			$todos->update([
  				'men_cantidad' => $input['cantidad'],
          'men_descripcion' => $input['descripcion'],
          'men_precio' => $input['precio']
  				]);
        return $response->withJson(
            [
              'resultado' => "Modificacion con Exito",
              'salida' => $todos,
              'numfilas' => 1
            ]
          );
      }
      return $response->withStatus(400)->withJson(
        [
          'resultado' => "Modificacion sin Exito",
          'numfilas' => 0
        ]
      );
		} catch (\Illuminate\Database\QueryException $e) {
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "Error en Base de Datos",
          'salida' => $e->errorInfo[2],
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
