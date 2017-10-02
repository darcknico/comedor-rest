<?php

namespace App;

use App\Controladores\Controlador;
/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="url base del sitio ",
 *     basePath="/",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="AppComedor REST",
 *         description="Este es un simple servidor para la gestion del Ticket en la compra de menus en el comedor Universitario.",
 *         @SWG\Contact(
 *             email="nicolasrl2005@gmail.com"
 *         )
 *     )
 * )
 */

class Swagger extends Controlador{

  public function inicio($request,$response){
    return $this->view->render($response,'swagger.twig');
  }

  public function get($request,$response){
    $swagger = \Swagger\scan(__DIR__. '/');
    $json = json_decode($swagger, true);
    $json["host"] = $this->request->getUri()->getBaseUrl();
    $json["securityDefinitions"]["comedor_auth"]["tokenUrl"] = $this->request->getUri()->getBaseUrl().'/token';
    $json["securityDefinitions"]["comedor_auth"]["authorizationUrl"] = $this->request->getUri()->getBaseUrl().'/authorize';
    return $response->withJson($json);
  }

}
