<!DOCTYPE html>
<!--

 ,,
*MM                     `7MMF'            db      `7MM"""Yb.
 MM                       MM             ;MM:       MM    `Yb.  __,
 MM,dMMb.`7M'   `MF'      MM            ,V^MM.      MM     `Mb `7MM  pd""b.   pd""b.  M******A'
 MM    `Mb VA   ,V        MM           ,M  `MM      MM      MM   MM (O)  `8b (O)  `8b Y     A'
 MM     M8  VA ,V         MM      ,    AbmmmqMA     MM     ,MP   MM      ,89      ,89      A'
 MM.   ,M9   VVV          MM     ,M   A'     VML    MM    ,dP'   MM    ""Yb.    ""Yb.     A'
 P^YbmdP'    ,V         .JMMmmmmMMM .AMA.   .AMMA..JMMmmmdP'   .JMML.     88       88    A'
            ,V                                                      (O)  .M' (O)  .M'   A'
         OOb"                                                        bmmmd'   bmmmd'   A'
-->
<html>
    <head>
        <meta charset="utf-8">
        <title><?=$title?> | Xem</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--[if lt IE 9]>
            <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

<!--
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="./images/apple-touch-icon-144x144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="./images/apple-touch-icon-114x114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="./images/apple-touch-icon-72x72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="./images/apple-touch-icon-57x57-precomposed.png">
-->

        <? echo link_tag('css/bootstrap.css', 'stylesheet', 'text/css'); ?>
        <? echo link_tag('css/smoothness/jquery-ui-1.8.16.custom.css', 'stylesheet', 'text/css'); ?>
        <? echo link_tag('css/jquery.qtip.min.css', 'stylesheet', 'text/css'); ?>
        <? echo link_tag('css/main.css', 'stylesheet', 'text/css'); ?>

        <script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/html5boilerplate.consolewrapper.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery.transition.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery.dataset.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery.jeditable.mini.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery.autoGrowInput.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery.qtip.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/raphael-min.js"></script>

        <!-- own stuff -->
        <script type="text/javascript" src="<?php echo base_url();?>js/main.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/xem.logo.js"></script>
        <!-- share this -->
        <script type="text/javascript">var switchTo5x=true;</script>
        <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
        <script type="text/javascript">stLight.options({publisher:'15e087b2-55e2-44be-bda6-54e3ee00d766'});</script>

    </head>
<body class="<?if(isset($fullelement)){if($fullelement->isDraft) echo 'draft';} ?>">
<div id="page">
    <!-- <a href="http://github.com/you"><img style="position: absolute; top: 0; left: 0; border: 0;" src="https://a248.e.akamai.net/assets.github.com/img/c641758e06304bc53ae7f633269018169e7e5851/687474703a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f6c6566745f77686974655f6666666666662e706e67" alt="Fork me on GitHub"></a> -->
	<?if(isset($fullelement)):?>
	<?if($fullelement->isDraft):?>
    <div class="draft_background" id="draft_left">DRAFT</div>
    <div class="draft_background" id="draft_right">DRAFT</div>
    <?endif?>
    <?endif?>

    <div class="navbar">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <a class="brand" href="/" title="XEM"><img alt="XEM" src="/images/xem_logo.png" height="26" /></a>

                <div class="nav-collapse">
                    <ul class="nav">
                        <li class="divider-vertical"></li>
                        <li class="dropdown">
                            <?if($logedIn):?>
                            <a class="dropdown-toggle" href="#" data-toggle="dropdown"><? echo $user_nick ?> <strong class="caret"></strong></a>
                            <ul class="dropdown-menu">
                                <li><?=anchor("user","<i class='icon-user'></i> Profile (Level $user_lvl)")?></li>
                                <li><?=anchor("user/logout","<i class='icon-off'></i> Log Out")?></li>
                            <?if(grantAcces(4)):?>
                                <li class="divider"></li>
                                <li><?=anchor("xem/adminShows","<i class='icon-fire'></i> Admin View")?></li>
                            <?endif?>
                            </ul>
                            <?else:?>
                            <a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <strong class="caret"></strong></a>
                            <div class="dropdown-menu" style="padding: 15px;">
                                <?=form_open("user/login/",array('class'=>'form-vertical'))?>
                                <fieldset>
                                    <legend>Sign In</legend>
                                    <div class="control-group">
                                        <label class="control-label" for="user">User:</label>
                                        <div class="controls">
                                            <input type="text" name="user" class="input-large">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="user">Password:</label>
                                        <div class="controls">
                                            <input type="password" name="pw" class="input-large">
                                        </div>
                                    </div>
                                    <div class="pull-left">
                                        <div style="padding-top: 6px;">
                                            <?=anchor('user/register','Need an account?')?>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <input class="btn btn-large" type="submit" value="Sign In">
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
                            <?=anchor("faq","Faq")?>
                        </li>
                        <li class="divider-vertical"></li>
                        <li>
                            <?=anchor("xem/shows","Shows")?>
                        </li>
                        <li>
                            <?=form_open("xem/addShow",array('class'=>'navbar-search','id'=>'addShowForm'))?>
                                <select id="elementSelector">
                                    <?if($logedIn):?>
                                        <option value="0">Add New Show</option>
                                    <?endif?>
                                    <option value="choose" <?if(!isset($fullelement)){echo 'selected="selected"';} ?>>Choose a Show</option>
                                    <?foreach($shows as $row):?>
                                        <option value="<?=$row->id?>"  <?if(isset($fullelement)){if($fullelement->id==$row->id) echo 'selected="selected"';} ?>><?=$row->main_name?></option>
                                    <?endforeach?>
                                </select>
                                <div id="newStuff" class="hide">
                                    <input id="newElementName" name="main_name" class="search-query" <?=$disabled?>/>
                                    <div class="btn-group pull-right">
                                        <input type="button" value="Cancel" id="cancelNewElement" class="btn btn-danger btn-mini" <?=$disabled?>/>
                                        <input type="submit" value="Add" id="addNewElement" class="btn btn-primary btn-mini" <?=$disabled?>/>
                                    </div>
                                </div>
                            </form>
                        </li>
                        <li class="divider-vertical"></li>
                    </ul>
                    <ul class="nav pull-right">
                        <li>
                            <?=form_open("search/",array('method'=>'get','class'=>'navbar-search','id'=>'searchForm'))?>
                                <input class="search-query" id="search" name="q" <?if(isset($searchQeuery)){echo 'value="'.$searchQeuery.'"';}?>/>
                                <input id="search-submit" type="submit" value="Search">
                            </form>
                        </li>
                    </ul>
                </div><!-- /nav-collapse -->

            </div><!-- /container -->
        </div><!-- /navbar-inner -->
    </div><!-- /navbar -->

		<div id="header" style="display: none;">
			<div id="logo">
			</div>
		</div>
		<!--<h1 style="color:red;">Dev:This site make break any minute now... be aware</h1>-->

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">