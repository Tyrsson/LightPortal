<?php

declare(strict_types=1);

namespace Bugo\LightPortal\Service;

use Bugo\LightPortal\AddonHandler;
use Bugo\LightPortal\App;
use Bugo\LightPortal\Events\Listeners\SmfHookListener;
use Bugo\LightPortal\Integration;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Psr\Container\ContainerInterface;

final class AppFactory
{
	public function __invoke(ContainerInterface $container): App
	{
		$eventManager = $container->get(EventManagerInterface::class);
		$eventManager->setIdentifiers([App::class]);
		/** @var SmfHookListener */
		$listener = $container->get(SmfHookListener::class);
		$listener->attach($eventManager, 10000); // attach listener and insure this listener runs first
		$app = new App();
		$app->setEventManager($eventManager);
		return $app;
	}
}
