
<ul class="breadcrumb">
<?if($element->isDraft()):?>
<li><?=anchor('xem/draft/'.$element->parent, $element->main_name)?> <span class="divider">/</span></li>
<?else:?>
<li><?=anchor('xem/show/'.$element->id, $element->main_name)?> <span class="divider">/</span></li>
<?endif?>
<li class="active">ChangeLog</li>
</ul>

<table class="table table-condensed table-striped" style="width: auto;">
    <!--
    <thead>
        <tr>
            <th>Time</th>
            <th>User</th>
            <th>Description</th>
        </tr>
    </thead>
    -->
    <tbody>
    <?foreach($events as $curEvent):?>
        <tr>
            <td title="id: <?=$curEvent['id']?>"><?=$curEvent['time']?></td>
            <td><?=$curEvent['user_nick']?></td>
            <td><?=$curEvent['human_form']?></td>
        </tr>
    <?endforeach?>
    </tbody>
</table>

<script type="text/javascript">
$('tr:has(.draft_bottom)').addClass('draft_bottom');
$('tr:has(.draft_top)').addClass('draft_top');
$('tr:has(.suppressed)').hide();
</script>
