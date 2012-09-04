<div class="page-header">
    <h1>TV Show List <small>wip</small></h1>
</div>

<table class="table table-striped" style="width: auto;">
    <thead>
        <tr>
            <th>id</th>
            <th>modified</th>
            <th>show name</th>
            <th>xxx</th>
        </tr>
    </thead>
    <tbody>
    <?foreach($curShows as $show):?>
        <tr>
            <td>
                <?=$show->id?>
            </td>
            <td>
                2012-09-03
            </td>
            <td>
                <?=anchor('xem/show/'.$show->id,$show->main_name)?>
                <?if(isset($show->name)):?>
                (<?=$show->name?>)
                <?endif?>
            </td>
            <td><? print_r($show) ?></td>
        </tr>
    <?endforeach?>
    </tbody>
</table>
