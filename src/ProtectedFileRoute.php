<?php

namespace FoFBA\ProtectedUploads;

use Flarum\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;

class ProtectedFileRoute implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);

        if ($actor->isGuest()) {
            return new Response('php://memory', 403, ['Content-Type' => 'text/plain']);
        }

	$path = $request->getUri()->getPath();
	
	$parts = explode('/', $path);       // Split by slash
	array_shift($parts);
	array_shift($parts);
	array_shift($parts);
	array_shift($parts);
	array_unshift($parts, 'assets/files');
	$filename = implode('/', $parts);  
	//throw new \Exception("Got here with actor: " . $path . " " . $filename . " |");

        $filePath = __DIR__ . '/../../../../public/' . $filename;
	//throw new \Exception("Got here with actor: " . $actor->username . " " . $filename . " " . $filePath);

        if (!file_exists($filePath) || !is_file($filePath)) {
            return new Response('php://memory', 404, ['Content-Type' => 'text/plain']);
        }

        $mime = mime_content_type($filePath) ?: 'application/octet-stream';
	$stream = fopen($filePath, 'rb');
	$body = new Stream($stream);

	return (new Response())
	       ->withBody($body)
	       ->withStatus(200)
	       ->withHeader('Content-Type', $mime)
	       ->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"');

    }
}
