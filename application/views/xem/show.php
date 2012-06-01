
<?if(isset($fullelement)):?>
<?if($fullelement->status > 0 || grantAcces(4)):?>
<div id="element" data-id="<?=$fullelement->id?>">
		<h1><?=$fullelement->main_name?></h1>
		<br class="clear"/>
		<div id="alternativeNamesContainer">
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

    		<?if($editRight):?>
    		<div id="newAlternativeName">
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
    		</div>
    		<?endif?>
		</div>

		<?if($editRight || grantAcces(1)):?>
		<div id="toolbox">
		  <strong>Toolbox</strong>
		  <ul>
                <?if(!$fullelement->isDraft):?>
                <li><label>Draft (<?=$fullelement->draftChangesCount()?> ahead)</label><input type="button" value="Go To Draft" onClick="window.location = '/xem/draft/<?=$fullelement->id?>'"/></li>
                <?else:?>
                <li><label>Public (<?=$fullelement->draftChangesCount()?> behind)</label><input type="button" value="Go To Public" onClick="window.location = '/xem/show/<?=$fullelement->parent?>'"/></li>
                <?if($fullelement->status<4):?>
                <li><label>Public request</label><input type="button" value="Request&hellip;" onClick="requestPublic()"/></li>
                <?else:?>
                <li><label>Public request was send&hellip;</label></li>
                <?endif;?>
                <?endif?>
                <?if($editRight):?>
                <li><label>Save entity order</label><input type="button" value="Save" onClick="saveEntityOrder()"/></li>
                <li><label title="If QuickConnet is ON a direct connection will be made as soon two episodes are marked.">QuickConnet</label><input type="button" value="OFF" onclick="if(quickConnet){quickConnet = false; $(this).val('OFF')}else{quickConnet = true; $(this).val('ON')}"/></li>
                <?endif?>
                <?if(grantAcces(3)):?>
                <li>
                    <?=form_open("xem/setLockLevel",array('id'=>'deleteShowForm'))?>
                    <?=form_hidden("element_id",$fullelement->id)?>

                    <label>Lock Level</label><select name="lvl">
                        <?for($i = 1; $i <= 3; $i++):?>
                        <option value="<?=$i?>" <?if($fullelement->status == $i){ echo 'selected="selected"';} ?>><?=$i?></option>
                        <?endfor?>
                        </select>
                        <input type="submit" value="Set"/>
                    </form>
                </li>

                <?if(!$fullelement->isDraft):?>
                <li>
                    <?=form_open("xem/clearCache",array('id'=>'deleteShowForm'))?>
                        <?=form_hidden("element_id",$fullelement->id)?>
                        <label>Clear cache (<?=$fullelement->cacheSize?>)</label><input type="submit" value="Clear"/>
                    </form>
                </li>
                <?endif?>
                <?endif?>

              <?if(grantAcces(4)):?>
              <?if($fullelement->status > 0):?>
                <?if(!$fullelement->isDraft):?>
                <li><label>Delete</label><input type="button" onClick="deleteMe()" value="Delete This Show&hellip;"/></li>
                <?else:?>
                <li><label>Make draft public</label><input type="button" onClick="window.location = '/xem/makePublic/<?=$fullelement->id?>'" value="Make Public"/></li>
                <li><label>Delete</label><input type="button" onClick="deleteMe()" value="Delete This Draft&hellip;"/></li>
                <?endif?>
              <?else:?>
              <li>
                 <?=form_open("xem/unDeleteShow")?>
                    <?=form_hidden("element_id",$fullelement->id)?>
                    <input type="submit" value="UnDelete This Show">
                  </form>
              </li>
              <?endif?>
              <?endif?>

              <?if(!$fullelement->isDraft):?>
    		  <li><label>This <strong>show</strong> has a lvl of <strong><?=$fullelement->status?></strong></label></li>
              <?else:?>
              <li><label>This <strong>draft</strong> has a lvl of <strong><?=$fullelement->status?></strong></label></li>
              <?endif?>
              <li><label><?=anchor('xem/changelog/'.$fullelement->id,'Changelog')?></label></li>
		  </ul>
		</div>
		<?endif;?>
        <br class="clear-keep-height"/>
		<!-- this is a fix for the windows firefox svg 1px top offset problem. a div,p, h3(wtf?) or any other block stuff didnt work -->
		<!-- ok is not a fix .... this can create and remove the effect -->
		<!--<h2 style="height: 45px;">&nbsp;</h2>-->
		<div class="clear-keep-height">
			<ul id="sortable">
				<?foreach($fullelement->sortedEntitys() as $curLocation):$absolute_number = 1;?>
				<? include('editShow_singleEntity.php');?>
				<?endforeach?>
				<li class="clear"></li>
			</ul>
			<div class="clear"></div>
		</div>
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

<?endif?>
<?endif?>
