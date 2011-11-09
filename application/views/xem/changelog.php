
<h1>Changelog for <?=$element->main_name?></h1>

<table>
	<?foreach($events as $curEvent):?>
	<tr>
		<td style="vertical-align:top;"><?=$curEvent['time']?></td>
		<td style="vertical-align:top;"><?=$curEvent['user_nick']?></td>
		<td>
            <?=$curEvent['human_form']?>
            <!--
			<table>
				<tr><th>Field</th><th>Old Value</th><th>New Value</th></tr>
				<?/*$curOld = json_decode($curEvent['old']);
				$curNew = json_decode($curEvent['new']);
				foreach($curNew as $key=>$value):?>
					<tr><td><?=$key?>: </td><td><?=$curOld->$key?>&rarr;</td><td><?=$curNew->$key?></td></tr>
				<?endforeach*/
				?>
			</table>
            -->
		</td>
	</tr>
	<?endforeach?>

</table>