<div class="page-header">
    <h1>Welcome to XEM</h1>
</div>

<h2>The Xross Entity Map for TV shows</h2>
<p>
    xem helps you keep an overview of your shows on different websites.<br/>
    Many websites use different numberings, names and season titles for the same show.<br/>
    xem lets you create a map for an easy overview of all the different counting and naming systems.
</p>

<table id="entity" class="table table-condensed table-bordered" style="width: auto;">
    <thead>
        <tr>
            <th>id</th>
            <th>entity</th>
            <th>description</th>
            <th>url</th>
        </tr>
    </thead>
    <tbody>
        <? foreach($locations->result() as $row):?>
        <tr class="<?=$row->name?>">
            <td><?=$row->id?></td>
            <td><?=$row->name?></td>
            <td><?=$row->description?></td>
            <td><?=anchor($row->url)?></td>
        </tr>
        <? endforeach?>
    </tbody>
</table>

<?if($events):?>
<h2>Previously on XEM</h2>
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
        <td><?=$curEvent['human_form']?><?if($curEvent['element_id']):?> &rarr; <b><?=anchor("xem/show/".$curEvent['element_id'], "element ".$curEvent['element_id'])?></b><?endif;?></td>
    </tr>
    <?endforeach?>
</table>
<?endif;?>
