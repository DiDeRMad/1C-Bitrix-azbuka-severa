<?
$webdebug_utilities_default_option = [

	# General
	'pageprops_enabled' => 'Y',
	'prevent_logout' => 'N',
	
	#
	'use_select2_for_modules' => 'Y',
	'set_admin_favicon' => 'N',
		'admin_favicon' => '',
	
	#
	'admin_auth_notify' => 'N',
	'admin_auth_email' => \Bitrix\Main\Config\Option::get('main', 'email_from'),
	'admin_auth_http_request' => '',
	
	#
	'hide_partners_menu' => 'N',
		'hide_partners_menu_exclude' => 'desktop, content, marketing, store, services, marketPlace',
	'popup_expand_on_top_edge' => 'Y',

	#
	'iblock_add_detail_link' => 'Y',
	'iblock_show_element_id' => 'Y',
	'iblock_just_this_site' => 'N',
	'iblock_hide_empty_types' => 'N',
	'iblock_show_iblock_prop_meta' => 'N',
	
	# Developers
	'global_main_functions' => 'N',
	'js_debug_functions' => 'N',
	'editor_show_template_path' => 'Y',
	'php_no_confirm' => 'N',
	'sql_no_confirm' => 'N',
	
	#
	'fastsql_enabled' => 'N',
		'fastsql_auto_exec' => 'Y',
	
	# Search
	'finder_step_time' => 5,
	'finder_max_filesize' => 1 * 1024 * 1024,
	'finder_max_results' => 100,
	
	# Backup
	'backups_enabled' => 'N',
	'backups_interval' => '1d', // Every day
	'backups_interval_custom' => '',
	'backups_files' => implode("\n", [
		# Bitrix
		'/bitrix/.settings.php',
		'/bitrix/.settings_extra.php',
		'/bitrix/license_key.php',
		'/bitrix/.access.php',
		'/bitrix/crontab/crontab.cfg',
		# Bitrix php_interface
		'/bitrix/php_interface/admin_header.php',
		'/bitrix/php_interface/after_connect.php',
		'/bitrix/php_interface/after_connect_d7.php',
		'/bitrix/php_interface/dbconn.php',
		'/bitrix/php_interface/init.php',
		'/bitrix/php_interface/this_site_support.php',
		'/bitrix/php_interface/user_lang/*/lang.php',
		'/bitrix/php_interface/*/init.php',
		'/bitrix/php_interface/*/site_closed.php',
		# Local php_interface
		'/local/php_interface/admin_header.php',
		'/local/php_interface/init.php',
		'/local/php_interface/this_site_support.php',
		'/local/php_interface/user_lang/*/lang.php',
		'/local/php_interface/*/init.php',
		'/local/php_interface/*/site_closed.php',
		# Templates in /bitrix/templates (just main files)
		'/bitrix/templates/*/header.php',
		'/bitrix/templates/*/footer.php',
		'/bitrix/templates/*/template_styles.css',
		'/bitrix/templates/*/styles.css',
		'/bitrix/templates/*/.styles.php',
		'/bitrix/templates/*/description.php',
		# Templates in /local/templates (just main files)
		'/local/templates/*/header.php',
		'/local/templates/*/footer.php',
		'/local/templates/*/template_styles.css',
		'/local/templates/*/styles.css',
		'/local/templates/*/.styles.php',
		'/local/templates/*/description.php',
		# Public 
		'/.access.php',
		'/.htaccess',
		'/.htsecure',
		'/.section.php',
		'/404.php',
		'/index.php',
		'/favicon.ico',
		'/catalog/index.php',
		'/robots.txt',
		'/robots.php',
		'/urlrewrite.php',
		'/*menu*.php',
		# Guess, popular files
		'/contacts/index.php',
		'/about/contacts/index.php',
		'/include',
		'/index_*.php',
		'/sect_*.php',
	]),
	'backups_additional' => implode("\n", [
		'Options',
		'OptionsSite',
		'Crontab',
		'BackupPassword',
		'Modules',
		'Handlers',
		'Agents',
		'Tables',
		'Sites',
		'Templates',
		'Events',
		'Undo',
		'Stickers',
		'Admins',
		'System',
	]),
	'backups_count' => '30d',
	'backups_send_to_email' => 'Y',
	'backups_email' => '',
	
];
