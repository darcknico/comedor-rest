<?php

namespace App\Controladores\Almacen;

use App\Controladores\Controlador;
use App\Modelos\Almacen\Oferta;
use App\Modelos\Almacen\Local;
use App\Modelos\Usuario;


class OfertaControlador extends Controlador
{
  public function getList($request,$response,$args)
  {
    try {
      $todos = Oferta::all();
      if($request->hasHeader('token')) {
      $us = Usuario::where('token',$request->getHeader('token'))->first();
        if($us){
          $locales = Local::select('idLocal')->where('idUsuario',$us->id)->get();
          $todos = Oferta::with('local')->whereIn('idLocal',$locales)->where('idProducto',$args['idProducto'])->get();
          if(!$todos){
            return $response->withStatus(400)->withJson(
              [
                'resultado' => "No hay Ofertas",
                'numfilas' => 0
              ]
            );
          }
        } else {
          return $response->withStatus(400)->withJson(
            [
              'resultado' => 'No existe Usuario',
              'numfilas' => 0
            ]
          );
        }
      } else {
        $ofertas = Oferta::select('idLocal')->where('idProducto',$args['idProducto'])->groupBy('idLocal')->get();
        $todos = Local::with('ofertas')->join('gpsofertas','gpsofertas.idLocal','=','gpslocales.idLocal')->whereIn('gpslocales.idLocal',$ofertas)->where('idProducto',$args['idProducto'])->get();
      }
      return $response->withJson(
        [
          'resultado' => 'Exito',
          'salida' => $todos,
          'numfilas' => $todos->count()
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
			$todos = Oferta::with('local')->with('producto')->find($args['id'])->first();
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

	public function post($request,$response,$args)
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
      $us = Usuario::where('token',$request->getHeader('token'))->first();
      if($us){
        $input = $request->getParsedBody();
        $todos = Oferta::create([
          'idLocal' => $args['idLocal'],
          'idProducto' => $args['idProducto'],
          'precio' => $input['precio'],
          'vencimiento' => date('Y-m-d',strtotime($input['vencimiento']))
  			]);
        return $response->withStatus(201)->withJson(
          [
            'resultado' => "Creacion con Exito",
            'salida' => $todos,
            'numfilas' => $todos->count()
          ]
        );
      }
      return $response->withStatus(400)->withJson(
        [
          'resultado' => 'No existe Usuario',
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

	public function delete($request,$response,$args)
	{
    try{
			$input = $request->getParsedBody();
			$todos = Oferta::find($args['id'])->first();
      if($todos){
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
          'resultado' => "No existe Oferta",
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
      $todos = Oferta::find($args['id'])->first();
      if ($todos) {
  			$todos->update([
          'precio' => $input['precio'],
          'vencimiento' => date('Y-m-d',strtotime($input['vencimiento']))
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
          'resultado' => "No existe Oferta",
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
