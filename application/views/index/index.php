
<h1>Welcome to xem</h1>
<h2>The Xross Entity Map for TV shows*</h2>
<p>
	xem helps you keep an overview of your shows on different websites.<br/>
	Many websites use different numberings, names and season titles for the same show.<br/>
	xem lets you create a map for an easy overview of all the different counting and naming systems.
</p>

<h2>Entity list</h2>
<table border=1>
<tr>
	<th>id</th>
	<th>name</th>
	<th>desc</th>
	<th>url</th>
</tr>
<? foreach($locations->result() as $row):?>
<tr class="<?=$row->name?>">
	<td><?=$row->id?></td>
	<td><?=$row->name?></td>
	<td><?=$row->description?></td>
	<td><?=anchor($row->url)?></td>
</tr>
<? endforeach?>
</table>


<span class="note">*And soon for motion pictures as well</span>