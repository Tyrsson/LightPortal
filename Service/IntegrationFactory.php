<?php

declare(strict_types=1);

namespace Bugo\LightPortal\Service;

use Bugo\LightPortal\AddonHandler;
use Bugo\LightPortal\Integration;
use Bugo\LightPortal\Actions;
use Psr\Container\ContainerInterface;

final class IntegrationFactory
{
	public function __invoke(ContainerInterface $container): Integration
	{
		return new Integration();
	}
}
