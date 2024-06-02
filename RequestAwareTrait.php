<?php

declare(strict_types=1);

namespace Bugo\LightPortal;

use Laminas\Http\PhpEnvironment\Request;

trait RequestAwareTrait
{
	protected ?Request $request = null;

	public function getRequest(): ?Request
	{
		return $this->request;
	}

	public function setRequest(Request $request): void
	{
		$this->request = $request;
	}
}
