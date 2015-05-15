<div class="well">
    <?if($curShows):?>
    <?if(isset($forceAdd)):?>
        <h2>We already have shows that contain that name</h2>
    <?endif;?>
        <table class="table table-striped table-condensed" style="width: auto; margin-bottom: 0;">
            <thead>
                <tr>
                    <th>show name</th>
                    <th>created</th>
                    <th>last modified</th>
                </tr>
            </thead>
            <tbody>
            <?foreach($curShows as $show):?>
                <tr>
                    <td>
                        <?=anchor('xem/show/'.$show->id,$show->main_name)?>
                        <?if(isset($show->name)):?>
                            (<?=$show->name?>)
                        <?endif?>
                    </td>
                    <td>
                        <span title="<?=$show->created?> UTC"><? $data = explode(' ', $show->created); echo $data[0] ?></span>
                    </td>
                    <td>
                        <?=$show->last_modified?> UTC
                    </td>
                </tr>
            <?endforeach?>
            </tbody>
        </table>
    <?else:?>
        <h2>No Shows Found</h2>
    <?endif;?>

    <?if(isset($forceAdd)):?>
        <h2>Add Show</h2>
        <?=form_open("xem/addShow", array('class' => 'form-horizontal'))?>
            <input id="newElementName" name="main_name" class="input-large">
            <label class="checkbox" for="forceAdd"><input type="checkbox" name="forceAdd" id="forceAdd"> I know what I am doing</label>
            <input type="submit" value="Add" class="btn btn-danger">
        </form>
    <?endif;?>
</div>
