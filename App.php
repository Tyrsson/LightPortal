<?php

declare(strict_types=1);

namespace Bugo\LightPortal;

use Bugo\LightPortal\Events\Event as EventType;
use Bugo\LightPortal\Integration;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;

/**
 * app.php
 *
 * @package Light Portal
 * @link https://dragomano.ru/mods/light-portal
 * @author Bugo <bugo@dragomano.ru>
 * @copyright 2019-2024 Bugo
 * @license https://spdx.org/licenses/GPL-3.0-or-later.html GPL-3.0-or-later
 *
 * @version 2.6
 */

if (! defined('SMF'))
	die('We gotta get out of here!');

final class App implements EventManagerAwareInterface, RequestAwareInterface
{
	use EventManagerAwareTrait;
	use RequestAwareTrait;

	public function run()
	{
		$integration = Integration::getInstance(
			$this->getEventManager(),
			$this->getRequest()
		);
		$integration::init();
	}
}
