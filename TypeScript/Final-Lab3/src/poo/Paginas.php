<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class Pagina
{
    public function login(Request $request, Response $response, array $args): Response
    {
        header("Location:login.html");
        return $response;
    }

}