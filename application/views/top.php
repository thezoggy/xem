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

        <? echo link_tag('css/normalize.css', 'stylesheet', 'text/css'); ?>
        <? echo link_tag('css/smoothness/jquery-ui-1.8.16.custom.css', 'stylesheet', 'text/css'); ?>
        <? echo link_tag('css/jquery.qtip.min.css', 'stylesheet', 'text/css'); ?>
        <? echo link_tag('css/main.css', 'stylesheet', 'text/css'); ?>

        <script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/html5boilerplate.consolewrapper.js"></script>
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
<div id="everything">
    <!-- <a href="http://github.com/you"><img style="position: absolute; top: 0; left: 0; border: 0;" src="https://a248.e.akamai.net/assets.github.com/img/c641758e06304bc53ae7f633269018169e7e5851/687474703a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f6c6566745f77686974655f6666666666662e706e67" alt="Fork me on GitHub"></a> -->
	<div id="page">
		<div id="header">
			<ul>
				<li><?=anchor("/","Home")?></li>
				<li>
					<?if($logedIn):?>
					<?=anchor("user/logout/".$uri,"Profile",'id="logout" title="'.$user_nick.' lvl '.$user_lvl.'. Hold to logout"')?>
					<?else:?>
					<?=anchor("user/login/".$uri,"Login",'title="and registration"')?>
					<?endif?>
				</li>
				<li>
					<?=anchor("doc","Doc")?>
				</li>
				<li>
					<?=anchor("faq","Faq")?>
				</li>
				<li>
					<?=anchor("xem/shows","Shows")?>
				</li>
			</ul>
			<div id="elementSelectorContainer" class="normal">
				<?=form_open("xem/addShow",array('id'=>'addShowForm'))?>
				    <select id="elementSelector">
				        <?if($logedIn):?><option value="0">Add New Show</option><?endif?>
				        <option value="choose" <?if(!isset($fullelement)){echo 'selected="selected"';} ?>>Choose a Show</option>
						<?foreach($shows as $row):?>
				        <option value="<?=$row->id?>"  <?if(isset($fullelement)){if($fullelement->id==$row->id) echo 'selected="selected"';} ?>><?=$row->main_name?></option>
						<?endforeach?>
				    </select>

				    <input class="newStuff" id="newElementName" name="main_name" <?=$disabled?>/>
				    <input type="button" value="Cancel" id="cancelNewElement" class="newStuff" <?=$disabled?>/>
				    <input type="submit" value="Add" id="addNewElement" class="newStuff" <?=$disabled?>/>
			    </form>
			</div>
			<div id="searchContainer">
				<?=form_open("search/",array('method'=>'get','id'=>'searchForm'))?>
					<input id="search" name="q"/>
				</form>
			</div>
			<div id="logo">
			</div>
		</div>
		<!--<h1 style="color:red;">Dev:This site make break any minute now... be aware</h1>-->
		<div id="content">