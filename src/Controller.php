<?php

namespace App;

use App\Session\Session;
use Core\Contracts\ManagerInterface;
use Core\Http\Handler\RequestHandler;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\RequestInterface;
use Router\Router;

class Controller
{
    protected Router $router;
    protected App $app;
    protected RequestInterface $request;
    protected RequestHandler $requestHandler;
	protected array $errors = [];
	protected array $infos = [];


    public function __construct()
    {
        $this->app = App::getInstance();
        $this->router = $this->app->get('router');
        $this->request = ServerRequest::fromGlobals();
        $this->requestHandler = new RequestHandler($this->request);
    }

    public function redirect(string $url)
    {
        $this->app->redirectTo($url);
    }

    public function routeTo(string $routeName, array $params)
    {
        App::redirectToRoute($routeName, $params);
    }

    public function modelManager(string $class, string $managerClass = ''):ManagerInterface
    {
        return $this->app->modelManager($class, $managerClass);
	}

	public function render($content, $infos = [], $errors = [])
	{
		return App::render($content, $infos ?? $this->infos, $errors ?? $this->errors);
	}

	protected function get($key)
	{
		return $this->app::get($key);
	}

	protected function getEnvironment()
	{
		return $this->app::get('environment');
	}

	protected function isInDevelopment()
	{
		$environment = $this->getEnvironment();
		return $environment == 'development';
	}

	protected function processError(string $message, $priority = 600)
	{
		if ($this->isInDevelopment()) {
			$this->log($message, 'debug');
			$this->errors[] = $message;
		} else {
			/**
			 * Severity codes from RFC 5424
			 * 0 Emergency: system is unusable
			 * 1 Alert: action must be taken immediately
			 * 2 Critical: critical conditions
			 * 3 Error: error conditions
			 * 4 Warning: warning conditions
			 * 5 Notice: normal but significant condition
			 * 6 Informational: informational messages
			 * 7 Debug: debug-level messages
			 */
			switch ($priority/100) {
				case 0:
					$this->log($message, 'emergency');
					break;
				case 1:
					$this->log($message, 'alert');
					break;
				case 2:
					$this->log($message, 'critical');
					break;
				case 3:
					$this->log($message, 'error');
					break;
				case 4:
					$this->log($message, 'warning');
					break;
				case 5:
					$this->log($message, 'notice');
					break;
				case 6:
					$this->log($message, 'info');
					$this->alert($message, $priority);
					break;
				case 7:
					$this->log($message, 'debug');
					break;
				default:
					$this->log($message, 'debug');
			}
			$this->log($message, $priority);
		}
		return '';
	}

	protected function log(string $message, $priority, array $context = [])
	{
		if($this->isInDevelopment()) {
			$this->logger->debug($message, $context);
		} else {
			switch ($priority) {
				/**
				 * System is unusable.
				 */
				case 'emergency':
					$this->logger->emergency($message, $context);
					break;
				/**
				 * Action must be taken immediately.
				 * Example: Entire website down, database unavailable, etc. This should
				 * trigger the SMS alerts and wake you up.
				 */
				case 'alert':
					$this->logger->alert($message, $context);
					break;

				/**
				 * Critical conditions.
				 * Example: Application component unavailable, unexpected exception.
				 */
				case 'critical':
					$this->logger->critical($message, $context);
					break;

				/**
				 * Runtime errors that do not require immediate action but should typically
				 * be logged and monitored.
				 */
				case 'error':
					$this->logger->error($message, $context);
					break;

				/**
				 * Exceptional occurrences that are not errors.
				 * Example: Use of deprecated APIs, poor use of an API, undesirable things
				 * that are not necessarily wrong.
				 */
				case 'warning':
					$this->logger->warning($message, $context);
					break;

				/**
				 * Normal but significant events.
				 */
				case 'notice':
					$this->logger->notice($message);
					break;

				/**
				 * Interesting events.
				 * Example: User logs in, SQL logs.
				 */
				case 'info':

					$this->logger->info($message);
					break;
    }
}

	}

	protected function alert(string $message, $priority)
	{
		$messages = Session::get('messages') ?? [];
		if ($messages) {
			$messages[$priority][] = $message;
		}
		Session::set('messages', $messages);
	}

	protected function addInfoMessage(string $message)
	{
		$this->infos[] = $message;
	}

	protected function handleRequest(Model $model, $prefix = '')
	{
		$this->requestHandler->handle($model, $prefix);
	}

	protected function getFromRequest($field)
	{
		return $this->requestHandler->get($field);
	}
}
