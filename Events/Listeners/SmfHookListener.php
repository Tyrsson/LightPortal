<?php

declare(strict_types=1);

namespace Bugo\LightPortal\Events\Listeners;

use Bugo\LightPortal\Events\CurrentActionEvent;
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

		$this->listeners[] = $events->attach(
			Event::CurrentAction->value,
			[$this, 'onCurrentAction'],
			$priority
		);
	}

	public function onSmfHook(EventInterface $event)
	{
	}

	public function onDefaultAction(EventInterface $event)
	{

	}

	public function onCurrentAction(CurrentActionEvent $event)
	{
		$target  = $event->getTarget();
		$action  = $event->getParam('action');
		$example = $target->doSomething();
		$action  = $example->get('action', 'some_default');
	}
}
