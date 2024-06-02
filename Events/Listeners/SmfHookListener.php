<?php

declare(strict_types=1);

namespace Bugo\LightPortal\Events\Listeners;

use Bugo\LightPortal\Events\Event;
use Bugo\LightPortal\Filters\SnakeNameFilter;
use Bugo\LightPortal\RequestAwareInterface;
use Bugo\LightPortal\RequestAwareTrait;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;

final class SmfHookListener extends AbstractListenerAggregate implements RequestAwareInterface
{
	use RequestAwareTrait;

	public function __construct(
		private SnakeNameFilter $snakeNameFilter,
	) {
	}

	public function attach(EventManagerInterface $events, $priority = 1)
	{
		$this->listeners[] = $events->attach(
			Event::SmfHook->value,
			[$this, 'onSmfHook'],
			$priority
		);

		$this->listeners[] = $events->attach(
			Event::DefaultAction->value,
			[$this, 'onDefaultAction'],
			$priority
		);
	}

	public function onSmfHook(EventInterface $event)
	{
	}

	public function onDefaultAction(EventInterface $event)
	{

	}
}
