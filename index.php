<?php
require 'vendor/autoload.php';
use Symfony\Component\Process\Process;

$app = new \Slim\Slim();

$app->get('/', function () use($app) {
    $app->render('doc.php');
});

$app->get('/api/v1/:query_type/:query_value', function ($query_type, $query_value) use($app) {

	$process = new Process(sprintf('casperjs gst.proc %s "%s"', $query_type, $query_value));
	$process->run();

	if (!$process->isSuccessful()) {
		throw new \RuntimeException($process->getErrorOutput());
	}

	$response = $app->response();
	$response['Content-Type'] = 'application/json';
	$response->status(200);
	$response->body(json_encode(json_decode($process->getOutput()), JSON_PRETTY_PRINT));
});

$app->run();