<?php

namespace App\Controladores\Comedor;

use App\Modelos\Usuario;
use App\Modelos\Comedor\Menu;
use App\Modelos\Comedor\Ticket;
use App\Controladores\Controlador;
use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;


class TicketControlador extends Controlador{
/**
* @SWG\Get(
*   path="/ticket",
*   tags={"ticket"},
*   summary="Tickets pretenecientes al usuario o por codigo",
*   description="Obtiene un listado de tickets, tambien se puede filtrar por estado. Si es pasado un codigo, el endpoint procede a devolver el ticket asociado",
*   operationId="getList",
*   consumes={"application/json"},
*   produces={"application/json"},
*   @SWG\Parameter(
*     name="estado",
*     in="query",
*     description="Estado asociado al Ticket",
*     type="string"
*   ),
*   @SWG\Parameter(
*     name="codigo",
*     in="query",
*     description="Codigo identificatorio del ticekt",
*     type="string"
*   ),
*
*   @SWG\Response(
*         response=200,
*         description="Listado de tickets. Si es que ingreso un ticket puede devolver un objeto en vez de un array.",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Exito"
*             ),
*             @SWG\Property(
*                 property="salida",
*                 type="array",
*                 @SWG\Items(type="object",ref="#/definitions/Ticket"),
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
*                example="Codigo del Ticket Invalido",
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
  public function getList($request,$response){
    try{
      if($request->hasHeader('codigo')) {
        $todos = Ticket::with('menu')->with('usuario')->where('tic_codigo',$request->getHeader('codigo')[0])->first();
        if($todos) {
          return $response->withJson(
            [
              'resultado' => "Exito",
              'salida' => $todos,
              'numfilas' => $todos->count()
            ]
          );
        }
        return $response->withStatus(400)->withJson(
          [
            'resultado' => "Codigo del Ticket Invalido",
            'numfilas' => 0
          ]
        );
      }
      $oauth2Request = RequestBridge::toOAuth2($request);
      $token = $this->server->getAccessTokenData($oauth2Request);
      $us = Usuario::where('usu_dni',$token['user_id'])->first();
      $todos = Ticket::with('menu')->where('usu_id',$us->id)->get();
      if($request->hasHeader('estado')){
        $todos = Ticket::with('menu')->
        where('usu_id',$us->id)->
        where('tic_estado','like',$request->getHeader('estado')[0])->get();
      }
      return $response->withJson(
        [
          'resultado' => "Exito",
          'salida' => $todos,
          'numfilas' => $todos->count()
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
* @SWG\Get(
*   path="/ticket/{id}",
*   tags={"ticket"},
*   summary="Obtene un ticket",
*   description="",
*   operationId="get",
*   consumes={"application/json"},
*   produces={"application/json"},
*   @SWG\Parameter(
*     name="id",
*     in="query",
*     description="Identificador asociado al Ticket",
*     type="string"
*   ),
*   @SWG\Response(
*         response=200,
*         description="Devuelve un objeto ticket.",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Exito"
*             ),
*             @SWG\Property(
*                 property="salida",
*                 type="array",
*                 @SWG\Items(type="object",ref="#/definitions/Ticket"),
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
*         description="Ocurrio un error.",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                example="Numero identificatorio del ticket no existe",
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
  public function get($request,$response,$args)
  {
    try{
      $todos = Ticket::with('menu')->with('usuario')->where('tic_id',$args['id'])->first();
      if($todos) {
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
          'resultado' => "Numero identificatorio del ticket no existe",
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
*   path="/menu/{idMenu}/ticket",
*   tags={"menu"},
*   summary="Compra un ticket",
*   description="",
*   operationId="post",
*   consumes={"application/json"},
*   produces={"application/json"},
*   @SWG\Parameter(
*     name="idMenu",
*     in="query",
*     description="Numero identificatorio del menu",
*     type="string"
*   ),
*   @SWG\Response(
*         response=200,
*         description="Devuelve el ticket recien comprado",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Creacion con Exito"
*             ),
*             @SWG\Property(
*                 property="salida",
*                 type="array",
*                 @SWG\Items(type="object",ref="#/definitions/Ticket"),
*             ),
*             @SWG\Property(
*                 property="numfilas",
*                 type="integer",
*                 example="1"
*             ),
*         ),
*     ),
*   @SWG\Response(
*         response=403,
*         description="Pueden ocurrir multiples errores al crear un ticket. Entre ellos son: si el ticket ya lo tiene comprado, no posee suficiente saldo o no puede comprar mas tickets.",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Error en Base de Datos",
*             ),
*             @SWG\Property(
*                property="salida",
*                type="string",
*                enum={"Usted ya tiene un ticket comprado","No posee suficiente saldo","Ya uso todos los tickets que tiene","No puede comprar mas tickets","Terminado consulta de forma imprevista"},
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
	public function post($request,$response,$args)
	{
		try{
      $oauth2Request = RequestBridge::toOAuth2($request);
      $token = $this->server->getAccessTokenData($oauth2Request);
      $us = Usuario::where('usu_dni',$token['user_id'])->first();
      $input = $request->getParsedBody();
      $menu = Menu::where('men_id',$args['idMenu'])->first();
			$id = Ticket::create([
				'men_id'=>$menu->id,
        'usu_id'=> $us->id,
        'tic_precio' => $menu->precio,
        'tic_fecha' => $menu->fecha,
				])->tic_id;
      $todos = Ticket::where('tic_id',$id)->first();
      $parteA = str_pad($token['user_id'], 6, "0", STR_PAD_LEFT);
      $parteB = str_pad(date('dmY'), 6, "0", STR_PAD_LEFT);
      $parteC = str_pad($id, 6, "0", STR_PAD_LEFT);
      $todos->update([
        'tic_codigo'=> $parteA.$parteB.$parteC,
      ]);
      return $response->withJson(
        [
          'resultado' => "Creacion con Exito",
          'salida' => $todos,
          'numfilas' => 1
        ]
      );
		} catch (\Illuminate\Database\QueryException $e) {
      $errorCode = $e->errorInfo[0];
      if($errorCode == "D000M"){
        $error = "Usted ya tiene un ticket comprado";
      }
      if($errorCode == "D001M"){
        $error = "No posee suficiente saldo";
      }
      if($errorCode == "D002M"){
        $error = "Ya uso todos los tickets que tiene";
      }
      if($errorCode == "D003M"){
        $error = "No puede comprar mas tickets";
      }
      if($errorCode == "D003M"){
        $error = "Terminado consulta de forma imprevista";
      }
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "Error en Base de Datos",
          'salida' => $error?$error:$e->errorInfo[2],
          'numfilas' => 0
        ]
      );
    } catch (\Exception $e) {
      return $response->withStatus(501)->withJson(
        [
          'resultado' => "Error no definido",
          'salida' => $e,
          'numfilas' => 0
        ]
      );
		}
	}
/**
* @SWG\Delete(
*   path="/ticket/{id}",
*   tags={"ticket"},
*   summary="Descarta un ticket",
*   description="Una vez que un ticket es comprado, tiene el estado como Activo, pero solo aquellos que esten hasta un dia anterior a la fecha del menu comprado o tengan estado Vencido, pueden darse de baja y descartarlo",
*   operationId="delete",
*   consumes={"application/json"},
*   produces={"application/json"},
*   @SWG\Parameter(
*     name="id",
*     in="query",
*     description="Numero identificador asociado al Ticket",
*     type="string"
*   ),
*   @SWG\Response(
*         response=200,
*         description="Devuelve un objeto ticket.",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Cancelacion con Exito"
*             ),
*             @SWG\Property(
*                 property="salida",
*                 type="object",
*                 ref="#/definitions/Ticket",
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
*         description="Ocurrio un error con el ticket. el resultado varia",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                enum={"Ticket YA CANCELADO","No hay Registro"}
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
	public function delete($request,$response,$args)
	{
    try{
			$input = $request->getParsedBody();
			$todos = Ticket::where('tic_id',$args['id'])->first();
      if($todos){
        if($todos->condicion == 'cancelado'){
          return $response->withStatus(400)->withJson(
            [
              'resultado' => "Ticket YA CANCELADO",
              'numfilas' => 0
            ]
          );
        }
        $todos->update([
  				'tic_estado'=>'cancelado'
  				]);
        return $response->withJson(
          [
            'resultado' => "Cancelacion con Exito",
            'salida' => $todos,
            'numfilas' => 1
          ]
        );
      }
      return $response->withStatus(400)->withJson(
        [
          'resultado' => "No hay Registro",
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
*   path="/ticket/{id}",
*   tags={"ticket"},
*   summary="Da como usado el ticket",
*   description="Usado en la validacion de ticket con estado Activo para el menu de la fecha. Solo el usuario como rol de Administrador puede usar este endpoint.",
*   operationId="delete",
*   consumes={"application/json"},
*   produces={"application/json"},
*   @SWG\Parameter(
*     name="id",
*     in="query",
*     description="Numero identificador asociado al Ticket",
*     type="string"
*   ),
*   @SWG\Response(
*         response=200,
*         description="Devuelve un objeto ticket.",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                example="Validacion con Exito"
*             ),
*             @SWG\Property(
*                 property="salida",
*                 type="object",
*                 ref="#/definitions/Ticket",
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
*         description="Ocurrio un error con el ticket. el resultado varia",
*         @SWG\Schema(
*             @SWG\Property(
*                property="resultado",
*                type="string",
*                enum={"No es Administrador","Ticket YA USADO","Validacion sin Exito"}
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
*     "comedor_auth":{
*       "basico": "Alumno",
*       "completo": "Administrador"}
*   }}
* )
*/
	public function put($request,$response,$args)
	{
    try{
      $oauth2Request = RequestBridge::toOAuth2($request);
      // obtiendo las credenciales del token
      $token = $this->server->getAccessTokenData($oauth2Request);
      // buscando al usuario del comedor
      $us = Usuario::where('usu_dni',$token['user_id'])->first();
      if($us->tus_id!=0){
        return $response->withStatus(400)->withJson(
          [
            'resultado' => "No es Administrador",
            'numfilas' => 0
          ]
        );
      }
			$input = $request->getParsedBody();
      $todos = Ticket::with('usuario')->with('menu')
        ->where('tic_id',$args['id'])
        ->where('men_id',$input['idMenu'])
        ->first();
      if($todos) {
        if($todos->estado == 'usado'){
          return $response->withStatus(400)->withJson(
            [
              'resultado' => "Ticket YA USADO",
              'numfilas' => 0
            ]
          );
        }
  			$todos->update([
  				'tic_estado'=>'usado'
  				]);
        return $response->withJson(
          [
            'resultado' => "Validacion con Exito",
            'salida' => $todos,
            'numfilas' => 1
          ]
        );
      }
      return $response->withStatus(400)->withJson(
        [
          'resultado' => "Validacion sin Exito",
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
