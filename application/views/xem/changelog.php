<?


/*
var ul = $('<ul>');
	
	jQuery.each(data,function(k,event){
		var li = $('<li style="list-style: circle;margin-bottom: 10px;">');
		li.append(event.time+" "+event.user+" "+event.action+" "+event.type);
		var table = $('<table>');
		table.append('<tr><th>name</th><th>old</th><th>new</th></tr>')
		var curOld = JSON.parse(event.old);
		var curNew = JSON.parse(event.new);
		jQuery.each(curNew,function(key,value){
			table.append("<tr><td>"+key+": </td><td>"+curOld[key]+" &rarr;</td><td>"+curNew[key]+"</td></tr>");
		});
		li.append(table);

		ul.append(li);
	})
	$('#historyContainer').append(ul);


*/

?>



<h1>Changelog for <?=$element->main_name?></h1>

<table>
	<tr>
		<th colspan="4">Event</th>
		<th>Data</th>
	</tr>
	<?foreach($changelog as $curEvent):?>
	<tr>
		<td style="vertical-align:top;"><?=$curEvent['time']?></td>
		<td style="vertical-align:top;"><?=$curEvent['user_nick']?></td>
		<td style="vertical-align:top;"><?=$curEvent['action']?></td> 
		<td style="vertical-align:top;"><?=$curEvent['type']?></td>
		<td>
			<table>
				<tr><th>Field</th><th>Old Value</th><th>New Value</th></tr>	
				<?$curOld = json_decode($curEvent['old']);
				$curNew = json_decode($curEvent['new']);
				foreach($curNew as $key=>$value):?>
					<tr><td><?=$key?>: </td><td><?=$curOld->$key?>&rarr;</td><td><?=$curNew->$key?></td></tr>
				<?endforeach?>
			</table>
		</td>
	</tr>
	<?endforeach?>

</table>