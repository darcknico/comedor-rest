<?php
// Routes
/*
$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
*/
use Respect\Validation\Validator as v;
use Chadicus\Slim\OAuth2\Middleware;

$authMiddleware = new Middleware\Authorization($container['server'], $container);

$app->any('/','AutenticacionControlador:inicio');
$app->post('/token','AutenticacionControlador:token');
$app->post('/recurso','AutenticacionControlador:recurso');
$app->post('/autorizar','AutenticacionControlador:autorizar');

$app->post('/acceder','AutenticacionControlador:acceder')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'dni' => v::notEmpty()->NoWhitespace()->IntVal()->Positive(),
    'contraseña' => v::notEmpty()->length(8,128)
  )))
;
$app->post('/registrar','AutenticacionControlador:registrar')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'nombre' => v::notEmpty()->alpha(),
    'dni' => v::notEmpty()->NoWhitespace()->IntVal()->Positive(),
    'contraseña' => v::notEmpty()->length(8,128)
  )));

$app->get('/usuario/acceder','AutenticacionControlador:signup');
$app->post('/usuario/contraseña','AutenticacionControlador:change');

$app->get('/usuario','UsuarioControlador:getList');
$app->get('/usuario/{id}','UsuarioControlador:get');
$app->post('/usuario/{id}/imagen','UsuarioControlador:postImagen');
$app->post('/usuario','UsuarioControlador:post')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'nombre' => v::notEmpty()->alpha(),
    'dni' => v::notEmpty()->NoWhitespace()->IntVal()->Positive(),
    'contraseña' => v::notEmpty()->length(8,128)
  )));
$app->delete('/usuario/{id}','UsuarioControlador:delete');
$app->put('/usuario/{id}','UsuarioControlador:put')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'nombre' => v::notEmpty()->alpha()
  )));

/////////////////GESTION DE MENUS
$app->get('/menu','MenuControlador:getList');
$app->get('/menu/{id}','MenuControlador:get');
$app->post('/menu','MenuControlador:post')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'fecha' => v::notEmpty()->date('d-m-Y')->min(date('d-m-Y')),
    'cantidad' => v::notEmpty()->IntVal()->Positive()
  )));
$app->post('/menu/{id}','MenuControlador:finalizar');
$app->delete('/menu/{id}','MenuControlador:delete');
$app->put('/menu/{id}','MenuControlador:put')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'cantidad' => v::notEmpty()->IntVal()->Positive()
  )));

/////////////////////GESTION DE TICKETS
$app->get('/ticket','TicketControlador:getList');
$app->get('/ticket/{id}','TicketControlador:get');
$app->post('/menu/{idMenu}/ticket','TicketControlador:post');
$app->delete('/ticket/{id}','TicketControlador:delete');
$app->put('/ticket/{id}','TicketControlador:put');

///////////////////////GESTION TRANSACCION
$app->get('/transaccion','TransaccionControlador:getList');
$app->post('/transaccion','TransaccionControlador:post')->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'fecha' => v::notEmpty()->date('d-m-Y')->min(date('d-m-Y')),
    'monto' => v::notEmpty()->Positive()->FloatVal()
  ))
);

////////////////////////ALMACEN//////////////////////////////
$app->get('/producto','ProductoControlador:getList');
$app->get('/producto/{id}','ProductoControlador:get');
$app->post('/producto','ProductoControlador:post')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'nombre' => v::notEmpty(),
    'descripcion' => v::notEmpty()
  )));
$app->delete('/producto/{id}','ProductoControlador:delete');
$app->put('/producto/{id}','ProductoControlador:put')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'nombre' => v::notEmpty(),
    'descripcion' => v::notEmpty()
  )));

$app->get('/local','LocalControlador:getList');
$app->get('/local/{id}','LocalControlador:get');
$app->post('/local','LocalControlador:post')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'nombre' => v::notEmpty(),
    'direccion' => v::notEmpty(),
    'latitud' => v::notEmpty()->FloatVal(),
    'longitud' => v::notEmpty()->FloatVal()
  )));
$app->delete('/local/{id}','LocalControlador:delete');
$app->put('/local/{id}','LocalControlador:put')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'nombre' => v::notEmpty(),
    'direccion' => v::notEmpty(),
    'latitud' => v::notEmpty()->FloatVal(),
    'longitud' => v::notEmpty()->FloatVal()
  )));

$app->get('/producto/{idProducto}/oferta','OfertaControlador:getList');
$app->get('/oferta/{id}','OfertaControlador:get');
$app->post('/local/{idLocal}/producto/{idProducto}/oferta','OfertaControlador:post')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'precio' => v::notEmpty(),
    'vencimiento' => v::notEmpty()->date('d-m-Y')->min(date('d-m-Y')),
  )));
$app->delete('/oferta/{id}','OfertaControlador:delete');
$app->put('/oferta/{id}','OfertaControlador:put')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'precio' => v::notEmpty(),
    'vencimiento' => v::notEmpty()->date('d-m-Y')->min(date('d-m-Y')),
  )));
