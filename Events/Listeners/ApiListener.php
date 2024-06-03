<?php

declare(strict_types=1);

namespace Bugo\LightPortal\Events\Listeners;

use Bugo\LightPortal\Events\Event as EventType;
use Bugo\LightPortal\RequestAwareInterface;
use Bugo\LightPortal\RequestAwareTrait;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Header\ContentType;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Http\PhpEnvironment\Request;

final class ApiListener extends AbstractListenerAggregate implements RequestAwareInterface
{
	use RequestAwareTrait;

	private const TARGET_PARAM = 'api';

	public function attach(EventManagerInterface $events, $priority = 1)
	{
		$this->listeners[] = $events->attach(
			EventType::CurrentAction->value,
			[$this, 'onApiAction'],
			$priority
		);
	}

	public function onApiAction(EventInterface $event)
	{
		global $modSettings;
		\ob_end_clean();
		if (!empty($modSettings['enableCompressedOutput']))
			@ob_start('ob_gzhandler');
		else
			ob_start();
		$eventContext = $event->getParams();
		$action = $this->request->getQuery()->get('action');
		$sa     = $this->request->getQuery()->get('sa');
		if ($action === self::TARGET_PARAM && $context['template_layers'] !== []) {
			$context['template_layers'] = [];
		}
		$response = new Response();
		$response->setHeadersSentHandler(function ($response): void {
			throw new RuntimeException('Cannot send headers, headers already sent');
		});
		$response->setStatusCode(Response::STATUS_CODE_200);
		$contentType = new ContentType('application/json');
		$headers = $response->getHeaders();
		//$headers->addHeaderLine('content-type: application/json');
		$headers->addHeaders([
			'X-Content-Type-Options' => 'nosniff',
			'Content-Type' => 'application/json',
			'HeaderField1' => 'header-field-value1',
			'HeaderField2' => 'header-field-value2',
		]);
		$data = [
			'body_param_one' => 'body_param_one_value',
			'body_param_two' => 'body_param_two_value',
		];
		$response->setContent(\json_encode($data));
		$response->send();
		 obExit(false);
	}
}
