<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

use Slim\Factory\AppFactory;
use \Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;


require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/poo/Usuario.php';
require_once __DIR__ . '/../src/poo/Juguete.php';
require_once __DIR__ . '/../src/poo/MW.php';
require_once __DIR__ . '/../src/poo/Front.php';

$app = AppFactory::create();


//SE TIENE QUE AGREGAR EL COMPONENTE TWIG --> composer require slim/twig-view
//SE ESTABLECE EL PATH DE LOS TEMPLATES
$twig = Twig::create('../src/views', ['cache' => false]);
//SE AGREGA EL MIDDLEWARE DE TWIG
$app->add(TwigMiddleware::create($app, $twig));


$app->post('/login[/]',\Usuario::class . ":IniciarSesion")->add(\MW::class . ":VerificarCorreoYClaveBD") -> add(\MW::class . "::ValidarVacioCorreoYClave");
$app->get('/login[/]',\Usuario::class . ":VerificarSesion");
$app->get('/',\Usuario::class . ":ListadoUsuarios");

$app->post('/',\Juguete::class . ":AltaJuguetes") -> add(\MW::class . ":VerificarTokenValido");
$app->get('/juguetes[/]',\Juguete::class . ":ListadoJuguetes");

$app->get('/loginusuarios',Front::class . ':LoginUsuarios');
$app->get('/front-end',\Front::class . ':InicioFront');  
$app->get('/principal',Front::class . ':MenuPrincipal');

$app->get('/front-end-registro',\Juguete::class . ":ListadoJuguetes");
$app->get('/front-end-principal',\Juguete::class . ":ListadoJuguetes");

$app->group('/toys', function (RouteCollectorProxy $grupo) 
{  
    $grupo->delete('/{id_juguete}[/]', \Juguete::class . ":BorrarJuguetePorID");
    $grupo->post('/', \Juguete::class . ":ModificarJuguetePorID");    
});



//CORRE LA APLICACIÓN.
$app->run();
