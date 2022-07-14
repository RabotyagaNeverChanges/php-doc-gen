<?php

namespace PhpDocGen\Http\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ApiController {
	public function compile(Request $request, Response $response, array $args): Response {
		$requestBody = $request->getParsedBody();
		
		$responseBody = [
			"code" => 200,
			"message" =>  $requestBody
		];
		$responseBody = json_encode($responseBody);
		
		$response->getBody()->write($responseBody);
		$response->withHeader("Content-type", "application/json");
		return $response;
	}
}