<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;
use Firebase\JWT\JWT; 
use Slim\Views\Twig;

class Front{
    public function InicioFront(Request $request, Response $response, array $args){

        $view = Twig::fromRequest($request);
        return $view->render($response, 'login.html');
    }

    public function LoginUsuarios(Request $request, Response $response, array $args){
        $view = Twig::fromRequest($request);
        return $view->render($response, 'registro.php');

    }
    public function MenuPrincipal(Request $request, Response $response, array $args){
        $view = Twig::fromRequest($request);
        return $view->render($response, 'principal.php');

    }
    
}