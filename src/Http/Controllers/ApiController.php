<?php

namespace PhpDocGen\Http\Controllers;

use PhpDocGen\DocGen\TemplateCompiler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpDocGen\YandexDisk\DiskManager;

class ApiController {
	public function compile(Request $request, Response $response): Response {
		$requestBody = $request->getParsedBody();
		$templateName = $requestBody["filename"];
		$context = $requestBody["context"];
		if (!$templateName || !$context) { /*TODO: custom exception response*/ }
		$diskManager = new DiskManager("AQAAAABiNeRgAADLW3lAxCW_1EiQpnnpANHuzL8");
		$diskManager->download(
			"/" . $templateName,
			dirname(__DIR__, 3) . "/storage/templates/" . $templateName);
		
		$templateCompiler = new TemplateCompiler(dirname(__DIR__, 3) . "/storage");
		$documentName = $templateCompiler->compileTemplate($templateName, $context);
		
		$diskManager->upload(
			dirname(__DIR__, 3) . "/storage/documents/" . $documentName,
			"/" . $documentName
		);
		$diskManager->publish("/" . $documentName);
		$metaInfo = $diskManager->getMetaInfo("/" . $documentName);
		$responseBody = [
			"code" => 200,
			"href" => $metaInfo["public_url"],
		];
		$responseBody = json_encode($responseBody);
		
		$response->getBody()->write($responseBody);
		return $response->withHeader("Content-Type", "application/json");
	}
}