
<?if(isset($fullelement)):?>
<?if($fullelement->status > 0):?>
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
						<div>Season <?=$season?></div>
						<?else:?>
						<div>Alias</div>
						<?endif?>
					</td>
					<td>
						<div>
							<ul class="names">
								<?foreach($names as $curName):?>
								<li class="name">
                                    <?=img(array('src'=>'images/flags/'.$curName->language.'.png','data-id'=>$curName->id,'data-lang'=>$curName->language,'id'=>'flag_'.$curName->id,'width'=>17))?>
                                    <span class="name" data-id="<?=$curName->id?>"><?=$curName->name?></span>
                                    <div class="clear"></div>
                                </li>
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
			<?if($editRight):?>
			<?=form_open("xem/newAlternativeName")?>
					<?=form_hidden("element_id",$fullelement->id)?>

                    <label style="min-width:45px;">Season</label>
                    <input id="newNameSeason" style="width:50px;" name="season" placeholder="All"/>

                    <select name="language">
                        <?foreach($languages->result() as $curLang):?>
                        <option value="<?=$curLang->id?>" <?if($curLang->id == 'us'){ echo 'selected="selected"';} ?>><?=$curLang->name?></option>
                        <?endforeach?>
                    </select>
					<input id="newNameName" name="name" placeholder="Name"/>
					<input type="submit" value="Add New Name"/>
			</form>
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
		<?if($editRight):?>
		<p>
			<input type="button" value="Save entity order" onClick="saveEntityOrder()"/><br/>

            <ul>
                <li><strong><?=$fullelement->main_name?></strong> has a lvl of <strong><?=$fullelement->status?></strong></li>
                <?if(grantAcces(4)):?>
                <li>
        			<?=form_open("xem/deleteShow",array('id'=>'deleteShowForm'))?>
        				<?=form_hidden("element_id",$fullelement->id)?>
        			</form>
        			Delete <input type="button" onClick="deleteMe()" value="Delete This Show"/>
                </li>
    			<?endif?>
                <?if(grantAcces(3)):?>
                <li>
                    <?=form_open("xem/setLockLevel",array('id'=>'deleteShowForm'))?>
                        <?=form_hidden("element_id",$fullelement->id)?>

                        Lock Level
                        <select name="lvl">
                            <?for($i = 1; $i <= 3; $i++):?>
                            <option value="<?=$i?>" <?if($fullelement->status == $i){ echo 'selected="selected"';} ?>><?=$i?></option>
                            <?endfor?>
                        </select>
                        <input type="submit" value="Set"/>
                    </form>
                </li>
                <li>
              	    <?=form_open("xem/clearCache",array('id'=>'deleteShowForm'))?>
                        <?=form_hidden("element_id",$fullelement->id)?>

                        Clear all cached data (<?=$fullelement->cacheSize?>)
                        <input type="submit" value="clear"/>
                    </form>
                </li>
                <?endif?>
            </ul>
		</p>
		<?endif?>

        <p><?=anchor('xem/changelog/'.$fullelement->id,'Changelog')?></p>
</div>
<!-- show script and functions -->
<script type="text/javascript">
var logedIn = <?=json_encode($logedIn)?>;
var editRight = <?=json_encode($editRight)?>;
var abstractConObjs = <?=$fullelement->getJSONDirectrules()?>;
var passthruConObjs = <?=$fullelement->getJSONPassthrus()?>;
var languages = <?=$languagesJS?>;
</script>
<script src="/js/shows.js"></script>
<?else:?>
<h2><?=$fullelement->main_name?></h2>
<p>This show was deleted!!</p>
<p><?=anchor('xem/changelog/'.$fullelement->id,'Changelog')?></p>
<?if(grantAcces(4)):?>
<?=form_open("xem/unDeleteShow")?>
	<?=form_hidden("element_id",$fullelement->id)?>
	<input type="submit" value="UnDelete This Show">
</form>
<?endif?>
<?endif?>
<?endif?>
