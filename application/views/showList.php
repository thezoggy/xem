<div>
    <?if($curShows):?>
    <?if(isset($forceAdd)):?>
        <h2>We already have shows that contain that name</h2>
    <?else:?>
        <h3><? echo count($shows); ?> Shows Found</h3>
    <?endif;?>
        <table style="width: auto; margin-bottom: 0;">
            <thead>
                <tr>
                    <th>show name</th>
                    <th class="input-small">created</th>
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
                        <?echo $show->last_modified == "0000-00-00 00:00:00" ? '' : $show->last_modified .' UTC'?>
                    </td>
                </tr>
            <?endforeach?>
            </tbody>
        </table>
    <?else:?>
        <h2>No Shows Found</h2>
    <?endif;?>

    <?if(isset($forceAdd)):?>
    <br>
    <div class="well" style="margin-bottom: 0;">
        <h2>Add Show</h2>
        <?=form_open("xem/addShow", array('class' => 'form-horizontal'))?>
            <input id="newElementName" name="main_name" class="input-large">
            <label class="checkbox" for="forceAdd"><input type="checkbox" name="forceAdd" id="forceAdd"> I know what I am doing</label>
            <input type="submit" value="Add" class="btn btn-danger">
        </form>
    </div>
    <?endif;?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.15/js/jquery.tablesorter.combined.min.js"></script>
<script>
    $(function() {
        $.tablesorter.filter.types.start = function( config, data ) {
            if ( /^\^/.test( data.iFilter ) ) {
                return data.iExact.indexOf( data.iFilter.substring(1) ) === 0;
            }
            return null;
        };
        $.tablesorter.filter.types.end = function( config, data ) {
            if ( /\$$/.test( data.iFilter ) ) {
                var filter = data.iFilter,
                    filterLength = filter.length - 1,
                    removedSymbol = filter.substring(0, filterLength),
                    exactLength = data.iExact.length;
                return data.iExact.lastIndexOf(removedSymbol) + filterLength === exactLength;
            }
            return null;
        };
        $("table").tablesorter({
            theme : "bootstrap",
            headerTemplate : '{content} {icon}',
            sortLocaleCompare : true,
            emptyTo: 'bottom',
            widgets : [ "uitheme", "filter", "zebra" ],
            widgetOptions : {
                zebra : ["even", "odd"],
                filter_hideEmpty : true,
                filter_hideFilters : false
            }
        });
    });
</script>