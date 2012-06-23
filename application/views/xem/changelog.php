
<h1>Changelog:</h1>
<?if($element->isDraft()):?>
<h2><?=anchor('xem/draft/'.$element->parent, $element->main_name)?></h2>
<?else:?>
<h2><?=anchor('xem/show/'.$element->id, $element->main_name)?></h2>
<?endif?>
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


<script type="text/javascript">
$('tr:has(.draft_bottom)').addClass('draft_bottom');
$('tr:has(.draft_top)').addClass('draft_top');
$('tr:has(.suppressed)').hide();
</script>


