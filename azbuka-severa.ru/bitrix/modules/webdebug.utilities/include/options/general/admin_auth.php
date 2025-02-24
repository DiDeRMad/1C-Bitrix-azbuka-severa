<?
namespace WD\Utilities;

use
	\WD\Utilities\Helper,
	\WD\Utilities\Options;

Helper::loadMessages();

return [
	'ITEMS' => [
		'admin_auth_notify' => [
			'CALLBACK_HEAD_DATA' => function($obOptions, $arOption, $strOption, $strOptionId){
				?>
				<script>
				$(document).delegate('#<?=$strOptionId;?>', 'change', function(e){
					$('#<?=$obOptions->getOptionPrefix('admin_auth_email');?>').closest('tr').toggle($(this).prop('checked'));
					$('#<?=$obOptions->getOptionPrefix('admin_auth_http_request');?>').closest('tr').toggle($(this).prop('checked'));
				});
				$(document).ready(function(){
					$('#<?=$strOptionId;?>').trigger('change');
				});
				</script>
				<?
			},
		],
		'admin_auth_email' => [
			'TYPE' => 'text',
			'ATTR' => 'size="50"',
		],
		'admin_auth_http_request' => [
			'TYPE' => 'textarea',
			'TOP' => 'Y',
			'ATTR' => 'style="box-sizing:border-box;width:100%;" rows="3"',
		],
	],
];
?>