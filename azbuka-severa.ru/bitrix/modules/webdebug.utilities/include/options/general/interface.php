<?
namespace WD\Utilities;

use
	\WD\Utilities\Helper,
	\WD\Utilities\Options,
	\WD\Utilities\MenuManager;

Helper::loadMessages();

return [
	'ITEMS' => [
		'use_select2_for_modules' => [],
		'use_select2_for_custom_admin_pages' => [
			'CALLBACK_HEAD_DATA' => function($obOptions, $arOption, $strOption, $strOptionId){
				?>
				<script>
				$(document).delegate('#<?=$strOptionId;?>', 'change', function(e){
					$('#webdebug_utilities_row_custom_pages_for_select2').toggle($(this).prop('checked'));
				});
				$(document).ready(function(){
					$('#<?=$strOptionId;?>').trigger('change');
				});
				</script>
				<?
			},
		],
		'custom_pages_for_select2' => [
			'CALLBACK_HEAD_DATA' => function($obOptions, $arOption, $strOption, $strOptionId){
				$strRowId = $obOptions->getOptionRowId($strOption);
				?>
				<style>
				#<?=$strRowId;?> > td.adm-detail-content-cell-l {
					padding-top:14px;
					vertical-align:top;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r table td {
					vertical-align:top;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r input[data-role="wdu_select2_add"] {
					height:25px!important;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r textarea {
					box-sizing:border-box;
					height:27px;
					max-height:200px;
					min-height:27px;
					resize:vertical;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r tr input[data-role="wdu_select2_delete"] {
					margin:0 2px;
					height:25px;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r tr:first-child input[data-role="wdu_select2_delete"] {
					visibility:hidden;
				}
				</style>
				<script>
				$(document).delegate('#<?=$strRowId;?> input[data-role="wdu_select2_add"]', 'click', function(e){
					var body = $(this).closest('table').children('tbody');
					var row = body.find('tr').first().clone();
					row.find('input[type=text],textarea').val('');
					body.append(row);
				});
				$(document).delegate('#<?=$strRowId;?> input[data-role="wdu_select2_delete"]', 'click', function(e){
					var row = $(this).closest('tr');
					var body = $(this).closest('tbody');
					if(body.children('tr').length > 1){
						row.remove();
					}
				});
				</script>
				<?
			},
			'CALLBACK_MAIN' => function($obOptions, $arOption, $strOption, $strOptionId){
				$arItems = Helper::getOption($obOptions->getModuleId(), $strOption);
				if(strlen($arItems)){
					$arItems = unserialize($arItems);
				}
				if(!is_array($arItems)){
					$arItems = [];
				}
				$arItemsTmp = [];
				$strKeysAll = array_keys($arItems);
				foreach($arItems[$strKeysAll[0]] as $key => $arValues){
					$arItem = [];
					foreach($strKeysAll as $key2){
						$arItem[$key2] = $arItems[$key2][$key];
					}
					$arItemsTmp[] = $arItem;
				}
				$arItems = $arItemsTmp;
				if(empty($arItems)){
					$arItems = [
						['url' => '', 'separator' => '']
					];
				}
				?>
				<table class="table_<?=$strOption;?>">
					<tbody>
						<?foreach($arItems as $arItem):?>
							<?if(is_array($arItem)):?>
								<tr>
									<td>
										<input type="text" name="<?=$strOption;?>[url][]" value="<?=htmlspecialcharsbx($arItem['url']);?>" size="35" 
											placeholder="<?=Options::getMessage('SELECT2_URL_PLACEHOLDER');?>" />
									</td>
									<td>
										<textarea name="<?=$strOption;?>[selector][]" cols="40" rows="2"
											placeholder="<?=Options::getMessage('SELECT2_SELECTOR_PLACEHOLDER');?>"
											><?=htmlspecialcharsbx($arItem['selector']);?></textarea>
									</td>
									<td>
										<input type="button" value="&times;" 
											title="<?=Options::getMessage('SELECT2_BUTTON_DELETE');?>" 
											data-role="wdu_select2_delete" />
									</td>
								</tr>
							<?endif?>
						<?endforeach?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="2">
								<input type="button" value="<?=Options::getMessage('SELECT2_BUTTON_ADD');?>"
									data-role="wdu_select2_add" />
							</td>
						</tr>
					</tfoot>
				</table>
				<?
			},
			'CALLBACK_BEFORE_SAVE' => function($obOptions, &$strValue, $arOption, $strOption, $strOptionId){
				$strValue = serialize($arOption['ORIGINAL_VALUE']);
			},
		],
		'set_admin_favicon' => [
			'CALLBACK_HEAD_DATA' => function($obOptions, $arOption, $strOption, $strOptionId){
				?>
				<script>
				$(document).delegate('#<?=$strOptionId;?>', 'change', function(e){
					$('#<?=$obOptions->getOptionPrefix('admin_favicon');?>').closest('tr').toggle($(this).prop('checked'));
				});
				$(document).ready(function(){
					$('#<?=$strOptionId;?>').trigger('change');
				});
				</script>
				<?
			},
		],
		'admin_favicon' => [
			'TYPE' => 'text',
			'ATTR' => 'style="width:80%;"',
			'CALLBACK_MORE' => function($obOptions, $arOption, $strOption, $strOptionId){
				$strFunctionName = sprintf('WDU_%s_SelectFavicon', $strOption);
				$strEventName = sprintf('WDU_%s_OnSelectFavicon', $strOption);
				?>
					<script>
					function <?=$strEventName;?>(filename, path, site){
						if(path.length > 1) {
							path += '/';
						}
						$('#<?=$strOptionId;?>').val(path + filename);
					}
					</script>
					<input type="button" value="..." onclick="<?=$strFunctionName;?>();" />
				<?
				$arDialogParams = [
					'event' => $strFunctionName,
					'arResultDest' => ['FUNCTION_NAME' => $strEventName],
					'arPath' => [],
					'select' => 'F',
					'operation' => 'O',
					'showUploadTab' => true,
					'showAddToMenuTab' => false,
					'fileFilter' => 'ico, gif, png',
					'allowAllFiles' => true,
					'saveConfig' => true,
				];
				\CAdminFileDialog::ShowScript($arDialogParams);
			},
		],
		'hide_partners_menu' => [
			'CALLBACK_HEAD_DATA' => function($obOptions, $arOption, $strOption, $strOptionId){
				?>
				<script>
				$(document).delegate('#<?=$strOptionId;?>', 'change', function(e){
					var rowFavicon = $('#<?=$obOptions->getOptionPrefix('hide_partners_menu_exclude');?>').closest('tr');
					rowFavicon.toggle($(this).prop('checked'));
				});
				$(document).ready(function(){
					$('#<?=$strOptionId;?>').trigger('change');
				});
				</script>
				<?
			},
		],
		'hide_partners_menu_exclude' => [
			'TYPE' => 'text',
			'ATTR' => 'style="width:80%;"',
			'CALLBACK_HEAD_DATA' => function($obOptions, $arOption, $strOption, $strOptionId){
				$strRowId = $obOptions->getOptionRowId($strOption);
				?>
				<script>
				let wduPopupSelectHiddenMenu;
				wduPopupSelectHiddenMenu = new WduPopup({
					height: 360,
					width: 500
				});
				wduPopupSelectHiddenMenu.Open = function(value){
					let popup = this;
					popup.WdSetContent('<?=Options::getMessage('HIDE_PARTNERS_MENU_EXCLUDE_LOADING');?>');
					popup.WdLoadContentAjax('<?=$strOption;?>', false, {current_value: value});
					popup.WdSetNavButtons([{
						'name': '<?=Options::getMessage('HIDE_PARTNERS_MENU_EXCLUDE_SAVE');?>',
						'id': 'wdu_save',
						'className': 'adm-btn-green',
						'action': function(){
							let text = $.map($('div[data-role="wdu_form_hide_partners_menu_exclude"] input[type="checkbox"]:checked')
								.get(), function(item){return $(item).val()}).join(', ');
							$('#<?=$strOptionId;?>').val(text);
							popup.Close();
						}
					}]);
					popup.Show();
				}
				$(document).delegate('#<?=$strRowId;?> input[data-role="wdu_hidden_menu_select"]', 'click', function(e){
					wduPopupSelectHiddenMenu.Open($('#<?=$strOptionId;?>').val());
				});
				</script>
				<?
			},
			'CALLBACK_MORE' => function($obOptions, $arOption, $strOption, $strOptionId){
				?>
				<input type="button" value="..." onclick="" data-role="wdu_hidden_menu_select" />
				<?
			},
			'CALLBACK_AJAX' => function(&$arJsonResult, $obOptions, $arOption, $strOption){
				global $adminPage, $adminMenu;
				\WD\Utilities\MenuManager::stopHandler();
				$adminPage->Init();
				$adminMenu->Init($adminPage->aModules);
				\WD\Utilities\MenuManager::stopHandler();
				$arMenuAll = [];
				foreach($adminMenu->aGlobalMenu as $arMenuItem){
					$arMenuAll[$arMenuItem['menu_id']] = $arMenuItem['text'];
				}
				// unset($arMenuAll['settings']);
				$arMenuHidden = Helper::splitCommaValues($this->getPost('current_value'));
				$arJsonResult['Title'] = Options::getMessage('HIDE_PARTNERS_MENU_EXCLUDE_POPUP_TITLE');
				ob_start();
				?>
				<style>
				div[data-role="wdu_form_hide_partners_menu_exclude"] ul {list-style:none; margin:0; padding:0;}
				div[data-role="wdu_form_hide_partners_menu_exclude"] ul li {margin-bottom:4px;}
				</style>
				<div data-role="wdu_form_hide_partners_menu_exclude">
					<ul>
						<?foreach($arMenuAll as $strMenuCode => $strMenuName):?>
							<li>
								<label>
									<input type="checkbox" name="menu[]" value="<?=$strMenuCode;?>"
										<?if(in_array($strMenuCode, $arMenuHidden)):?> checked="checked"<?endif?> />
									<span><?=$strMenuName;?></span>
								</label>
							</li>
						<?endforeach?>
					</ul>
				</div>
				<script>
					let checkboxes = document.querySelectorAll(
						'div[data-role="wdu_form_hide_partners_menu_exclude"] input[type="checkbox"]');
					for(let i=0; i<checkboxes.length; i++){
						BX.adminFormTools.modifyCheckbox(checkboxes[i]);
					}
				</script>
				<?
				$arJsonResult['Content'] = ob_get_clean();
			},
		],
		'popup_expand_on_top_edge' => [],
		'include_accessibility_js' => ['USER' => true],
	],
];
?>