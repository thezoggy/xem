
<style type="text/css">
<!--
.adminShows .draft.lvl_-1 {
    color: grey;
}
.adminShows .lvl_0 {
    color: white;
    text-shadow: -1px 0 #000, 0 1px #000,1px 0 #000, 0 -1px #000;
}

.adminShows .draft.lvl_1 {
    color: green;
}
.adminShows .draft.lvl_4 {
    color: blue;
}
-->
</style>

<div class="adminShows">
    <input type="button" value="Toggle" onCLick="$('.list .show.lvl_1,.list .show.lvl_2,.list .show.lvl_3').toggle()"/>
    <label>Black<span class="list"><span class="show lvl_1">√</span></span></label><span>Public Show</span><br/>

    <input type="button" value="Toggle" onCLick="$('.list .lvl_0').toggle()"/>
    <label>White<span class="list"><span class="lvl_0">√</span></span></label><span class="lvl_0">Deleted</span><br/>

    <input type="button" value="Toggle" onCLick="$('.list .draft.lvl_-1').toggle()"/>
    <label>Grey<span class="list"><span class="draft lvl_-1">√</span></span></label><span class="draft lvl_-1">Old Public</span><br/>

    <input type="button" value="Toggle" onCLick="$('.list .draft.lvl_1').toggle()"/>
    <label>Green<span class="list"><span class="draft lvl_1">√</span></span></label><span class="draft lvl_1">Current Draft</span><br/>

    <input type="button" value="Toggle" onCLick="$('.list .draft.lvl_4').toggle()"/>
    <label>Blue<span class="list"><span class="draft lvl_4">√</span></span></label><span class="draft lvl_4">Current Draft waiting for approval!</span><br/>

    <input type="button" value="Toggle Drafts" onCLick="$('.drafts').toggle()"/><input type="button" value="Toggle Old/Deleted Drafts" onCLick="$('.oldDrafts, .list .drafts .lvl_0, .list .drafts .lvl_-1').toggle()"/><br>
    Every deleted or previous draft will open in an "admin view" ... there are no restrictions for admins
    <br/>
</div>

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
                        prevoius <?=anchor('xem/adminShow/'.$id, 'draft '.$id)?>
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

<script type="text/javascript">
$('.oldDrafts, .list .drafts .lvl_0, .list .drafts .lvl_-1').toggle();
</script>
