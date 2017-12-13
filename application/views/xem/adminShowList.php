<div class="page-header">
    <h1>Admin View <small>Every deleted or previous draft will open in an 'admin view'.</small></h1>
</div>

<style type="text/css">
<!--
.show {
}
.show-public, .show-public a {
    color: #0088CC;
}
.show-deleted, .show-deleted a,
.draft-deleted, .draft-deleted a {
    color: grey;
}
.draft-pending, .draft-pending a {
    color: red;
}
.draft-current, .draft-current a {
    color: green;
}
.draft-old, .draft-old a {
    color: tan;
}
.show-locked, .show-locked a {
    color: #7932ea;
}
#adminListTB {
    margin: 0 0 10px;
}
#adminListTB h4 {
    margin: 0 0 5px;
}
.hiddenRow {
    display: none;
}

.legend {
    margin: 5px;
}
.legend ul {
    float: left;
    list-style: outside none none;
    margin: 0 0 5px;
    padding: 0;
}
.legend ul li {
    font-size: 80%;
    line-height: 18px;
    list-style: outside none none;
    margin-bottom: 2px;
    margin-left: 0;
}
.legend ul li span {
    border: medium none;
    border-radius: 3px;
    display: block;
    float: left;
    height: 16px;
    margin-left: 0;
    margin-right: 5px;
    width: 25px;
}

.key .legend-label {
    display: inline-block;
    width: 80px;
}
.key .public {
    background-color: #428bca;
    border-color: #428bca;
}
.key .info {
    background-color: #35c5f4;
    border-color: #35c5f4;
}
.key .deleted {
    background-color: #999;
    border-color: #999;
}
.key .old {
    background-color: tan;
    border-color: tan;
}
.key .pending {
    background-color: red;
    border-color: red;
}
.key .current {
    background-color: green;
    border-color: green;
}
.key .locked {
    background-color: #7932ea;
    border-color: #7932ea;
}

-->
</style>

<?if($curShows):?>

    <div id="adminListTB" class="row">
        <div style="box-sizing: border-box;display: block;float: left; width: 350px;">
            <h4>Shows:</h4>
            <div class="btn-toolbar" style="margin: 0;">
                <div id="showGroup" class="btn-group" data-toggle="buttons-radio">
                    <button class="btn btn-info" data-target="show-none" title="Hide Shows"><i class="icon-remove icon-white"></i></button>
                    <button class="btn btn-info" data-target="public" title="Display both Public and Deleted Shows">All</button>
                    <button class="btn btn-info active" data-target="show-public" title="Shows that are currently public (locked can be toggled)">Public</button>
                    <button class="btn btn-info" data-target="show-locked" title="Only display public shows that are locked">Locked</button>
                    <button class="btn btn-info" data-target="show-deleted" title="Shows that have been deleted">Deleted</button>
                </div>
                <div class="btn-group">
                    <button id="toggleShowLocked" class="btn btn-info active" data-target="show-locked" title="Toggle public shows with locked status"><i class="icon-lock icon-white"></i></button>
                </div>
            </div>
            <div class="legend key">
                <ul class="legend-labels">
                    <li class="legend-label"><span class="public"></span>Public</li>
                    <li class="legend-label"><span class="locked"></span>Locked</li>
                    <li class="legend-label"><span class="deleted"></span>Deleted</li>
                </ul>
            </div>
        </div>

        <div style="box-sizing: border-box;display: block;float: left; width: 350px; margin-left: 15px;">
            <h4>Drafts:</h4>
            <div class="btn-group">
                <button id="hideDrafts" class="btn btn-warning" title="Hide Drafts"><i class="icon-remove icon-white"></i></button>
                <button id="toggleCurrentDrafts" class="btn btn-warning active" title="Drafts created but not yet submitted for approval">Current</button>
                <button id="togglePendingDrafts" class="btn btn-warning active" title="Drafts pending approval">Pending</button>
                <button id="toggleOldDrafts" class="btn btn-warning" title="Drafts that were previously public">Old</button>
                <button id="toggleDeletedDrafts" class="btn btn-warning" title="Drafts that have been deleted">Deleted</button>
            </div>
            <div class="legend key">
                <ul class="legend-labels">
                    <li class="legend-label"><span class="current"></span>Current</li>
                    <li class="legend-label"><span class="pending"></span>Pending</li>
                    <li class="legend-label"><span class="old"></span>Old</li>
                    <li class="legend-label"><span class="deleted"></span>Deleted</li>
                </ul>
            </div>
        </div>
    </div>

    <table style="width: auto; margin-bottom: 0;">
        <thead>
            <tr>
                <th class="input-small">id</th>
                <th>show name</th>
                <th class="input-small">created</th>
                <th>last modified</th>
                <th class="input-mini">status</th>
            </tr>
        </thead>
        <tbody>
        <?foreach($curShows as $root_id=>$shows):?>
            <?if (!isset($shows['public'])) continue;?>
            <?$curPublicShow = $shows['public']?>
            <tr class="public <? echo $curPublicShow->status > 0 ? 'show-public' : 'show-deleted' ?><? echo $curPublicShow->status >= 1 && $curPublicShow->status <= 3 ? ' show-locked' : '' ?>">
                <td>
                    <?=anchor('xem/show/'.$curPublicShow->id, 'Show ' . $curPublicShow->id)?>
                </td>
                <td>
                    <?=$curPublicShow->main_name?>
                </td>
                <td>
                    <span title="<?=$curPublicShow->created?> UTC"><? $data = explode(' ', $curPublicShow->created); echo $data[0] ?></span>
                </td>
                <td>
                    <?echo $curPublicShow->last_modified == "0000-00-00 00:00:00" ? '' : $curPublicShow->last_modified .' UTC'?>
                </td>
                <td>
                    <?=$curPublicShow->status?>
                </td>
            </tr>

            <?if(isset($shows['draft'])):?>
                <!-- start drafts -->
                <?foreach($shows['draft'] as $id=>$draft):?>
                    <tr class="draft
                        <?
                        switch ($draft->status) {
                            case -1:
                                echo "draft-old";
                                break;
                            case 0:
                                echo "draft-deleted";
                                break;
                            case 4:
                                echo "draft-pending";
                                break;
                            default:
                                echo "draft-current";
                                break;
                        }
                        ?>
                    ">
                        <td>
                            <?if($draft->status > 0):?>
                                <?=anchor('xem/draft/'.$draft->parent, 'Draft ' . $id)?>
                            <?else:?>
                                <?=anchor('xem/adminShow/'.$draft->parent, 'Draft ' . $id)?>
                            <?endif;?>
                        </td>
                        <td>
                            <?=$draft->main_name?>
                        </td>
                        <td>
                            <span title="<?=$draft->created?> UTC"><? $data = explode(' ', $draft->created); echo $data[0] ?></span>
                        </td>
                        <td>
                            <?echo $draft->last_modified == "0000-00-00 00:00:00" ? '' : $draft->last_modified .' UTC'?>
                        </td>
                        <td>
                            <?=$draft->status?>
                        </td>
                    </tr>

                    <?if(isset($curShows[$id])):?>
                        <!-- start oldDrafts -->
                        <?foreach($curShows[$id]['draft'] as $id=>$draft):?>
                            <tr class="draft draft-old">
                                <td>
                                    <?=anchor('xem/adminShow/'.$id, 'Draft ' . $id)?>
                                </td>
                                <td>
                                    <?=$draft->main_name?>
                                </td>
                                <td>
                                    <span title="<?=$draft->created?> UTC"><? $data = explode(' ', $draft->created); echo $data[0] ?></span>
                                </td>
                                <td>
                                    <?echo $draft->last_modified == "0000-00-00 00:00:00" ? '' : $draft->last_modified .' UTC'?>
                                </td>
                                <td>
                                    <?=$draft->status?>
                                </td>
                            </tr>
                            <!-- end oldDrafts -->
                        <?endforeach;?>
                    <?endif?>
                <?endforeach;?>
                <!-- end drafts -->

            <?endif?>
        <?endforeach?>
        </tbody>
    </table>

<?else:?>
<div class="well">
    <h1>No Shows Found</h1>
</div>
<?endif;?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.15/js/jquery.tablesorter.combined.min.js"></script>
<script>
    $(function() {
        // override init to remove table-striped
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
            sortList: [[1,0],[4,1]], // sort by name then status
            widgetOptions : {
                zebra : ["even", "odd"],
                filter_hideEmpty : true,
                filter_hideFilters : false
            }
        });
    });

    // initialize selection
    $('.show-deleted, .draft-old, .draft-deleted').addClass('hiddenRow');

    $('#showGroup.btn-group button').click(function(){
        var target = "." + $(this).data("target");
        $(".public").not(target).addClass('hiddenRow');
        $(target).removeClass('hiddenRow');
        // toggle lock visibility if public shows are shown...
        if(target == '.show-none' || target == '.show-deleted') {
            $(toggleShowLocked).removeClass('active');
        } else {
            $(toggleShowLocked).addClass('active');
        }
        $('table').trigger('applyWidgets');
    }).filter('[data-target="show-public"]').click();

    $('#toggleShowLocked').click(function() {
        // only toggle lock visibility if public shows are shown...
        if($('button[data-target="public"]').hasClass('active') || $('button[data-target="show-public"]').hasClass('active')) {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
                $('.show-locked').addClass('hiddenRow');
            } else {
                $(this).addClass('active');
                $('.show-locked').removeClass('hiddenRow');
            }
            $('table').trigger('applyWidgets');
        }
    });

    $('#hideDrafts').click(function(){
        $(this).addClass('active').siblings().removeClass('active');
        $('.draft').addClass('hiddenRow');
        $('table').trigger('applyWidgets');
    });
    $('#toggleCurrentDrafts').click(function() {
        if($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.draft-current').addClass('hiddenRow');
            // toggle hideDrafts if all options are not active
            if( !$('#toggleCurrentDrafts').hasClass('active') && !$('#togglePendingDrafts').hasClass('active') && !$('#toggleOldDrafts').hasClass('active') && !$('#toggleDeletedDrafts').hasClass('active')) {
                $('#hideDrafts').addClass('active');
            }
        } else {
            $('#hideDrafts').removeClass('active');
            $(this).addClass('active');
            $('.draft-current').removeClass('hiddenRow');
        }
        $('table').trigger('applyWidgets');
    });
    $('#togglePendingDrafts').click(function() {
        if($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.draft-pending').addClass('hiddenRow');
            // toggle hideDrafts if all options are not active
            if( !$('#toggleCurrentDrafts').hasClass('active') && !$('#togglePendingDrafts').hasClass('active') && !$('#toggleOldDrafts').hasClass('active') && !$('#toggleDeletedDrafts').hasClass('active')) {
                $('#hideDrafts').addClass('active');
            }
        } else {
            $('#hideDrafts').removeClass('active');
            $(this).addClass('active');
            $('.draft-pending').removeClass('hiddenRow');
        }
        $('table').trigger('applyWidgets');
    });
    $('#toggleOldDrafts').click(function() {
        if($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.draft-old').addClass('hiddenRow');
            // toggle hideDrafts if all options are not active
            if( !$('#toggleCurrentDrafts').hasClass('active') && !$('#togglePendingDrafts').hasClass('active') && !$('#toggleOldDrafts').hasClass('active') && !$('#toggleDeletedDrafts').hasClass('active')) {
                $('#hideDrafts').addClass('active');
            }
        } else {
            $('#hideDrafts').removeClass('active');
            $(this).addClass('active');
            $('.draft-old').removeClass('hiddenRow');
        }
        $('table').trigger('applyWidgets');
    });
    $('#toggleDeletedDrafts').click(function() {
        if($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.draft-deleted').addClass('hiddenRow');
            // toggle hideDrafts if all options are not active
            if( !$('#toggleCurrentDrafts').hasClass('active') && !$('#togglePendingDrafts').hasClass('active') && !$('#toggleOldDrafts').hasClass('active') && !$('#toggleDeletedDrafts').hasClass('active')) {
                $('#hideDrafts').addClass('active');
            }
        } else {
            $('#hideDrafts').removeClass('active');
            $(this).addClass('active');
            $('.draft-deleted').removeClass('hiddenRow');
        }
        $('table').trigger('applyWidgets');
    });
    $('#adminListTB button[title]').qtip({
        style: {
            classes: 'qtip-dark qtip-shadow qtip-rounded'
        },
        position: {
            my: 'top center',
            at: 'bottom center'
        },
    });

</script>