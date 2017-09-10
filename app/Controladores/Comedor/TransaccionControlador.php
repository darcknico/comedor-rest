<?php

namespace App\Controladores\Comedor;

use App\Modelos\Usuario;
use App\Modelos\Comedor\Transaccion;
use App\Controladores\Controlador;


class TransaccionControlador extends Controlador
{
  public function getList($request,$response)
  {
    try{
      $us = Usuario::where('token',$request->getHeader('token')[0])->first();
      $todos = Transaccion::where('usu_id',$us->id)->get();
      if($us && $todos){
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
          'resultado' => "No hay Registros",
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
      $us = Usuario::where('usu_token',$request->getHeader('token')[0])->first();
      $input = $request->getParsedBody();
      if($us){
  			$todos = Transaccion::create([
          'usu_id'=> $us->id,
          'tra_monto'=>$input['monto'],
          'tra_concepto'=>$input['concepto'],
          'tra_fecha'=> date('Y-m-d'),
          'paymentMethodId'=>$input['paymentMethodId'],
          'cardIssuerId'=>$input['cardIssuerId'],
          'installment'=>$input['installment'],
          'cardToken'=>$input['cardToken'],
          'campaignId'=>$input['campaignId'],
  				]);
        $us->saldo = $us->saldo + floatval($input['monto']);
        $us->save();
        return $response->withJson(
          [
            'resultado' => "Creacion con Exito",
            'salida' => $todos,
            'numfilas' => 1
          ]
        );
      }
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "No hay Registros",
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
