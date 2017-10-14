<?php

namespace App\Controladores\Comedor;

use App\Modelos\Usuario;
use App\Modelos\Comedor\Transaccion;
use App\Controladores\Controlador;
use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;


class TransaccionControlador extends Controlador
{
  public function getList($request,$response)
  {
    try{
      $oauth2Request = RequestBridge::toOAuth2($request);
      $token = $this->server->getAccessTokenData($oauth2Request);
      $us = Usuario::where('usu_dni',$token['user_id'])->first();
      $todos = Transaccion::where('usu_id',$us->id)->get();
      return $response->withJson(
        [
          'resultado' => "Exito",
          'salida' => $todos,
          'numfilas' => $todos->count()
        ]
      );
    } catch (\Illuminate\Database\QueryException $e) {
      $this->logger->info($e->getMessage());
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "Error en Base de Datos",
          'salida' => $e->errorInfo[2],
          'numfilas' => 0
        ]
      );
    } catch (\Exception $e) {
      $this->logger->info($e->getMessage());
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
      $oauth2Request = RequestBridge::toOAuth2($request);
      $token = $this->server->getAccessTokenData($oauth2Request);
      $us = Usuario::where('usu_dni',$token['user_id'])->first();
      $input = $request->getParsedBody();
      $dt = new \DateTime();
			$todos = Transaccion::create([
        'usu_id'=>$us->id,
        'tra_monto'=>$input['monto'],
        'tra_concepto'=>isset($input['concepto'])?$input['concepto']:"PAGO DIRECTO",
        'tra_fecha'=> date('Y-m-d',strtotime($input['fecha_acreditacion'])),
        'paymentMethodId'=>isset($input['paymentMethodId'])?$input['paymentMethodId']:null,
        'cardIssuerId'=>isset($input['cardIssuerId'])?$input['cardIssuerId']:null,
        'installment'=>isset($input['installment'])?$input['installment']:null,
        'cardToken'=>isset($input['cardToken'])?$input['cardToken']:null,
        'campaignId'=>isset($input['campaignId'])?$input['campaignId']:null,
			]);
      // aumentar tu saldo luego de cargar
      $us->saldo = $us->saldo + floatval($input['monto']);
      $us->save();
      return $response->withJson(
        [
          'resultado' => "Creacion con Exito",
          'salida' => $todos,
          'numfilas' => 1
        ]
      );
		} catch (\Illuminate\Database\QueryException $e) {
      $this->logger->info($e->getMessage());
      return $response->withStatus(403)->withJson(
        [
          'resultado' => "Error en Base de Datos",
          'salida' => $e->errorInfo[2],
          'numfilas' => 0
        ]
      );
    } catch (\Exception $e) {
      $this->logger->info($e->getMessage());
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
