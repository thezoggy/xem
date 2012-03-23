
<?if(isset($fullelement)):?>
<div id="element" data-id="<?=$fullelement->id?>">
		<h1><?=$fullelement->main_name?></h1>
		<?if($fullelement->groupedNames()):?>
		<div id="alternativeNames">
			<h2>Alternative Names</h2>
			<table>
				<?foreach($fullelement->groupedNames() as $season=>$names):?>
				<tr>
					<td>
						<?if($season!=-1):?>
						<div>Season <?=$season?> Names</div>
						<?else:?>
						<div>Global Names</div>
						<?endif?>
					</td>
					<td>
						<div>
							<ul class="names">
								<?foreach($names as $curName):?>
								<li data-id="<?=$curName->id?>"><?=$curName->name?></li>
								<?endforeach?>
							</ul>
						</div>
					</td>
				</tr>
				<?endforeach?>
			</table>
		</div>
		<?endif?>
		<div id="newAlternativeName">
			<?if($logedIn):?>
			Season <input id="newNameSeason" style="width:50px;"/> Name <input id="newNameName" /><input type="button" value="Add New Name"/>
			<?endif?>
		</div>
		<!-- this is a fix for the windows firefox svg 1px top offset problem. a div,p, h3(wtf?) or any other block stuff didnt work -->
		<!-- ok is not a fix .... this can create and remove the effect -->
		<!--<h2 style="height: 45px;">&nbsp;</h2>-->
		<div>
			<ul id="sortable">
				<?foreach($fullelement->sortedEntitys() as $curLocation):$absolute_number = 1;?>
				<? include('editShow_singleEntity.php');?>
				<?endforeach?>
				<li class="clear"></li>
			</ul>
			<div class="clear"></div>
		</div>
		<?if($logedIn):?>
		<p>
			<input type="button" value="Save entity order" onClick="saveEntityOrder()"/><br/>
			<input type="button" value="show history" id="history"/>
		</p>
		<div id="historyContainer"></div>
		<?endif?>
</div>
<!-- show script and functions -->
<script type="text/javascript">
var logedIn = <?=json_encode($logedIn)?>;
var abstractConObjs = <?=$fullelement->getJSONDirectrules()?>;
var passthruConObjs = <?=$fullelement->getJSONPassthrus()?>;
</script>
<script src="/js/shows.js"></script>


<?endif?>
