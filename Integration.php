<?php declare(strict_types=1);

/**
 * Integration.php
 *
 * @package Light Portal
 * @link https://dragomano.ru/mods/light-portal
 * @author Bugo <bugo@dragomano.ru>
 * @copyright 2019-2024 Bugo
 * @license https://spdx.org/licenses/GPL-3.0-or-later.html GPL-3.0-or-later
 *
 * @version 2.6
 */

namespace Bugo\LightPortal;

use Bugo\LightPortal\Events\CurrentActionEvent;
use Bugo\LightPortal\Events\Event as EventType;
use Bugo\LightPortal\Events\SMFEvent;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\PhpEnvironment\Request;
use stdClass;

use function add_integration_function;

use const SMF;

if (! defined('SMF'))
	die('No direct access...');

/**
 * This class contains only hook methods
 */
final class Integration
{
	protected static $eventManager;
	protected static $instance;
	protected static $request;

	public static function getInstance(
		EventManagerInterface $eventManager,
		Request $request,
	): self {
		static::$eventManager = $eventManager;
		static::$eventManager->addIdentifiers([static::class]);
		static::$request = $request;

		if (! isset(static::$instance)) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	public static function init(): void
	{
		if ('BACKGROUND' !== SMF) {
			add_integration_function(
				hook: SMFEvent::DefaultAction->value,
				function: __CLASS__ . EventType::DefaultAction->value,
				permanent: false,
				file: __FILE__,
				object: true
			);
			add_integration_function(
				hook: SMFEvent::CurrentAction->value,
				function: __CLASS__ . EventType::CurrentAction->value,
				permanent: false,
				file: __FILE__,
				object: true
			);
			add_integration_function(
				hook: SMFEvent::LoadTheme->value,
				function: __CLASS__ . EventType::LoadTheme->value,
				permanent: false,
				object: true
			);
		}

		//$this->applyHook('init');
		// $this->applyHook('pre_css_output');
		// $this->applyHook('load_theme');
		// $this->applyHook('redirect', 'changeRedirect');
		// $this->applyHook('actions');
		// $this->applyHook('default_action');
		// $this->applyHook('current_action');
		// $this->applyHook('current_action', 'currentPage');
		// $this->applyHook('menu_buttons');
		// $this->applyHook('display_buttons');
		// $this->applyHook('delete_members');
		// $this->applyHook('load_illegal_guest_permissions');
		// $this->applyHook('load_permissions');
		// $this->applyHook('alert_types');
		// $this->applyHook('fetch_alerts');
		// $this->applyHook('profile_areas');
		// $this->applyHook('profile_popup');
		// $this->applyHook('download_request');
		// $this->applyHook('whos_online');
	}

	// non proxied method
	public static function actions(&$actionArray) {
		$actionArray['api'] = ['ApiAction.php', 'ApiMain'];
	}

	public static function defaultAction()
	{
		/**
		 * from this point the event workflow can get as complex as required
		 *
		 * Bare in mind this is the absolute bare minimum implementation
		 * This could use a factoried custom Event object and we could call triggerEvent and pass the event.
		 * We could also pass a target here. I mean its hard to explain how many different ways this could
		 * work.
		 */
		static::$eventManager->trigger(
			EventType::DefaultAction->value,
			null,
			[
				'key_one' => 'value_one'
			],
		);
	}

	public static function currentAction(string &$action)
	{
		// Custom Event example
		$currentAction = new CurrentActionEvent(EventType::CurrentAction->value);
		$currentAction->setParams(['action' => $action]);
		static::$eventManager->triggerEvent($currentAction);
	}

	/**
	 * trigger order 1
	 * @return void
	 */
	public static function loadTheme()
	{
		$event = new Event(EventType::LoadTheme->value);
		$event->setParams(['action', static::$request->getQuery()->get('action', 'unknown_action')]);
		static::$eventManager->triggerEvent($event);
	}

	public function preCssOutput(): void
	{
		if (SMF === 'BACKGROUND')
			return;

		echo "\n\t" . Html::el('link', [
			'rel'  => 'preconnect',
			'href' => '//cdn.jsdelivr.net',
		])->toHtml();

		if (! empty(Utils::$context['portal_next_page'])) {
			echo "\n\t" . Html::el('link', [
				'rel'  => 'prerender',
				'href' => Utils::$context['portal_next_page'],
			])->toHtml();
		}

		$styles = [];

		if (empty(Config::$modSettings['lp_fa_source']) || Config::$modSettings['lp_fa_source'] === 'css_cdn') {
			$styles[] = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css';
		}

		$this->hook('preloadStyles', [&$styles]);

		foreach ($styles as $style) {
			echo "\n\t" . Html::el('link', [
				'rel'    => 'preload',
				'href'   => $style,
				'as'     => 'style',
				'onload' => "this.onload=null;this.rel='stylesheet'",
			])->toHtml();
		}
	}

	// public function loadTheme(): void
	// {
	// 	if ($this->isPortalCanBeLoaded() === false)
	// 		return;

	// 	Lang::load('LightPortal/LightPortal');

	// 	$this->defineVars();

	// 	$this->loadAssets(new Zero());

	// 	$this->hook('init');
	// }

	// public function loadTheme(): void
	// {
	// 	if ($this->isPortalCanBeLoaded() === false)
	// 		return;

	// 	Lang::load('LightPortal/LightPortal');

	// 	$this->defineVars();

	// 	$this->loadAssets(new Zero());

	// 	// Add some demo plugins for testing
	// 	Config::updateModSettings(['lp_enabled_plugins' => 'UserInfo,Example']);

	// 	// Check init method for all plugins
	// 	$this->hook('init');
	// 	// We have to get hello message from UserInfo plugin when it enabled

	// 	// Check example method with params
	// 	$params = [];
	// 	$this->hook('exampleMethod', [&$params]);
	// 	var_dump($params);
	// 	// We have to get [1] from Example plugin when it enabled

	// 	// Change enabled plugins
	// 	Config::updateModSettings(['lp_enabled_plugins' => 'UserInfo']);

	// 	$this->hook('init');
	// 	// We have to get hello message from UserInfo plugin when it enabled

	// 	$params = [];
	// 	$this->hook('exampleMethod', [&$params]);
	// 	var_dump($params);
	// 	// We have to get [] because there is no active plugins with exampleMethod method
	// }

	/**
	 * @hook integrate_redirect
	 */
	public function changeRedirect(string &$setLocation): void
	{
		if (empty(Config::$modSettings['lp_frontpage_mode']) || $this->isStandaloneMode())
			return;

		if ($this->getRequest()->is('markasread'))
			$setLocation = Config::$scripturl . '?action=forum';
	}

	// public function actions(array &$actions): void
	// {
	// 	if (! empty(Config::$modSettings['lp_frontpage_mode']))
	// 		$actions[LP_ACTION] = [false, [new FrontPage(), 'show']];

	// 	$actions['forum'] = [false, [new BoardIndex(), 'show']];

	// 	Theme::load();

	// 	if ($this->getRequest()->is(LP_ACTION) && Utils::$context['current_subaction'] === 'categories')
	// 		$this->cat->show(new Page());

	// 	if ($this->getRequest()->is(LP_ACTION) && Utils::$context['current_subaction'] === 'tags')
	// 		($this->tag)->show(new Page());

	// 	if ($this->getRequest()->is(LP_ACTION) && Utils::$context['current_subaction'] === 'promote')
	// 		$this->promoteTopic();

	// 	if (empty(Config::$modSettings['lp_standalone_mode']))
	// 		return;

	// 	$this->unsetDisabledActions($actions);

	// 	$this->redirectFromDisabledActions();
	// }

	// public function defaultAction(): mixed
	// {
	// 	if ($this->getRequest()->isNotEmpty(LP_PAGE_PARAM))
	// 		return $this->callHelper([new Page(), 'show']);

	// 	if (empty(Config::$modSettings['lp_frontpage_mode']) || $this->isStandaloneMode())
	// 		return $this->callHelper([new BoardIndex(), 'show']);

	// 	return $this->callHelper([new FrontPage(), 'show']);
	// }

	/**
	 * Add a selection for some menu items when navigating to the specified areas
	 *
	 * Добавляем выделение для некоторых пунктов меню при переходе в указанные области
	 */
	// public function currentAction(string &$action): void
	// {
	// 	if (empty(Config::$modSettings['lp_frontpage_mode']))
	// 		return;

	// 	if ($this->getRequest()->isEmpty('action')) {
	// 		$action = LP_ACTION;

	// 		if ($this->isStandaloneMode() && Config::$modSettings['lp_standalone_url'] !== $this->getRequest()->url()) {
	// 			$action = 'forum';
	// 		}

	// 		if ($this->getRequest()->isNotEmpty(LP_PAGE_PARAM)) {
	// 			$action = LP_ACTION;
	// 		}
	// 	} else {
	// 		$action = empty(Config::$modSettings['lp_standalone_mode']) && $this->getRequest()->is('forum')
	// 			? 'home'
	// 			: Utils::$context['current_action'];
	// 	}

	// 	if (isset(Utils::$context['current_board']) || $this->getRequest()->is('keywords')) {
	// 		$action = empty(Config::$modSettings['lp_standalone_mode'])
	// 			? 'home'
	// 			: (in_array('forum', $this->getDisabledActions()) ? LP_ACTION : 'forum');
	// 	}
	// }

	/**
	 * @hook integrate_current_action
	 */
	public function currentPage(string &$action): void
	{
		if (empty(Utils::$context['lp_page']) || empty(Utils::$context['lp_menu_pages']))
			return;

		if (empty(Utils::$context['lp_menu_pages'][Utils::$context['lp_page']['id']]))
			return;

		if ($this->getRequest()->url() === LP_PAGE_URL . Utils::$context['lp_page']['slug']) {
			$action = 'portal_page_' . $this->request(LP_PAGE_PARAM);
		}
	}

	public function menuButtons(array &$buttons): void
	{
		if ($this->isPortalCanBeLoaded() === false)
			return;

		$this->callHelper([new Block(), 'show']);

		$this->prepareAdminButtons($buttons);

		$this->prepareModerationButtons($buttons);

		$this->preparePageButtons($buttons);

		$this->showDebugInfo();

		if (empty(Config::$modSettings['lp_frontpage_mode']))
			return;

		$this->preparePortalButtons($buttons);

		$this->fixCanonicalUrl();

		$this->fixLinktree();
	}

	/**
	 * Add "Promote to frontpage" (or "Remove from frontpage") button if the "Selected topics" portal mode is selected
	 *
	 * Добавляем кнопку «Добавить на главную» (или «Убрать с главной»), если выбран режим портала «Выбранные темы»
	 */
	public function displayButtons(): void
	{
		if (empty(User::$info['is_admin']) || $this->isFrontpageMode('chosen_topics') === false)
			return;

		Utils::$context['normal_buttons']['lp_promote'] = [
			'text' => in_array(Utils::$context['current_topic'], Utils::$context['lp_frontpage_topics'])
				? 'lp_remove_from_fp'
				: 'lp_promote_to_fp',
			'url'  => LP_BASE_URL . ';sa=promote;t=' . Utils::$context['current_topic']
		];
	}

	/**
	 * Remove comments, and alerts on deleting members
	 *
	 * Удаляем комментарии и оповещения при удалении пользователей
	 */
	public function deleteMembers(array $users): void
	{
		if (empty($users))
			return;

		Db::$db->query('', '
			DELETE FROM {db_prefix}lp_comments
			WHERE author_id IN ({array_int:users})',
			[
				'users' => $users,
			]
		);

		Db::$db->query('', '
			DELETE FROM {db_prefix}user_alerts
			WHERE id_member IN ({array_int:users})
				OR id_member_started IN ({array_int:users})',
			[
				'users' => $users,
			]
		);

		$this->cache()->flush();
	}

	public function loadIllegalGuestPermissions(): void
	{
		Utils::$context['non_guest_permissions'] = array_merge(
			Utils::$context['non_guest_permissions'],
			[
				'light_portal_manage_pages_own',
				'light_portal_manage_pages_any',
				'light_portal_manage_pages',
				'light_portal_approve_pages',
			]
		);
	}

	public function loadPermissions(
		array &$permissionGroups,
		array &$permissionList,
		array &$leftPermissionGroups
	): void
	{
		Lang::$txt['permissiongroup_light_portal'] = LP_NAME;

		$permissionList['membergroup']['light_portal_view']          = [false, 'light_portal'];
		$permissionList['membergroup']['light_portal_manage_pages']  = [true, 'light_portal'];
		$permissionList['membergroup']['light_portal_approve_pages'] = [false, 'light_portal'];

		$permissionGroups['membergroup'][] = $leftPermissionGroups[] = 'light_portal';
	}

	public function alertTypes(array &$types): void
	{
		Lang::$txt['alert_group_light_portal'] = Lang::$txt['lp_portal'];

		if ($this->getCommentBlockType() === 'default') {
			$types['light_portal'] = [
				'page_comment' => [
					'alert' => 'yes',
					'email' => 'never',
					'permission' => [
						'name'     => 'light_portal_manage_pages_own',
						'is_board' => false,
					]
				],
				'page_comment_reply' => [
					'alert' => 'yes',
					'email' => 'never',
					'permission' => [
						'name'     => 'light_portal_view',
						'is_board' => false,
					]
				]
			];
		}

		$types['light_portal']['page_unapproved'] = [
			'alert' => 'yes',
			'email' => 'yes',
			'permission' => [
				'name'     => 'light_portal_manage_pages_any',
				'is_board' => false,
			]
		];
	}

	public function fetchAlerts(array &$alerts): void
	{
		foreach ($alerts as $id => $alert) {
			if (in_array($alert['content_action'], ['page_comment', 'page_comment_reply', 'page_unapproved'])) {
				$icon = $alert['content_action'] === 'page_comment' ? 'im_off' : 'im_on';
				$icon = $alert['content_action'] === 'page_unapproved' ? 'news' : $icon;

				if ($alert['sender_id'] !== User::$info['id']) {
					$alerts[$id]['icon'] = '<span class="alert_icon main_icons ' . $icon . '"></span>';
					$alerts[$id]['text'] = Lang::getTxt(
						'alert_' . $alert['content_type'] . '_' . $alert['content_action'],
						['gender' => $alert['extra']['sender_gender']]
					);

					$link = Config::$scripturl . '?action=profile;u=' . $alert['sender_id'];

					$substitutions = [
						'{member_link}' => $alert['sender_id'] && $alert['show_links']
							? '<a href="' . $link . '">' . $alert['sender_name'] . '</a>'
							: '<strong>' . $alert['sender_name'] . '</strong>',
						'{content_subject}' => '(' . $alert['extra']['content_subject'] . ')'
					];

					$alerts[$id]['text'] = strtr($alerts[$id]['text'], $substitutions);
					$alerts[$id]['target_href'] = $alert['extra']['content_link'];
				} else {
					unset($alerts[$id]);
				}
			}
		}
	}

	public function profileAreas(array &$areas): void
	{
		if (Utils::$context['user']['is_admin'])
			return;

		$areas['info']['areas']['lp_my_pages'] = [
			'label'      => Lang::$txt['lp_my_pages'],
			'custom_url' => Config::$scripturl . '?action=admin;area=lp_pages',
			'icon'       => 'reports',
			'enabled'    => $this->request('area') === 'popup',
			'permission' => [
				'own' => 'light_portal_manage_pages_own',
			],
		];
	}

	public function profilePopup(array &$items): void
	{
		if (Utils::$context['user']['is_admin'] || empty(Utils::$context['allow_light_portal_manage_pages_own']))
			return;

		$counter = 0;
		foreach ($items as $item) {
			$counter++;

			if ($item['area'] === 'showdrafts')
				break;
		}

		$items = array_merge(
			array_slice($items, 0, $counter, true),
			[
				[
					'menu'  => 'info',
					'area'  => 'lp_my_pages',
					'title' => Lang::$txt['lp_my_pages']
				]
			],
			array_slice($items, $counter, null, true)
		);
	}

	/**
	 * @hook integrate_download_request
	 */
	public function downloadRequest(&$attachRequest): void
	{
		$this->loadTheme();

		$this->hook('downloadRequest', [&$attachRequest]);
	}

	public function whosOnline(array $actions): string
	{
		$result = '';
		if (empty($actions['action']) && empty($actions['board'])) {
			$result = sprintf(Lang::$txt['lp_who_viewing_frontpage'], Config::$scripturl);

			if ($this->isStandaloneMode()) {
				$result = Lang::getTxt('lp_who_viewing_index', [
					Config::$modSettings['lp_standalone_url'],
					Config::$scripturl
				]);
			}
		}

		if (isset($actions[LP_PAGE_PARAM])) {
			$result = sprintf(
				Lang::$txt['lp_who_viewing_page'],
				LP_PAGE_URL . $actions[LP_PAGE_PARAM]
			);
		}

		if (empty($actions['action']))
			return $result;

		if ($actions['action'] === LP_ACTION) {
			$result = sprintf(Lang::$txt['lp_who_viewing_frontpage'], LP_BASE_URL);

			if (isset($actions['sa']) && $actions['sa'] === 'tags') {
				$tags = $this->getEntityData('tag');

				$result = isset($actions['id'])
					? Lang::getTxt('lp_who_viewing_the_tag', [
						LP_BASE_URL . ';sa=tags;id=' . $actions['id'],
						$tags[$actions['id']]
					])
					: sprintf(
						Lang::$txt['lp_who_viewing_tags'],
						LP_BASE_URL . ';sa=tags'
					);
			}

			if (isset($actions['sa']) && $actions['sa'] === 'categories') {
				$categories = $this->getEntityData('category');

				$result = isset($actions['id'])
					? Lang::getTxt('lp_who_viewing_the_category', [
						LP_BASE_URL . ';sa=categories;id=' . $actions['id'],
						$categories[$actions['id']]['name']
					])
					: sprintf(
						Lang::$txt['lp_who_viewing_categories'],
						LP_BASE_URL . ';sa=categories'
					);
			}
		}

		if ($actions['action'] === 'forum') {
			$result = Lang::getTxt('who_index', [
				Config::$scripturl . '?action=forum',
				Utils::$context['forum_name']
			]);
		}

		return $result;
	}
}
