<!DOCTYPE html>
<html lang="en">
<head>
	<!-- meta tags -->
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	<!-- favicon and title -->
	<link rel="icon" href="http://thexem.de/images/entitys/icon_master.png" type="image/pn">
	<title><?=$title?> | Xem</title>
	<!-- debug / console wrapper -->
	<script  src="/js/html5boilerplate.consolewrapper.js"></script>
	<!-- normalize.css -->
	<?=link_tag("css/normalize.css")?>
	<!-- jquery -->
	<?=link_tag("js/jquery/css/smoothness/jquery-ui-1.8.16.custom.css")?>
	<script  src="/js/jquery/js/jquery-1.7.min.js"></script>
	<script  src="/js/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
	<!-- jquery plugins -->
	<script  src="/js/jquery.transition.js"></script>
	<script  src="/js/jquery.dataset.js"></script>
	<script  src="/js/jquery.jeditable.mini.js"></script>
	<script  src="/js/jquery.autoGrowInput.js"></script>
	<!-- qtip - i know its also a jquery plugin :P-->
	<?=link_tag("js/qtip2/jquery.qtip.min.css")?>
	<script  src="/js/qtip2/jquery.qtip.min.js"></script>
	<!-- raphael -->
	<script  src="/js/raphael-min.js"></script>
	<!-- own stuff -->
	<?=link_tag("css/main.css")?>
	<script  src="/js/main.js"></script>
	<script  src="/js/xem.logo.js"></script>
	<!-- share this -->
	<script type="text/javascript">var switchTo5x=true;</script>
	<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
	<script type="text/javascript">stLight.options({publisher:'15e087b2-55e2-44be-bda6-54e3ee00d766'});</script>
</head>
<body>
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
				<?=form_open("search/",array('method'=>'get'))?>
					<input id="search" name="q"/>
				</form>
			</div>
			<div id="logo">
			</div>
		</div>
		<!--<h1 style="color:red;">Dev:This site make break any minute now... be aware</h1>-->
		<div id="content">