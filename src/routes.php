<?php
// Routes
use Respect\Validation\Validator as v;
use Chadicus\Slim\OAuth2\Middleware;

$authMiddleware = new Middleware\Authorization($container['server'], $container);

$app->any('/','AutenticacionControlador:inicio');
$app->post('/token','AutenticacionControlador:token');
$app->post('/recurso','AutenticacionControlador:recurso');
$app->post('/autorizar','AutenticacionControlador:autorizar');

$app->post('/acceder','AutenticacionControlador:acceder');
$app->post('/registrar','AutenticacionControlador:registrar')
->add(new \DavidePastore\Slim\Validation\Validation(
  array(
    'nombre' => v::notEmpty()->alpha(),
    'dni' => v::notEmpty()->NoWhitespace()->IntVal()->Positive(),
    'contraseña' => v::notEmpty()->length(8,128)
  )));

  //AUTENTICACION POR OAUTH
$app->group('', function () {

  $this->get('/usuario/acceder','AutenticacionControlador:signup');
  $this->post('/usuario/contraseña','AutenticacionControlador:change');

  $this->get('/usuario','UsuarioControlador:getList');
  $this->get('/usuario/{id}','UsuarioControlador:get');
  $this->post('/usuario/imagen','UsuarioControlador:postImagen');
  $this->post('/usuario','UsuarioControlador:post')
  ->add(new \DavidePastore\Slim\Validation\Validation(
    array(
      'nombre' => v::notEmpty()->alpha(),
      'dni' => v::notEmpty()->NoWhitespace()->IntVal()->Positive(),
      'contraseña' => v::notEmpty()->length(8,128)
    )));
  $this->delete('/usuario/{id}','UsuarioControlador:delete');
  $this->put('/usuario/{id}','UsuarioControlador:put')
  ->add(new \DavidePastore\Slim\Validation\Validation(
    array(
      'nombre' => v::notEmpty()->alpha()
    )));

  /////////////////GESTION DE MENUS
  $this->get('/menu','MenuControlador:getList');
  $this->get('/menu/{id}','MenuControlador:get');
  $this->post('/menu','MenuControlador:post')
  ->add(new \DavidePastore\Slim\Validation\Validation(
    array(
      'fecha' => v::notEmpty()->date('d-m-Y')->min(date('d-m-Y')),
      'cantidad' => v::notEmpty()->IntVal()->Positive()
    )));
  $this->post('/menu/{id}','MenuControlador:finalizar');
  $this->delete('/menu/{id}','MenuControlador:delete');
  $this->put('/menu/{id}','MenuControlador:put')
  ->add(new \DavidePastore\Slim\Validation\Validation(
    array(
      'cantidad' => v::notEmpty()->IntVal()->Positive()
    )));

  /////////////////////GESTION DE TICKETS
  $this->get('/ticket','TicketControlador:getList');
  $this->get('/ticket/{id}','TicketControlador:get');
  $this->post('/menu/{idMenu}/ticket','TicketControlador:post');
  $this->delete('/ticket/{id}','TicketControlador:delete');
  $this->put('/ticket/{id}','TicketControlador:put');

  ///////////////////////GESTION TRANSACCION
  $this->get('/transaccion','TransaccionControlador:getList');
  $this->post('/transaccion','TransaccionControlador:post')->add(new \DavidePastore\Slim\Validation\Validation(
    array(
      'fecha' => v::notEmpty()->date('d-m-Y')->min(date('d-m-Y')),
      'monto' => v::notEmpty()->Positive()->FloatVal()
    ))
  );


})->add(new App\Capamedia\LoginCapamedia($container));
