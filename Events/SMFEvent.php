<?php

declare(strict_types=1);

namespace Bugo\LightPortal\Events;

enum SMFEvent: string
{
	case DefaultAction = 'integrate_default_action';
}
