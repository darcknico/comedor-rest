<?php

namespace App\Controladores;

use App\Modelos\Usuario;
use App\Controladores\Controlador;
use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;


class UsuarioControlador extends Controlador
{
  public function getList($request,$response){
    $oauth2Request = RequestBridge::toOAuth2($request);
    $token = $this->server->getAccessTokenData($oauth2Request);
    $todos = Usuario::where('usu_dni',$token['user_id'])->first();
    if($todos->tus_id!=0) {
      return $response->withJson(
        [
          'resultado' => "Exito",
          'salida' => $todos,
          'numfilas' => 1
        ]
      );
    }
    //ROL ADMINISTRADOR
    $todos = Usuario::all();
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
			$todos = Usuario::where('usu_id',$args['id'])->get();
      if($todos){
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
          'resultado' => "No existe el recurso",
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
			$todos = Usuario::create([
				'nombre'=>$input['nombre'],
        'dni'=>$input['dni'],
        'contraseña'=> password_hash($input['contraseña'],PASSWORD_DEFAULT),
        'token'=> md5(microtime() . rand())
				]);
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
    $input = $request->getParsedBody();
    try{
			$todos = Usuario::where('usu_id',$args['id'])->first();
      if ($todos) {
        $todos->delete();
        return $response->withJson(
          [
            'resultado' => "Eliminacion con Exito",
            'numfilas' => 1
          ]
        );
      }
      return $response->withStatus(403)->withJson(
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
    try{
			$input = $request->getParsedBody();
      $todos = Usuario::where('usu_id',$args['id'])->get();
			$todos->update([
				'usu_nombre'=>$input['nombre'],
        'usu_apellido'=>$input['apellido'],
        'usu_estado'=>$input['estado'],
				]);
        return $response->withJson(
          [
            'resultado' => "Modificacion con Exito",
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


public function postImagen($request,$response)
{
  try{
    $imagepath = 'assets/comedor/usuario/'.uniqid('img_').'.png';
    $file = $request->getUploadedFiles();
    $newfile = $file['file'];
    $input = $request->getParsedBody();
    if($newfile->getError() === UPLOAD_ERR_OK ) {
      $uploadFilename = $newfile->getClientFilename();
			$newfile_type = $newfile->getClientMediaType();
      $newfile->moveTo("assets/comedor/usuario/raw/". $uploadFilename);
			$pngfile = "assets/comedor/usuario/raw/".substr($uploadFilename, 0, -4).".png";
			if('image/jpeg' == $newfile_type){
				$_img = imagecreatefromjpeg("assets/comedor/usuario/raw/".$uploadFilename);
				imagepng($_img, $pngfile);
				unlink("assets/comedor/usuario/raw/". $uploadFilename);
			}
			copy($pngfile,$imagepath);
			unlink($pngfile);
    }
    $oauth2Request = RequestBridge::toOAuth2($request);
    $token = $this->server->getAccessTokenData($oauth2Request);
    $todos = Usuario::where('usu_dni',$token['user_id'])->first();
    $todos->update([
      'image'=>$imagepath
    ]);
    return $response->withStatus(201)->withJson(
        [
          'resultado' => "Imagen subida con Exito",
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
}
