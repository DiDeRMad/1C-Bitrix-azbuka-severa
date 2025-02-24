<?
namespace WD\Utilities;

use
	\WD\Utilities\Helper,
	\WD\Utilities\Backup;

Helper::loadMessages();
$strLang = 'WDU_OPTION_';

return [
	'ITEMS' => [
		'backups_enabled' => [
			'TYPE' => 'checkbox',
		],
		'backups_interval' => [
			'TYPE' => 'select',
			'VALUES' => [
				'1h' => Helper::getMessage($strLang.'BACKUPS_INTERVAL_1H'),
				'6h' => Helper::getMessage($strLang.'BACKUPS_INTERVAL_6H'),
				'12h' => Helper::getMessage($strLang.'BACKUPS_INTERVAL_12H'),
				'1d' => Helper::getMessage($strLang.'BACKUPS_INTERVAL_1D'),
				'3d' => Helper::getMessage($strLang.'BACKUPS_INTERVAL_3D'),
				'7d' => Helper::getMessage($strLang.'BACKUPS_INTERVAL_7D'),
				'1m' => Helper::getMessage($strLang.'BACKUPS_INTERVAL_1M'),
				'CUSTOM' => Helper::getMessage($strLang.'BACKUPS_INTERVAL_CUSTOM'),
			],
			'CALLBACK_HEAD_DATA' => function($obOptions, $arOption, $strOption, $strOptionId){
				?>
				<style>
				input[data-role="wdu_backups_create_now"] {
					height:27px;
					margin:0;
				}
				</style>
				<script>
				$(document).delegate('#<?=$strOptionId;?>', 'change', function(e){
					$('#<?=$obOptions->getOptionPrefix('backups_interval_custom');?>').toggle($(this).val() == 'CUSTOM');
				});
				$(document).delegate('input[data-role="wdu_backups_create_now"]', 'click', function(e){
					let
						button = $(this);
					button.attr('disabled', 'disabled');
					wduAjax('<?=$strOption;?>', {}, {}, function(arJson){
						if(arJson.Success){
							alert(button.attr('data-success'));
						}
						button.removeAttr('disabled');
					}, function(error){
						alert('Error');
						button.removeAttr('disabled');
					}, false);
				});
				$(document).ready(function(){
					$('#<?=$strOptionId;?>').trigger('change');
				});
				</script>
				<?
			},
			'CALLBACK_MORE' => function($obOptions, $arOption, $strOption, $strOptionId)use($strLang){
				$strOptionCustom = $strOption.'_custom';
				$strCustomValue = Helper::getOption($obOptions->getModuleId(), $strOptionCustom);
				?>
				<input type="text" name="<?=$strOptionCustom;?>" value="<?=htmlspecialcharsbx($strCustomValue);?>" 
					id="<?=$obOptions->getOptionPrefix($strOptionCustom);?>" size="8" style="max-width:100px;" />
				<?=Helper::showHint(Helper::getMessage($strLang.'BACKUPS_INTERVAL_CUSTOM_HINT'));?>
				&nbsp;&nbsp;&nbsp;
				<input type="button" value="<?=Helper::getMessage($strLang.'BACKUPS_CREATE_NOW');?>" 
					data-success="<?=Helper::getMessage($strLang.'BACKUPS_CREATE_NOW_SUCCESS');?>"
					data-role="wdu_backups_create_now" />
				<?
			},
			'CALLBACK_AFTER_SAVE' => function($obOptions, $strValue, $arOption, $strOption, $strOptionId){
				$strOptionCustom = $strOption.'_custom';
				$strCustomValue = $obOptions->getPost($strOptionCustom);
				Helper::setOption($obOptions->getModuleId(), $strOptionCustom, $strCustomValue);
			},
			'CALLBACK_AJAX' => function(&$arJsonResult, $obOptions, $arOption, $strOption){
				Backup::execute();
				$arJsonResult['Success'] = true;
			},
		],
		'backups_files' => [
			'CALLBACK_HEAD_DATA' => function($obOptions, $arOption, $strOption, $strOptionId){
				$strRowId = $obOptions->getOptionRowId($strOption);
				?>
				<style>
				#<?=$strRowId;?> > td.adm-detail-content-cell-l {
					padding-top:14px;
					vertical-align:top;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r table[data-role="wdu_backup_table"]{
					width:100%;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r table[data-role="wdu_backup_table"] td{
					padding:1px;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r td[data-role="wdu_backup_col_select"]{
					width:1px;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r td[data-role="wdu_backup_col_delete"]{
					width:1px;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r input[data-role="wdu_backup_file_input"] {
					font-family:monospace;
					height:25px;
					width:100%;
					-webkit-box-sizing:border-box;
					   -moz-box-sizing:border-box;
					        box-sizing:border-box;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r input[data-role="wdu_backup_file_select"],
				#<?=$strRowId;?> > td.adm-detail-content-cell-r input[data-role="wdu_backup_file_delete"] {
					height:25px;
					margin:0;
					width:36px;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r input[data-role="wdu_backup_file_delete"] {
					color:maroon;
				}
				#<?=$strRowId;?> > td.adm-detail-content-cell-r input[data-role="wdu_backup_file_add"] {
					height:25px!important;
				}
				</style>
				<script>
				$(document).delegate('#<?=$strRowId;?> input[data-role="wdu_backup_file_add"]', 'click', function(e){
					var body = $(this).closest('table').children('tbody');
					var row = body.find('tr').first().clone().show();
					row.find('input[type=text]').val('');
					body.append(row);
					row.find('input[type="text"]').focus();
				});
				$(document).delegate('#<?=$strRowId;?> input[data-role="wdu_backup_file_select"]', 'click', function(e){
					$(this).closest('tr').attr('data-current', 'Y').siblings().removeAttr('data-current');
					wduImageOptimizerSelectExcludeSelect();
				});
				$(document).delegate('#<?=$strRowId;?> input[data-role="wdu_backup_file_delete"]', 'click', function(e){
					let
						input = $(this).closest('tr').find('input[type="text"]');
					if(!$.trim(input.val()).length || confirm('Delete ' + input.val() + '?')){
						var row = $(this).closest('tr');
						var body = $(this).closest('tbody');
						row.remove();
					}
				});
				function wduBackupFileSelectHandler(filename, path, site){
					$('table[data-role="wdu_backup_table"] tr[data-current="Y"]').removeAttr('data-current')
						.find('input[data-role="wdu_backup_file_input"]').val(path + (filename.length ? '/' + filename : ''));
				}
				</script>
				<?
			},
			'CALLBACK_MAIN' => function($obOptions, $arOption, $strOption, $strOptionId)use($strLang){
				# Load values
				$arFiles = Backup::getBackupFilesRaw();
				$arFiles = array_merge([false], $arFiles);
				# File dialog
				\CAdminFileDialog::showScript([
					'event' => 'wduImageOptimizerSelectExcludeSelect',
					'arResultDest' => ['FUNCTION_NAME' => 'wduBackupFileSelectHandler'],
					'arPath' => ['PATH' => '/'],
					'select' => 'F,D',
					'operation' => 'O',
					'showUploadTab' => false,
					'showAddToMenuTab' => false,
					'allowAllFiles' => true,
					'SaveConfig' => true
				]);
				?>
				<table data-role="wdu_backup_table">
					<tbody>
						<?foreach($arFiles as $strFile):?>
							<?$bHidden = $strFile === false;?>
							<?if(strlen($strFile) || $bHidden):?>
								<tr<?if($bHidden):?> style="display:none;"<?endif?>>
									<td data-role="wdu_backup_col_filename">
										<input type="text" name="<?=$strOption;?>[]" value="<?=htmlspecialcharsbx($strFile);?>" size="50" 
											data-role="wdu_backup_file_input" spellcheck="false"
											placeholder="<?=Helper::getMessage($strLang.'BACKUPS_FILE_ADD_PLACEHOLDER');?>" />
									</td>
									<td data-role="wdu_backup_col_select">
										<input type="button" value="..." 
											title="<?=Helper::getMessage($strLang.'BACKUPS_FILE_BUTTON_SELECT');?>" 
											data-role="wdu_backup_file_select" />
									</td>
									<td data-role="wdu_backup_col_delete">
										<input type="button" value="&times;" 
											title="<?=Helper::getMessage($strLang.'BACKUPS_FILE_BUTTON_DELETE');?>" 
											data-role="wdu_backup_file_delete" />
									</td>
								</tr>
							<?endif?>
						<?endforeach?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="2">
								<input type="button" value="<?=Helper::getMessage($strLang.'BACKUPS_FILE_BUTTON_ADD');?>"
									data-role="wdu_backup_file_add" />
							</td>
						</tr>
					</tfoot>
				</table>
				<?
			},
			'CALLBACK_BEFORE_SAVE' => function($obOptions, &$strValue, $arOption, $strOption, $strOptionId){
				$strValue = implode("\n", $arOption['ORIGINAL_VALUE']);
			},
		],
		'backups_additional' => [
			'TOP' => 'Y',
			'CALLBACK_MAIN' => function($obOptions, $arOption, $strOption, $strOptionId)use($strLang){
				$arOptions = Backup::getAdditionals();
				?>
				<div style="padding-top:4px;">
					<?foreach($arOptions as $strItem => $arItem):?>
						<div style="margin-bottom:2px;">
							<label>
								<input type="checkbox" name="<?=$strOption;?>[]" value="<?=$strItem;?>"
									<?if($arItem['ON']):?> checked="checked"<?endif?> />
								<?=$arItem['NAME'];?>
							</label>
						</div>
					<?endforeach?>
				</div>
				<?
			},
			'CALLBACK_BEFORE_SAVE' => function($obOptions, &$strValue, $arOption, $strOption, $strOptionId){
				$strValue = implode("\n", $arOption['ORIGINAL_VALUE']);
			},
		],
		'backups_count' => [
			'TYPE' => 'text',
			'ATTR' => 'size="8" style="max-width:60px;"',
		],
		'backups_send_to_email' => [
			'TYPE' => 'checkbox',
			'CALLBACK_HEAD_DATA' => function($obOptions, $arOption, $strOption, $strOptionId){
				?>
				<script>
				$(document).delegate('#<?=$strOptionId;?>', 'change', function(e){
					$('#<?=$obOptions->getOptionPrefix('backups_email');?>').closest('tr')
						.toggle($(this).prop('checked'));
				});
				$(document).ready(function(){
					$('#<?=$strOptionId;?>').trigger('change');
				});
				</script>
				<?
			},
		],
		'backups_email' => [
			'TYPE' => 'text',
			'ATTR' => 'size="50"',
		],
	],
	'CALLBACK_AFTER_SAVE' => function($obOptions, $arTab, $strTab){
		$arImrortantOptions = ['backups_enabled', 'backups_interval'];
		if($obOptions->getOldValues($arImrortantOptions) != $obOptions->getNewValues($arImrortantOptions)){
			Backup::addAgent();
		}
	},
];
?>