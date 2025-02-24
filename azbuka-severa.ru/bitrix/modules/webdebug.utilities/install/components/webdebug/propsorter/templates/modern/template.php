<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<?if(!empty($arResult['PROPS_GROUPS'])):?>
	<div class="wdu_propsorter">
		<table>
			<?foreach($arResult['PROPS_GROUPS'] as $arGroup):?>
				<?if(is_array($arGroup['ITEMS']) && !empty($arGroup['ITEMS'])):?>
					<tbody>
						<?if(strlen($arGroup['NAME'])):?>
							<tr>
								<th colspan="2">
									<span>
										<?=$arGroup['NAME'];?>
									</span>
								</th>
							</tr>
						<?endif?>
						<?foreach($arGroup['ITEMS'] as $arProp):?>
							<tr>
								<td>
									<span>
										<?=$arProp['NAME'];?>
										<?if($arParams['SHOW_HINTS'] == 'Y' && strlen($arProp['HINT'])):?>
											<sup title="<?=htmlspecialcharsbx($arProp['HINT']);?>"></sup>
										<?endif?>
									</span>
								</td>
								<td>
									<span>
										<?=$arProp['DISPLAY_VALUE'];?>
									</span>
								</td>
							</tr>
						<?endforeach?>
					<?endif?>
				</tbody>
			<?endforeach?>
		</table>
	</div>
<?endif?>
