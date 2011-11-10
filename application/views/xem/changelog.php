
<h1>Changelog for <?=anchor('xem/show/'.$element->id, $element->main_name)?></h1>

<table id="changelog">
	<?foreach($events as $curEvent):?>
	<tr>
		<td style="padding-right:20px;" title="id: <?=$curEvent['id']?>"><?=$curEvent['time']?></td>
		<td style="text-align:right;padding-right:4px;"><?=$curEvent['user_nick']?></td>
		<td><?=$curEvent['human_form']?></td>
	</tr>
	<?endforeach?>

</table>