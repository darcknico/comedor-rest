<?php

namespace App\Controladores\Comedor;

use App\Modelos\Usuario;
use App\Modelos\Comedor\Menu;
use App\Modelos\Comedor\Ticket;
use App\Controladores\Controlador;


class TicketControlador extends Controlador
{
  public function getList($request,$response)
  {
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
        return $response->withStatus(403)->withJson(
          [
            'resultado' => "Codigo del Ticket Invalido",
            'numfilas' => 0
          ]
        );
      }
      $us = Usuario::where('usu_token',$request->getHeader('token'))->first();
      if($us){
        $todos = Ticket::with('menu')->where('usu_id',$us->id)->get();
        if($request->hasHeader('estado')){
          $todos = Ticket::with('menu')->
          where('usu_id',$us->id)->
          where('tic_estado','like',$request->getHeader('estado'))->get();
        }
        return $response->withJson(
          [
            'resultado' => "Exito",
            'salida' => $todos,
            'numfilas' => $todos->count()
          ]
        );
      }
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "Usuario Invalido",
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

  public function get($request,$response,$args)
  {
    try{
      $todos = Ticket::with('menu')->with('usuario')->where('tic_id',$args['id'])->first();
      if($todos) {
        return $response->withJson(
          [
            'resultado' => "Exito",
            'salida' => $todos,
            'numfilas' => $todos->count()
          ]
        );
      }
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "Ticket Invalido",
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

	public function post($request,$response,$args)
	{
		try{
      $us = Usuario::where('token',$request->getHeader('token'))->first();
      $input = $request->getParsedBody();
      if($us){
        $menu = Menu::where('men_id',$args['idMenu'])->first();
  			$todos = Ticket::create([
  				'men_id'=>$menu->id,
          'usu_id'=> $us->id,
          'tic_precio' => $menu->precio,
          'tic_fecha' => $menu->fecha,
  				]);
        return $response->withJson(
          [
            'resultado' => "Creacion con Exito",
            'salida' => $todos,
            'numfilas' => 1
          ]
        );
      }
      return $response->withStatus(400)->withJson(
        [
          'resultado' => "Usuario Invalido",
          'numfilas' => 0
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
        $error = "No posee suficiente saldo";
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

	public function delete($request,$response,$args)
	{
    try{
			$input = $request->getParsedBody();
			$todos = Ticket::where('tic_id',$args['id'])->first();
      if($todos){
        if($todos->condicion == 'cancelado'){
          return $response->withJson(
            [
              'resultado' => "Ticket YA CANCELADO",
              'salida' => $todos,
              'numfilas' => 1
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
            'numfilas' => $todos->count()
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

	public function put($request,$response,$args)
	{
    try{
			$input = $request->getParsedBody();
      $todos = Ticket::with('usuario')->with('menu')
        ->where('tic_id',$args['id'])
        ->where('men_id',$input['idMenu'])
        ->first();
      if($todos) {
        if($todos->estado == 'usado'){
          return $response->withJson(
            [
              'resultado' => "Ticket YA USADO",
              'salida' => $todos,
              'numfilas' => 1
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
      return $response->withStatus(403)->withJson(
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
