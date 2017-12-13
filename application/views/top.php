<!doctype html>
<!--

  ,,                 ,,
`7MM               `7MM
  MM                 MM   __,
  MM   ,6"Yb.   ,M""bMM  `7MM  pd""b.   pd""b.  M******A'
  MM  8)   MM ,AP    MM    MM (O)  `8b (O)  `8b Y     A'
  MM   ,pm9MM 8MI    MM    MM      ,89      ,89      A'
  MM  8M   MM `Mb    MM    MM    ""Yb.    ""Yb.     A'
.JMML.`Moo9^Yo.`Wbmd"MML..JMML.     88       88    A'
                              (O)  .M' (O)  .M'   A'
                               bmmmd'   bmmmd'   A'




M"""MMV ,pW"Wq.   .P"Ybmmm  .P"Ybmmm `7M'   `MF'
'  AMV 6W'   `Wb :MI  I8   :MI  I8     VA   ,V
  AMV  8M     M8  WmmmP"    WmmmP"      VA ,V
 AMV  ,YA.   ,A9 8M        8M            VVV
AMMmmmM `Ybmd9'   YMMMMMb   YMMMMMb      ,V
                 6'     dP 6'     dP    ,V
                 Ybmmmd'   Ybmmmd'   OOb"

made at http://patorjk.com/software/taag/ with font Georgia11
-->
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?=$title?> | Xem</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!--[if lt IE 9]>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
        <![endif]-->

<!--
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="./images/apple-touch-icon-144x144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="./images/apple-touch-icon-114x114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="./images/apple-touch-icon-72x72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="./images/apple-touch-icon-57x57-precomposed.png">
-->

        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <? echo link_tag('css/smoothness/jquery-ui-1.11.4.custom.css', 'stylesheet', 'text/css'); ?>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.3/jquery.qtip.min.css" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.15/css/theme.bootstrap_2.min.css" rel="stylesheet" type="text/css" />
        <? echo link_tag('css/main.css', 'stylesheet', 'text/css'); ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="<?php echo base_url();?>js/html5boilerplate.consolewrapper.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url();?>js/jquery.dataset.js"></script>
        <script src="<?php echo base_url();?>js/jquery.jeditable-1.7.3-custom.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.3/jquery.qtip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.7/raphael.min.js"></script>

        <!-- own stuff -->
        <script src="<?php echo base_url();?>js/main.js"></script>
        <script src="<?php echo base_url();?>js/xem.logo.js"></script>

    </head>
<body class="<?if(isset($fullelement)){if($fullelement->isDraft) echo 'draft';} ?>">
    <!--[if lte IE 9]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->
<div id="page">
    <?if(isset($fullelement)):?>
    <?if($fullelement->isDraft):?>
    <div class="draft_background" id="draft_left">DRAFT</div>
    <div class="draft_background" id="draft_right">DRAFT</div>
    <?endif?>
    <?endif?>

    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <a class="brand" href="/" title="XEM"><div id="logo"></div></a>

                <div class="nav-collapse">
                    <ul class="nav">
                        <li class="divider-vertical"></li>
                        <li class="dropdown">
                            <?if($logedIn):?>
                            <a class="dropdown-toggle" href="#" data-toggle="dropdown"><? echo $user_nick ?> <strong class="caret"></strong></a>
                            <ul class="dropdown-menu">
                                <li><?=anchor("user","<i class='icon-user'></i> Profile (Level $user_lvl)")?></li>
                                <li><?=anchor("user/logout/".$uri, "<i class='icon-off'></i> Log Out")?></li>
                            <?if(grantAccess(4)):?>
                                <li class="divider"></li>
                                <li><?=anchor("xem/adminShows","<i class='icon-fire'></i> Admin View")?></li>
                            <?endif?>
                            </ul>
                            <?else:?>
                            <a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <strong class="caret"></strong></a>
                            <div class="dropdown-menu" style="padding: 10px 15px;">
                                <?=form_open("user/login/".$uri, array('class'=>'form-inline'))?>
                                <fieldset>
                                    <legend>Sign In</legend>
                                    <div class="control-group">
                                        <label class="control-label" for="signin_user">Username:</label>
                                        <div class="controls">
                                            <input type="text" id="signin_user" name="user" class="input-large" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="signin_pw">Password:</label>
                                        <div class="controls">
                                            <input type="password" id="signin_pw" name="pw" class="input-large">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <?=anchor("user/register","Need an account?", array('class' => 'btn btn-warning btn-block disabled'))?>
                                        <input class="btn btn-success btn-block" type="submit" value="Sign In">
                                    </div>
                                </fieldset>
                                </form>
                            </div>
                            <?endif?>
                        </li>
                        <li class="divider-vertical"></li>
                        <li>
                            <?=anchor("doc","Doc")?>
                        </li>
                        <li class="divider-vertical"></li>
                        <li>
                            <?=anchor("faq","FAQ")?>
                        </li>
                        <li class="divider-vertical"></li>
                        <li>
                            <?=anchor("xem/shows","Shows")?>
                        </li>
                        <li style="padding-right: 5px;">
                            <?=form_open("xem/addShow",array('class'=>'navbar-search','id'=>'addShowForm'))?>
                                <select id="elementSelector" style="margin-bottom: 0;">
                                    <?if($logedIn):?>
                                        <option value="0">Add New Show</option>
                                    <?endif?>
                                    <option value="choose" <?if(!isset($fullelement)){echo 'selected="selected"';} ?>>Choose a Show</option>
                                    <?foreach($shows as $row):?>
                                        <option value="<?=$row->id?>"  <?if(isset($fullelement)){if($fullelement->id==$row->id) echo 'selected="selected"';} ?>>
                                            <?php echo strlen($row->main_name) > 40 ? substr($row->main_name,0,40)."..." : $row->main_name; ?>
                                        </option>
                                    <?endforeach?>
                                </select>
                                <div id="newStuff" class="hide form-inline">
                                    <input id="newElementName" type="text" name="main_name" class="search-query" <?=$disabled?>/>
                                    <div class="btn-group">
                                        <input type="button" value="Cancel" id="cancelNewElement" class="btn btn-danger" <?=$disabled?>/>
                                        <input type="submit" value="Add" id="addNewElement" class="btn btn-primary" <?=$disabled?>/>
                                    </div>
                                </div>
                            </form>
                        </li>
                        <li class="divider-vertical"></li>
                    </ul>
                    <ul class="nav pull-right">
                        <li>
                            <?=form_open("search/",array('method'=>'get','class'=>'navbar-search','id'=>'searchForm'))?>
                                <input class="search-query" id="search" type="text" name="q" <?if(isset($searchQeuery)){echo 'value="'.$searchQeuery.'"';}?>/>
                                <input id="search-submit" type="submit" value="Search">
                            </form>
                        </li>
                    </ul>
                </div><!-- /nav-collapse -->

            </div><!-- /container -->
        </div><!-- /navbar-inner -->
    </div><!-- /navbar -->

    <div class="alert alert-error hide">
        <button class="close" data-dismiss="alert">&times;</button>
        <strong>Warning!</strong> Development : This site could break at any moment, be aware!
    </div>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">