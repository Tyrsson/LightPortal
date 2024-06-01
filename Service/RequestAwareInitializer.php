<?php

declare(strict_types=1);

namespace Bugo\LightPortal\Service;

use Bugo\LightPortal\RequestAwareInterface;
use Bugo\LightPortal\Utils\Request;
use Laminas\ServiceManager\Initializer\InitializerInterface;
use Psr\Container\ContainerInterface;

final class RequestAwareInitializer implements InitializerInterface
{
    public function __invoke(ContainerInterface $container, $instance)
	{
		if (! $instance instanceof RequestAwareInterface) {
			return;
		}
		/** @var Request */
		$request = $instance->getRequest();
		if ($request instanceof Request) {
			return;
		}
		$instance->setRequest($container->get(Request::class));
	}
}
