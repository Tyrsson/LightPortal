<?php

declare(strict_types=1);

namespace Bugo\LightPortal;

use Laminas\Http\PhpEnvironment\Request;

interface RequestAwareInterface
{
	public function getRequest(): ?Request;
	public function setRequest(Request $request): void;
}
