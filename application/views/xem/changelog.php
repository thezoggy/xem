
<h1><?=anchor('xem/show/'.$element->id, $element->main_name)?> Changelog</h1>

<table id="changelog">
    <!--
    <tr>
        <th>Time</th>
        <th>User</th>
        <th>Description</th>
    </tr>
    -->
	<?foreach($events as $curEvent):?>
	<tr>
		<td style="padding-right:20px;" title="id: <?=$curEvent['id']?>"><?=$curEvent['time']?></td>
		<td style="text-align:right;padding-right:4px;"><?=$curEvent['user_nick']?></td>
		<td><?=$curEvent['human_form']?></td>
	</tr>
	<?endforeach?>

</table>
