<div class="well">
    <?if($curShows):?>
    <?if(isset($forceAdd)):?>
    <h2>We already have shows that contain that name</h2>
    <?endif;?>
    <ul>
        <?foreach($curShows as $show):?>
        <li>
            <?=anchor('xem/show/'.$show->id,$show->main_name)?>
            <?if(isset($show->name)):?>
            (<?=$show->name?>)
            <?endif?>
        </li>
        <?endforeach?>
    </ul>
    <?else:?>
    <h1>No Shows Found</h1>
    <?endif;?>
    <?if(isset($forceAdd)):?>
    <h2>Add Show</h2>
    <?=form_open("xem/addShow")?>
        <input id="newElementName" name="main_name" class="input-large">
        <label class="checkbox" for="forceAdd"><input type="checkbox" name="forceAdd" id="forceAdd"> I know what I am doing</label>
        <input type="submit" value="Add" class="btn btn-danger">
    </form>
<?endif;?>
</div>