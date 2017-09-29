<?php

namespace App\Controladores\Comedor;

use App\Modelos\Usuario;
use App\Modelos\Comedor\Menu;
use App\Modelos\Comedor\Ticket;
use App\Controladores\Controlador;
use Illuminate\Support\Facades\DB;
use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;


class MenuControlador extends Controlador{

  public function getList($request,$response){
    $todos = Menu::all();
    $oauth2Request = RequestBridge::toOAuth2($request);
    $token = $this->server->getAccessTokenData($oauth2Request);
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

  public function get($request,$response,$args)
	{
		try {
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
  			$todos->update([
  				'finalizado' => true
  				]);
        return $response->withJson(
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
