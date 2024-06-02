<?php

declare(strict_types=1);

namespace Bugo\LightPortal\Service;

use Laminas\Http\PhpEnvironment\Request;
use Psr\Container\ContainerInterface;

final class RequestFactory
{
	public function __invoke(ContainerInterface $container): Request
	{
		return new Request();
	}
}
