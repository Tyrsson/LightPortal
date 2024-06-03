<?php

declare(strict_types=1);

namespace Bugo\LightPortal\Events;

enum Event: string
{
	case Api           = '::ApiAction';
	case Actions       = '::action';
	case DefaultAction = '::defaultAction';
	case CurrentAction = '::currentAction';
	case LoadTheme     = '::loadTheme';
	case InitAddons    = 'plugin.init';
	case RunAddon      = 'plugin.run';
	case SmfHook       = 'smf.hook';
	case PortalHook    = 'portal.hook';
}
