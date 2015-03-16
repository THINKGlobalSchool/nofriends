<?php
/**
 * Elgg - No Friends start.php
 *
 * @package NoFriends
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 *
 * Tweak list:
 *
 * - Friends actions unregistered
 * - Friends page handlers unregistered
 * - Forwarding to 404 from all page handlers that implement friends
 * - User hover menu
 * - Topbar menu
 * - Page menu items (sidebar)
 * - Filter menu
 * - Friends tab on tabbed profile removed
 * - input/userpicker
 * - Access dropdown
 * - Friends widgets
 * - Group invite form override
 * - Group invite action override
 * - Group tools admin transfer form override
 */

elgg_register_event_handler('init', 'system', 'no_friends_init');

// Init wall posts
function no_friends_init() {
	// Register hook handler for user hover menu
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'no_friends_menu_handler', 999);

	// Register hook handler for topbar menu
	elgg_register_plugin_hook_handler('register', 'menu:topbar', 'no_friends_menu_handler', 999);

	// Register hook handler for page (sidebar) menu
	elgg_register_plugin_hook_handler('register', 'menu:page', 'no_friends_menu_handler', 999);

	// Register hook handler for page (sidebar) menu
	elgg_register_plugin_hook_handler('register', 'menu:filter', 'no_friends_menu_handler', 999);

	// Register hook handler to remove friends from access dropdown
	elgg_register_plugin_hook_handler('access:collections:write', 'user', 'no_friends_write_access_handler');

	// Register hook handler for page handlers
	elgg_register_plugin_hook_handler('route', 'all', 'no_friends_route_handler');

	// Register hook handler for profile tabs
	elgg_register_plugin_hook_handler('tabs', 'profile', 'no_friends_tabbed_profile_handler', 501);

	// Unregister page handlers
	elgg_unregister_page_handler('friends');
	elgg_unregister_page_handler('friendsof');
	elgg_unregister_page_handler('collections');

	// Unregister friends actions
	elgg_unregister_action('friends/add');
	elgg_unregister_action('friends/remove');
	elgg_unregister_action('friends/collections/add');
	elgg_unregister_action('friends/collections/delete');
	elgg_unregister_action('friends/collections/edit');
	elgg_unregister_action('groups/invite');

	// Register actions
	$action_base = elgg_get_plugins_path() . 'nofriends/actions/groups/membership';
	elgg_register_action('groups/invite', "$action_base/invite.php");

	// Unregister friends widget
	elgg_unregister_widget_type("friends");

}

/**
 * Modify menus and remove friends items
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function no_friends_menu_handler($hook, $type, $value, $params) {
	// Friends item names
	$friends_items = array(
		'add_friend',                 // Add friend on user hover
		'remove_friend',              // Remove friend on user hover
		'friends',                    // Friends item on topbar
		'friends:view:collections',   // Friend collections on page menu
		'friends:of',                 // 'Friends of' on page menu,
		'friend'                      // Friend filter item
	);

	// Inspect each existing menu element
	foreach ($value as $idx => $item) {
		// Remove anything related to friends
		if (in_array($item->getName(), $friends_items)) {
			unset($value[$idx]);
		}
	}

	return $value;
}

/**
 * Modify write access list
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function no_friends_write_access_handler($hook, $type, $value, $params) {
	unset($value[ACCESS_FRIENDS]); // Remove friends
	return $value;
}

/**
 * Handle routing to friends content (forward elsewhere)
 *
 * @param string $hook
 * @param string $type
 * @param mixed  $value
 * @param array  $params
 * @return mixed
 */
function no_friends_route_handler($hook, $type, $value, $params) {
	if (is_array($value['segments']) && ($value['segments'][0] == 'friends' || $value['segments'][1] == 'friends')) {
		forward('', 404); // Bail out
	}
	return $value;
}

/**
 * Handler profile tabs
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function no_friends_tabbed_profile_handler($hook, $type, $value, $params) {
	// Inspect profile tabs
	foreach ($value as $idx => $item) {
		if ($item == 'friends') {
			unset($value[$idx]); // Remove friends
		}
	}

	return $value;
}