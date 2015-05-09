<div class="page-header">
    <h1>Admin View <small>Every deleted or previous draft will open in an 'admin view'.</small></h1>
</div>

<style type="text/css">
<!--
.adminShows .draft.lvl_-1,
.adminShows .draft.lvl_-1 a {
    color: tan;
}
.adminShows .lvl_0,
.adminShows .lvl_0 a {
    color: grey;
}
.adminShows .draft.lvl_1,
.adminShows .draft.lvl_1 a {
    color: green;
}
.adminShows .draft.lvl_4,
.adminShows .draft.lvl_4 a{
    color: red;
}
.adminShows .show.lvl_1 {
    color: #0088CC;
}
-->
</style>

<div class="adminShows">
    <input type="button" class="btn" value="Toggle" onclick="$('.list .show.lvl_1,.list .show.lvl_2,.list .show.lvl_3,.list .show.lvl_4').toggle()"/>
    <label>Blue<span class="list"><span class="show lvl_1">√</span></span></label><span>Public Show</span><br/>

    <input type="button" class="btn" value="Toggle" onclick="$('.list .lvl_0').toggle()"/>
    <label>Grey<span class="list"><span class="lvl_0">√</span></span></label><span class="lvl_0">Deleted</span><br/>

    <input type="button" class="btn" value="Toggle" onclick="$('.list .draft.lvl_-1').toggle()"/>
    <label>Tan<span class="list"><span class="draft lvl_-1">√</span></span></label><span class="draft lvl_-1">Old Public</span><br/>

    <input type="button" class="btn" value="Toggle" onclick="$('.list .draft.lvl_1').toggle()"/>
    <label>Green<span class="list"><span class="draft lvl_1">√</span></span></label><span class="draft lvl_1">Current Draft</span><br/>

    <input type="button" class="btn" value="Toggle" onclick="$('.list .draft.lvl_4').toggle()"/>
    <label>Red<span class="list"><span class="draft lvl_4">√</span></span></label><span class="draft lvl_4">Current Draft waiting for approval!</span><br/>

    <input type="button" class="btn" value="Toggle Drafts" onclick="$('.drafts').toggle()"/>
    <input type="button" class="btn" value="Toggle Old/Deleted Drafts" onclick="$('.oldDrafts, .list .drafts .lvl_0, .list .drafts .lvl_-1').toggle()"/>
</div>
<br/>
<div class="well">
<?if($curShows):?>
<ul class="adminShows list">
	<?foreach($curShows as $root_id=>$drafPublic):?>
    <?if (!isset($drafPublic['public'])) continue;?>
    <?$show = $drafPublic['public']?>
	<li class="show lvl_<?=$show->status?>">
		<?=anchor('xem/show/'.$show->id,$show->main_name)?>
        <?if(isset($drafPublic['draft'])):?>
        <ul class="drafts">
            <?foreach($drafPublic['draft'] as $id=>$draft):?>
            <li class="draft lvl_<?=$draft->status?>">
                <?if($draft->status > 0):?>
                <?=anchor('xem/draft/'.$draft->parent, 'Draft '.$id)?>
                <?else:?>
                <?=anchor('xem/adminShow/'.$draft->parent, 'Draft '.$id)?>
                <?endif;?>
                <?if(isset($curShows[$id])):?>
                <ul class="oldDrafts">
                    <?foreach($curShows[$id]['draft'] as $id=>$draft):?>
                    <li class="draft lvl_<?=$draft->status?>">
                        previous <?=anchor('xem/adminShow/'.$id, 'draft '.$id)?>
                    </li>
                    <?endforeach;?>
                </ul>
                <?endif?>
            </li>
            <?endforeach;?>
        </ul>
        <?endif?>
	</li>
	<?endforeach?>
</ul>
<?else:?>
<h1>No Shows Found</h1>
<?endif;?>
</div>

<script type="text/javascript">
$('.oldDrafts, .list .drafts .lvl_0, .list .drafts .lvl_-1').toggle();
</script>
