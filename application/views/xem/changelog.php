
<h1>Changelog:</h1>
<h2><?=anchor('xem/show/'.$element->id, $element->main_name)?></h2>

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
		<td style="padding-right:20px;white-space:nowrap;" title="id: <?=$curEvent['id']?>"><?=$curEvent['time']?></td>
		<td style="text-align:right;padding-right:4px;"><?=$curEvent['user_nick']?></td>
		<td><?=$curEvent['human_form']?></td>
	</tr>
	<?endforeach?>

</table>
