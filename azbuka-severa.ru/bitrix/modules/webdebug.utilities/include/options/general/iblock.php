<?
namespace WD\Utilities;

use
	\WD\Utilities\Helper,
	\WD\Utilities\Options;

Helper::loadMessages();

return [
	'ITEMS' => [
		'iblock_add_detail_link' => [
			'TYPE' => 'select',
			'VALUES' => [
				'N' => Options::getMessage('IBLOCK_ADD_DETAIL_LINK_NO'),
				'Y' => Options::getMessage('IBLOCK_ADD_DETAIL_LINK_SUBMENU'),
				'S' => Options::getMessage('IBLOCK_ADD_DETAIL_LINK_SEPARATE'),
			],
		],
		'iblock_show_element_id' => [],
		'iblock_just_this_site' => [],
		'iblock_hide_empty_types' => [],
		'iblock_show_iblock_prop_meta' => [],
		//
		'iblock_hide_section_tab_properties' => [
			'TYPE' => 'select',
			'VALUES' => [
				'N' => Options::getMessage('IBLOCK_HIDE_SECTION_TAB_PROPERTIES_NO'),
				'P' => Options::getMessage('IBLOCK_HIDE_SECTION_TAB_PROPERTIES_PUBLIC'),
				'A' => Options::getMessage('IBLOCK_HIDE_SECTION_TAB_PROPERTIES_ADMIN'),
				'Y' => Options::getMessage('IBLOCK_HIDE_SECTION_TAB_PROPERTIES_YES'),
			],
			'USER' => true,
		],
		'iblock_hide_section_tab_seo' => [
			'TYPE' => 'select',
			'VALUES' => [
				'N' => Options::getMessage('IBLOCK_HIDE_SECTION_TAB_SEO_NO'),
				'P' => Options::getMessage('IBLOCK_HIDE_SECTION_TAB_SEO_PUBLIC'),
				'A' => Options::getMessage('IBLOCK_HIDE_SECTION_TAB_SEO_ADMIN'),
				'Y' => Options::getMessage('IBLOCK_HIDE_SECTION_TAB_SEO_YES'),
			],
			'USER' => true,
		],
		'iblock_hide_element_tab_seo' => [
			'TYPE' => 'select',
			'VALUES' => [
				'N' => Options::getMessage('IBLOCK_HIDE_ELEMENT_TAB_SEO_NO'),
				'P' => Options::getMessage('IBLOCK_HIDE_ELEMENT_TAB_SEO_PUBLIC'),
				'A' => Options::getMessage('IBLOCK_HIDE_ELEMENT_TAB_SEO_ADMIN'),
				'Y' => Options::getMessage('IBLOCK_HIDE_ELEMENT_TAB_SEO_YES'),
			],
			'USER' => true,
		],
		'iblock_hide_element_tab_adv' => ['USER' => true],
		'iblock_hide_element_tab_wf' => ['USER' => true],
		'iblock_hide_element_tab_bp' => ['USER' => true],
	],
];
?>