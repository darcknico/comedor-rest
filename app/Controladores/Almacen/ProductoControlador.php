<?php

namespace App\Controladores\Almacen;

use App\Controladores\Controlador;
use App\Modelos\Almacen\Producto;


class ProductoControlador extends Controlador
{
  public function getList($request,$response)
  {
    try {
      $todos = Producto::all();
      return $response->withJson(
        [
          'resultado' => 'Exito',
          'salida' => $todos,
          'numfilas' => 0
        ]
      );
    } catch (Exception $e) {
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
		try {
			$todos = Producto::where('id',$args['id'])->first();
      if($todos) {
        return $response->withJson(
          [
            'resultado' => "Exito ",
            'salida' => $todos,
            'numfilas' => $todos->count()
          ]
        );
      } else {
        return $response->withJson(
          [
            'resultado' => "Sin registros",
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
			$todos = Producto::create([
        'nombre' => $input['nombre'],
        'descripcion' => $input['descripcion']
				]);
        return $response->withStatus(201)->withJson(
          [
            'resultado' => "Creacion con Exito",
            'salida' => $todos,
            'numfilas' => $todos->count()
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

	public function delete($request,$response,$args)
	{
    try{
			$input = $request->getParsedBody();
			$todos = Producto::where('id',$args['id'])->first();
      if($todos) {
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
          'resultado' => "No hay Registro",
          'numfilas' => 0
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
      $todos = Producto::where('id',$args['id'])->first();
      if($todos){
  			$todos->update([
          'nombre' => $input['nombre'],
          'descripcion' => $input['descripcion']
  				]);
        return $response->withJson(
            [
              'resultado' => "Modificacion con Exito",
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
          'resultado' => "Error en Base de Datos ",
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
