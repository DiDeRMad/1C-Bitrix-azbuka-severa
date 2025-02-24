<?
use
	\WD\Utilities\Adv,
	\WD\Utilities\Helper,
	\WD\Utilities\IBlockHelper,
	\WD\Utilities\PropSorterTable as PropSorter;

$strModuleId = $ModuleID = 'webdebug.utilities';
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
if (!CModule::IncludeModule($ModuleID)) {
	die('Module is not found!');
}
IncludeModuleLangFile(__FILE__);

$ModuleRights = $APPLICATION->GetGroupRight($ModuleID);
if($ModuleRights=="D") {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$intIBlockId = IntVal($_GET['IBLOCK_ID']);
$arIBlockList = Helper::getIBlocks($bGroup=true, $bInactive=true);

if($_REQUEST['save_to_iblock'] == 'Y'){
	header('Content-Type: application/json; charset='.(defined('BX_UTF') && BX_UTF === true ? 'UTF-8' : 'windows-1251'));
	$arJsonResult = array(
		'Success' => false,
	);
	if(is_array($_POST['prop_id'])){
		$intIndex = 1;
		$intStep = 10;
		$obProperty = new \CIBlockProperty();
		foreach($_POST['prop_id'] as $strPropId){
			$obProperty->update($strPropId, ['SORT' => $intIndex * $intStep]);
			$intIndex++;
		}
		unset($obProperty);
		$arJsonResult['Success'] = true;
	}
	print \Bitrix\Main\Web\Json::encode($arJsonResult);
	die();
}

// Save data
$bSaving = isset($_POST['save']);
if($bSaving) {
	$arSaveProps = $_POST['prop_id'];
	// Delete exist items
	$arQuery = [
		'order' => [
			'SORT' => 'ASC',
		],
		'filter' => [
			'IBLOCK_ID' => $intIBlockId,
		],
	];
	$resItems = PropSorter::getList($arQuery);
	while($arItem = $resItems->fetch()){
		PropSorter::delete($arItem['ID']);
	}
	// Save new
	if (is_array($arSaveProps) && !empty($arSaveProps)) {
		$intSort = 0;
		foreach($arSaveProps as $strPropKey => $strPropValue) {
			if(preg_match('#^header_active_(.*?)$#', $strPropKey)){
				continue;
			}
			$arFields = [
				'IBLOCK_ID' => $intIBlockId,
				'PROPERTY_ID' => null,
				'GROUP_TITLE' => null,
				'SORT' => ++$intSort,
			];
			if(preg_match('#^prop_\d+$#i', $strPropKey, $arMatch)){
				$arFields['PROPERTY_ID'] = $strPropValue;
			}
			elseif(preg_match('#^header_(\d+)$#i', $strPropKey, $arMatch)){
				$strId = $arMatch[1];
				$arFields['GROUP_TITLE'] = $strPropValue;
				$arFields['GROUP_ACTIVE'] = $arSaveProps['header_active_'.$strId] == 'N' ? 'N' : 'Y';
			}
			PropSorter::add($arFields);
		}
		LocalRedirect($APPLICATION->GetCurPageParam('IBLOCK_ID='.$intIBlockId.'&lang='.LANGUAGE_ID, array('IBLOCK_ID', 'lang')));
	}
}

$APPLICATION->SetTitle(GetMessage('WD_PROPSORTER_PAGE_TITLE'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$arTabs[] = array("DIV"=>"general", "TAB"=>GetMessage("WD_PROPSORTER_TAB_GENERAL_NAME"), "TITLE"=>GetMessage("WD_PROPSORTER_TAB_GENERAL_DESC"));
\CJSCore::Init(['jquery', 'jquery2', 'jquery3', 'wdupopup']);
$APPLICATION->AddHeadScript('/bitrix/js/'.$strModuleId.'/jquery.ui.sortable.js');
$APPLICATION->AddHeadScript('/bitrix/js/'.$strModuleId.'/wdu_popup.js');
$APPLICATION->AddHeadScript('/bitrix/js/'.$strModuleId.'/helper.js');

# Advertising
Adv::showAdv();

// Load saved props
$arProps = PropSorter::loadIBlockData($intIBlockId);
?>

<style>
.wd_iblock_data {
	-moz-user-select:none;
	-webkit-user-select:none;
	-ms-user-select:none;
	user-select:none;
}
.wd_prop_item {
	padding:1px;
}
.wd_prop_item_outer.ui-sortable-placeholder {
	position:relative;
	visibility:visible!important;
}
.wd_prop_item_outer.ui-sortable-placeholder:before {
	content:'';
	border:2px dashed gray;
	height:100%;
	left:0;
	position:absolute;
	top:0;
	width:100%;
	-webkit-box-sizing:border-box;
	   -moz-box-sizing:border-box;
	        box-sizing:border-box;
}
.wd_prop_item_inner {
	background:#e5edef;
	border:1px solid #aab5b9;
	cursor:default;
	height:24px;
	line-height:24px;
	padding:0 5px 0 30px;
	position:relative;
	border-radius:2px;
}
.wd_prop_item_inner:hover {
	background-color:#ccd7db;
}
.wd_prop_item_inner:before {
	content:'';
	background:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAgklEQVR42mNgwA24gHg7FHMxkAhAGvYC8X8o3kuqITuA+A0QX4TiN1AxokEvEOsB8Roo1oOKkQxgBhDl5x1YbMFmQC9ULSd6gL2BOpWQAXpQtXtghmyHhvRFJA0w/ASK0cUvQvVsodSA7ZR4ASVtkBOIXFSNRqonJIqTMsWZiejsDABbQ0bDhprMrwAAAABJRU5ErkJggg==') 0 0 no-repeat;
	height:16px;
	left:4px;
	margin-top:-8px;
	position:absolute;
	top:50%;
	width:16px;
}
.wd_prop_item_group .wd_prop_item_inner {
	background:#afd2e0;
	height:36px;
	line-height:36px;
	padding-right:100px;
}
.wd_prop_item_group .wd_prop_item_inner_active {
	position:absolute;
	height:16px;
	line-height:16px;
	margin-top:-8px;
	right:40px;
	top:50%;
	width:50px;
}
.wd_prop_item_inner > label, .wd_prop_item_inner > span {
	vertical-align:middle;
}
.wd_prop_item_group .wd_prop_item_inner:hover {
	background:#afd2e0;
}
.wd_prop_item input[type=text] {
	-webkit-box-sizing:border-box;
	   -moz-box-sizing:border-box;
	        box-sizing:border-box;
	width:100%;
}
.wd_prop_item input[type=button]{
	font-size:15px;
	height:23px!important;
	margin-top:-11px;
	padding:0!important;
	position:absolute;
	right:4px;
	top:50%;
	width:24px;
}
.wd_group_move_wrapper {
	height:100%;
	overflow:auto;
}
.wd_group_move_wrapper .wd_group_item {
	background:#efefef;
	border:1px solid #b1b1b1;
	border-radius:3px;
}
.wd_group_move_wrapper .wd_group_item + .wd_group_item {
	margin-top:10px;
}
.wd_group_move_wrapper .wd_group_item input[type="radio"] {
	display:none;
}
.wd_group_move_wrapper .wd_group_item .wd_group_name {
	border-radius:3px;
	cursor:pointer;
	display:block;
	padding:10px;
}
.wd_group_move_wrapper .wd_group_item .wd_group_name:hover {
	background:#ddd;
}
.wd_group_move_wrapper .wd_group_item .wd_group_name span {
	display:block;
	font-weight:bold;
}
.wd_group_move_wrapper .wd_group_item .wd_group_place {
	display:none;
	padding:0 10px 10px;
}
.wd_group_move_wrapper .wd_group_item .wd_group_place > div:first-child {
	padding:3px 0 6px;
}
.wd_group_move_wrapper input[type="radio"]:checked + .wd_group_name + .wd_group_place {
	display:block;
}
.wd_prop_item_outer,
.wd_prop_item_inner {
	transition:1s background;
}
.wd_prop_item_outer.wd_prop_item_outer_animate,
.wd_prop_item_outer.wd_prop_item_outer_animate .wd_prop_item_inner {
	background:green;
	transition:none;
}
</style>

<form method="post" action="<?=POST_FORM_ACTION_URI;?>" name="post_form" id="wd_propsorter_form">
	<?$TabControl = new CAdminTabControl("WDPropSorter", $arTabs);?>
	<?$TabControl->Begin();?>
	<?$TabControl->BeginNextTab();?>
	<tr>
		<td>
			<div id="wd_propsorter_iblock_list">
				<select name="iblock_id">
					<option value=""><?=GetMessage('WD_PROPSORTER_SELECT_IBLOCK');?></option>
					<?foreach($arIBlockList as $IBlockTypeKey => $arIBlockType):?>
						<?
						if(empty($arIBlockType['ITEMS'])){
							continue;
						}
						?>
						<optgroup label="<?=$arIBlockType['NAME'];?>">
							<?foreach($arIBlockType['ITEMS'] as $arIBlock):?>
								<option value="<?=$arIBlock['ID'];?>"<?if($intIBlockId==$arIBlock['ID']):?> selected="selected"<?endif?>>[<?=$arIBlock['ID'];?>] <?=$arIBlock['NAME'];?></option>
							<?endforeach?>
						</optgroup>
					<?endforeach?>
				</select>
			</div>
			<br/>
			<hr/>
			<br/>
			<div id="wd_iblock_data_wrapper">
				<?if($intIBlockId > 0):?>
					<div>
						<input type="text" value="" id="wd_propsorter_add_value" placeholder="<?=GetMessage('WD_PROPSORTER_ADD_GROUP_PLACEHOLDER');?>" size="50" maxlength="255" />
						<input type="button" value="<?=GetMessage('WD_PROPSORTER_ADD_GROUP_BUTTON');?>" id="wd_propsorter_add_button" />
					</div>
					<br/>
					<div class="wd_iblock_data" id="wd_iblock<?=$intIBlockId;?>_data">
						<div class="wd_prop_items">
							<?foreach($arProps as $arProperty):?>
								<?
								$bHeader = !$arProperty['PROPERTY_ID'];
								$arProp = &$arProperty['PROPERTY'];
								if(!$bHeader && !is_array($arProp)){
									continue;
								}
								?>
								<div class="wd_prop_item_outer">
									<div class="wd_prop_item<?if($bHeader):?> wd_prop_item_group<?endif?>" <?if($bHeader):?>data-group-id="<?=randString(16);?>"<?else:?>data-prop-id="<?=$arProperty['ID'];?>"<?endif?>>
										<div class="wd_prop_item_inner">
											<?if($bHeader):?>
												<?$strId = rand(100000000, 999999999);?>
												<input class="wd_prop_item_header_name" type="text" name="prop_id[header_<?=$strId;?>]" value="<?=htmlspecialcharsbx($arProperty['GROUP_TITLE']);?>" size="50" maxlength="255" placeholder="<?=getMessage('WD_PROPSORTER_HEADER_PLACEHOLDER');?>" />
												<span class="wd_prop_item_inner_active">
													<label>
														<input type="hidden" name="prop_id[header_active_<?=$strId;?>]" value="N" />
														<input type="checkbox" name="prop_id[header_active_<?=$strId;?>]" value="Y"<?if($arProperty['GROUP_ACTIVE'] != 'N'):?> checked="checked"<?endif?> title="<?=getMessage('WD_PROPSORTER_HEADER_TITLE_ACTIVE');?>" />
														<span title="<?=getMessage('WD_PROPSORTER_HEADER_TITLE_ACTIVE');?>"><?=getMessage('WD_PROPSORTER_HEADER_ACTIVE');?></span>
													</label>
												</span>
												<input type="button" value="&times;" title="<?=getMessage('WD_PROPSORTER_HEADER_DELETE');?>" />
											<?else:?>
												<label>
													<input type="checkbox" class="wd_prop_item_select_checkbox" id="wd_prop_<?=$arProp['ID'];?>" data-prop-id="<?=$arProp['ID'];?>" title="<?=getMessage('WD_PROPSORTER_CHECKBOX_SELECT');?>" />
												</label>
												<label class="wd_prop_item_property_text" for="wd_prop_<?=$arProp['ID'];?>">
													<?=$arProp['NAME'];?> [<?=$arProp['ID'];?>, <?=$arProp['CODE'];?>, <?=$arProp['PROPERTY_TYPE'];?><?if(strlen($arProp['USER_TYPE'])):?>:<?=$arProp['USER_TYPE'];?><?endif?>]
												</label>
												<input type="hidden" name="prop_id[prop_<?=$arProp['ID'];?>]" value="<?=$arProp['ID'];?>" />
											<?endif?>
										</div>
									</div>
								</div>
							<?endforeach?>
						</div>
					</div>
				<?else:?>
					<div id="wd_iblock_data_no"></div>
				<?endif?>
			</div>
		</td>
	</tr>
	<?$TabControl->Buttons();?>
		<input type="submit" name="save" value="<?=getMessage('WD_PROPSORTER_BUTTON_SAVE');?>" class="adm-btn-save">
		<input type="button" value="<?=getMessage('WD_PROPSORTER_BUTTON_SAVE_TO_IBLOCK');?>" class="adm-btn-save-to-iblock" 
			style="float:right;">
		<input type="button" value="<?=getMessage('WD_PROPSORTER_BUTTON_MOVE');?>" class="adm-btn-move-selected" 
			style="display:none; float:right; margin-right:20px;" data-title="<?=getMessage('WD_PROPSORTER_BUTTON_MOVE');?>">
	<?$TabControl->End();?>
</form>

<script>
// Sortable
var WD_SortableHelper = function(Event, TR) {
	var $originals = TR.children();
	var $helper = TR.clone();
	$helper.children().each(function(index) {
		$(this).width($originals.eq(index).width());
	});
	return $helper;
};
var WD_SortableOnStop = function(Event, UI) {
	$('td.index', UI.item.parent()).each(function (i) {
		$(this).html(i + 1);
	});
};
var WD_SortableEscapeHtml = function(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, function(m) {
		return map[m];
	});
}
// Change current iblock handler
$('#wd_propsorter_iblock_list select').change(function(){
	location.href = '/bitrix/admin/wdu_propsorter.php?IBLOCK_ID='+$(this).val()+'&lang='+phpVars.LANGUAGE_ID;
});
$('#wd_propsorter_add_button').click(function(e){
	e.preventDefault();
	var newGroup = $('#wd_propsorter_add_value').val().trim();
	if(newGroup.length > 0){
		var id = Math.round(Math.random() * 100000000);
		var newGroupHtml = $('<div><div class="wd_prop_item_outer"><div class="wd_prop_item wd_prop_item_group" data-group-id="'+Math.random()+'">'
			+'<div class="wd_prop_item_inner">'
			+'<input type="text" name="prop_id[header_'+id+']" value="'+WD_SortableEscapeHtml(newGroup)+'" size="50" maxlength="255" placeholder="<?=getMessage('WD_PROPSORTER_HEADER_PLACEHOLDER');?>" />'
					+ '<span class="wd_prop_item_inner_active">'
						+ '<label>'
							+ '<input type="hidden" name="prop_id[header_active_'+id+']" value="N" />'
							+ '<input type="checkbox" name="prop_id[header_active_'+id+']" value="Y" checked="checked" title="<?=getMessage('WD_PROPSORTER_HEADER_TITLE_ACTIVE');?>" /> '
							+ '<span title="<?=getMessage('WD_PROPSORTER_HEADER_TITLE_ACTIVE');?>"><?=getMessage('WD_PROPSORTER_HEADER_ACTIVE');?></span>'
						+ '</label>'
					+ '</span>'
				+ '<input type="button" value="&times;" title="<?=getMessage('WD_PROPSORTER_HEADER_DELETE');?>" />'
			+'</div></div></div></div>');
		$('input[type=checkbox]', newGroupHtml).each(function(){
			BX.adminFormTools.modifyCheckbox(this);
		});
		$('.wd_iblock_data:visible > .wd_prop_items').prepend(newGroupHtml.html()).sortable('refresh');
	}
	$('#wd_propsorter_add_value').val('');
});
$('#wd_propsorter_add_value').keydown(function(e){
	if (e.keyCode == 13) {
		e.preventDefault();
		$('#wd_propsorter_add_button').trigger('click');
	}
});
$(document).ready(function(){
	$('.wd_prop_items').sortable({
		connectWith: '.wd_prop_items',
		handle:'.wd_prop_item',
		helper: WD_SortableHelper,
		stop: WD_SortableOnStop,
		distance: 2,
		update: function(Event, UI) {}
	});
});
$(document).delegate('.wd_prop_item_group.wd_prop_item input[type=button]', 'click', function(e){
	e.preventDefault();
	if(confirm('<?=getMessage('WD_PROPSORTER_BUTTON_DELETE_CONFIRM');?>')){
		$(this).closest('.wd_prop_item_group').remove();
	}
});
$(document).delegate('input.adm-btn-save-to-iblock', 'click', function(e){
	e.preventDefault();
	if(confirm('<?=getMessage('WD_PROPSORTER_BUTTON_SAVE_TO_IBLOCK_CONFIRM');?>')){
		$.ajax({
			url: '<?=$APPLICATION->getCurPageParam('save_to_iblock=Y', ['save_to_iblock'])?>',
			type: 'POST',
			data: $('#wd_propsorter_form .wd_iblock_data .wd_prop_item_inner > input[type="hidden"]').serialize(),
			datatype: 'json',
			success: function(arJson) {
				if(arJson.Success){
					alert('<?=getMessage('WD_PROPSORTER_BUTTON_SAVE_TO_IBLOCK_SUCCESS');?>');
				}
				else{
					alert('<?=getMessage('WD_PROPSORTER_BUTTON_SAVE_TO_IBLOCK_ERROR');?>');
				}
			}
		});
	}
});
// Multi-select
window.wduPropsorterLastSelectedPropId = null;
$(document).delegate('.wd_prop_item_select_checkbox', 'click', function(e){
	let
		clickedId = $(this).attr('data-prop-id');
	if(e.shiftKey && window.wduPropsorterLastSelectedPropId && clickedId){
		let started = false;
		$('.wd_prop_item_select_checkbox').each(function(){
			let
				itemId = $(this).attr('data-prop-id'),
				isCurrent = itemId == clickedId || itemId == window.wduPropsorterLastSelectedPropId;
			if(started){
				$(this).prop('checked', true);
				if(isCurrent){
					started = false;
				}
			}
			else if(isCurrent){
				started = true;
			}
		});
	}
});
$(document).delegate('.wd_prop_item_select_checkbox', 'change', function(e){
	window.wduPropsorterLastSelectedPropId = $(this).attr('data-prop-id');
	$('.adm-btn-move-selected')
		.toggle(!!$('.wd_prop_item_select_checkbox:checked').length)
		.val($('.adm-btn-move-selected').attr('data-title') + ' (' + $('.wd_prop_item_select_checkbox:checked').length + ')');
});
$(document).delegate('.adm-btn-move-selected', 'click', function(e){
	if($('.wd_prop_item_select_checkbox').length){
		wduPopupMoveProps.Open();
	}
});
let wduPopupMoveProps = new WduPopup({
	height: 300,
	width: 600,
	title: '<?=getMessage('WD_PROPSORTER_POPUP_MOVE_TITLE');?>'
});
wduPopupMoveProps.Open = function(){
	let popup = this;
	popup.WdSetNavButtons([{
		'name': '<?=getMessage('WD_PROPSORTER_POPUP_MOVE_BUTTON');?>',
		'id': 'wdu_props_move',
		'className': 'adm-btn-green',
		'action': function(){
			let
				selectedGroup = $('.wd_group_move_wrapper input[type="radio"]:checked').closest('.wd_group_item'),
				selectedGroupId = selectedGroup.attr('data-group-id'),
				selectedPropertyId = $('select', selectedGroup).val(),
				isBegin = selectedPropertyId == '--begin--',
				isEnd = selectedPropertyId == '--end--',
				groupInList = $('.wd_prop_item.wd_prop_item_group[data-group-id="'+selectedGroupId+'"]'),
				propertyInList = $('.wd_prop_item[data-prop-id="'+selectedPropertyId+'"]'),
				checkedProperties = $('.wd_iblock_data:visible .wd_prop_item[data-prop-id] input[type="checkbox"]:checked'),
				animateClass = 'wd_prop_item_outer_animate';
			if(isBegin){
				propertyInList = groupInList
			}
			else if(isEnd){
				let
					lastLoopGroup;
				$('.wd_iblock_data:visible .wd_prop_item').each(function(){
					if($(this).is('.wd_prop_item_group')){
						lastLoopGroup = $(this).attr('data-group-id');
					}
					if(lastLoopGroup == selectedGroupId){
						propertyInList = $(this);
					}
				});
			}
			$(checkedProperties.get().reverse()).each(function(){
				let
					outer = $(this).closest('.wd_prop_item_outer');
				outer.insertAfter(propertyInList.closest('.wd_prop_item_outer'))
					.find('input[type="checkbox"]').prop('checked', false);
					outer.addClass(animateClass);
				setTimeout(function(){
					outer.removeClass(animateClass);
				}, 1);
			});
			$('.wd_iblock_data:visible > .wd_prop_items').sortable('refresh');
			popup.Close();
		}
	}]);
	popup.BuildContent();
	popup.Show();
}
wduPopupMoveProps.BuildContent = function(){
	this.WdSetContent('');
	let
		divContent = $('.wdu_bx_dialog_content', wduPopupMoveProps.PARTS.CONTENT_DATA),
		div = $('<div>').addClass('wd_group_move_wrapper').appendTo(divContent),
		groups = $('.wd_prop_item_group'),
		groupId,
		groupName,
		groupSelected = 'checked',
		groupHtml;
	groups.each(function(){
		groupId = $(this).attr('data-group-id');
		groupName = $('.wd_prop_item_header_name', this).val().trim();
		groupHtml = `
			<div class="wd_group_item" data-group-id="${groupId}">
				<input type="radio" name="group" id="${groupId}" value="${groupName}" ${groupSelected} />
				<label class="wd_group_name" for="${groupId}">
					<span>${groupName}</span>
				</label>
				<div class="wd_group_place">
					<div><?=getMessage('WD_PROPSORTER_POPUP_MOVE_SELECT_PLACE');?></div>
					<div>
						<select></select>
					</div>
				</div>
			</div>
		`;
		groupSelected = '';
		div.append(groupHtml);
	});
	$('.wd_group_item input[type="radio"]').first().trigger('change');
}
$(document).delegate('.wd_group_item input[type="radio"]', 'change', function(e){
	let
		currentGroup = $(this).closest('.wd_group_item'),
		currentGroupId = currentGroup.attr('data-group-id'),
		select = currentGroup.find('select'),
		lastLoopGroup;
	select.html('');
	select.append($('<option>').val('--begin--').text('<?=getMessage('WD_PROPSORTER_POPUP_MOVE_SELECT_PLACE_BEGIN');?>'));
	select.append($('<option>').val('--end--').text('<?=getMessage('WD_PROPSORTER_POPUP_MOVE_SELECT_PLACE_END');?>'));
	$('.wd_iblock_data:visible .wd_prop_item').each(function(){
		if(!$('.wd_prop_item_select_checkbox', this).is(':checked')){
			if($(this).is('.wd_prop_item_group')){
				lastLoopGroup = $(this).attr('data-group-id');
			}
			else if(currentGroupId == lastLoopGroup){
				select.append($('<option>').val(
					$(this).attr('data-prop-id')).text($('.wd_prop_item_property_text', this).text().trim())
				);
			}
		}
	});
	if($('option', select).length == 2){
		$('option', select).last().remove();
	}
});
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>