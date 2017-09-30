<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;

    $capsule->addConnection($container['settings']['db']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};
$app->getContainer()->get("db");

$container['view'] = function($container){
	$view = new \Slim\Views\Twig(__DIR__ . '/../templates' , [
		'cache' => false,
		]);

	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
		));

	return $view;
};


$container['UsuarioControlador'] = function ($container) {
	return new \App\Controladores\UsuarioControlador($container);
};
$container['MenuControlador'] = function ($container) {
	return new \App\Controladores\Comedor\MenuControlador($container);
};
$container['TicketControlador'] = function ($container) {
	return new \App\Controladores\Comedor\TicketControlador($container);
};
$container['TransaccionControlador'] = function ($container) {
	return new \App\Controladores\Comedor\TransaccionControlador($container);
};
$container['AutenticacionControlador'] = function ($container) {
	return new \App\Controladores\AutenticacionControlador($container);
};

$container['server'] = function ($container) {
  $storage = new OAuth2\Storage\Pdo(array(
      'dsn' => 'pgsql:dbname='.$container['settings']['db']['database'].
               ';host='.$container['settings']['db']['host'],
      'username' => $container['settings']['db']['username'],
      'password' => $container['settings']['db']['password'],
    )
  );
  $server = new OAuth2\Server($storage);
  $server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));
  $server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));
  $defaultScope = 'basico';
  $supportedScopes = array(
      'basico',
      'medio',
      'completo'
  );
  $scope = new OAuth2\Scope(array(
    'default_scope' => $defaultScope,
    'supported_scopes' => $supportedScopes
  ));
  $server->setScopeUtil($scope);
	return $server;
};

$container['http'] = function ($container){
  return new \GuzzleHttp\Client([
    'headers' => [ 'Content-Type' => 'application/json' ],
    //'base_uri' => 'http://proyectosinformaticos.esy.es/apirest.slim/public/',
    'base_uri' => 'http://localhost/proyectos/comedor-rest/public/',
    'timeout'  => 10.0,
  ]);
};

$container['eloquence'] = function ($c) {
     $eloquence = new Sofa\Eloquence\ServiceProvider('Sofa\Eloquence\ServiceProvider');
     return $eloquence;
};

$eloquence = $container['eloquence'];
$eloquence->boot();
