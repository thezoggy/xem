<li style="float:left;" class="entityList" data-locationName="<?=$curLocation->name?>">
<div style="float:left;position:relative;" class="entity <?=$curLocation->name?>" data-id="<?=$curLocation->id?>" data-name="<?=$curLocation->name?>">
	<h3 class="<?=$curLocation->name?>">
		<?=anchor($curLocation->url,imgLazy('images/entitys/icon_'.$curLocation->name.'.png'))?><span><?=$curLocation->name?></span><?=img(array('src'=>'images/info.png','data-entity'=>$curLocation->name,'data-entityID'=>$curLocation->id,'class'=>'conInfo','alt'=>'Info'))?>
	</h3>
	<ul>
		<li>
			<div class="seasonHeaderInfo" id="infoHeader<?=$curLocation->name?>" data-locationName="<?=$curLocation->name?>" data-locationID="<?=$curLocation->id?>">
				<span style="margin-left:5px;"></span>
			</div>
		</li>
		<?foreach($fullelement->seasonForLocationId($curLocation->id) as $curElementLocation):?>
		<?if($curElementLocation->absolute_start > 0)
			$absolute_number = $curElementLocation->absolute_start;
		?>
		<li>
			<div class="seasonHeader" id="seasonHeader_<?=$curElementLocation->season?>_<?=$curLocation->name?>" data-season="<?=$curElementLocation->season?>" data-locationName="<?=$curLocation->name?>" data-locationID="<?=$curLocation->id?>">
				<span><?if($curElementLocation->season == -1) echo 'All';else echo 'S'.zero_pad($curElementLocation->season,2)?>
				<?
				if($curElementLocation->identifier){
					$lastIdentifier = $curElementLocation->identifier;
				}
				if(isset($lastIdentifier))
					echo '| '.anchor($fullelement->getdirectLink($curLocation->id,$curElementLocation->season),$lastIdentifier,'target="_blank"');
				?>
				<?/* if($nameCount = count($fullelement->namesForSeason($curElementLocation->season))) echo '| '.$nameCount.' Name'; if($nameCount>1) echo 's'; */?>
				</span>
			</div>
			<div id="seasonEdit_<?=$curElementLocation->season?>_<?=$curLocation->name?>" class="seasonEdit">
				
				<?=form_open("xem/editSeason",array('id'=>'seasonEditForm_'.$curLocation->name.'_'.$curElementLocation->season))?>
					<?=form_hidden("element_id",$fullelement->id)?>
					<?=form_hidden("location_id",$curLocation->id)?>
					<?=form_hidden("season_id",$curElementLocation->id)?>
					<?=form_hidden("delete")?>
					<ul>
						<li>
							<label>Season</label><input class="season" name="season" value="<?if($curElementLocation->season == -1)echo 'all';else echo $curElementLocation->season?>" <?=$disabled?>/>
						</li>
						<li>
							<label>Size</label><input class="season" name="size" value="<?if($curElementLocation->season_size == -1)echo 'infinite';else echo $curElementLocation->season_size?>" <?=$disabled?>/>
						</li>
						<li>
							<label>Identifier</label><input class="season" name="identifier" value="<?=$curElementLocation->identifier?>" <?=$disabled?>/>
						</li>
						<li>
							<label>Ab. Start</label><input class="season" name="absolute_start" value="<?if($curElementLocation->absolute_start == 0)echo 'auto';else echo $curElementLocation->absolute_start?>" <?=$disabled?>/>
						</li>
						<li>
							<label>Ep. Start</label><input class="season" name="episode_start" value="<?=$curElementLocation->episode_start?>" <?=$disabled?>/>
						</li>
						<li>
							<input class="fullWidthButton" type="submit" value="Save" onClick="saveSeasonValues('<?=$curLocation->name?>',<?=$curElementLocation->season?>)" <?=$disabled?>/>
						</li>
						<li>
							<input class="fullWidthButton" type="button" value="Delete Season" onClick="markSeasonForDeleteAndSubmit('<?=$curLocation->name?>',<?=$curElementLocation->season?>);" <?=$disabled?>/>
						</li>
					</ul>
				
				</form>
			</div>
			<ul>
			<?$size = $curElementLocation->season_size;for($i = 0; $i < $size; $i++):?>
				<li class="episode" id="<?=$curLocation->name?>_<?=$curElementLocation->season?>_<?=$i?>">
					<?if($curElementLocation->season != 0):?><span class="absolute_number"><?echo zero_pad($absolute_number,3);$absolute_number++;?></span><?endif?><span class="episode_number">e<?$curEp = $i+$curElementLocation->episode_start; echo zero_pad($curEp,2)?></span>
				</li>
			<?endfor?>
			</ul>
			
		</li>
		<?endforeach?>
		<li>
			
			<?if($logedIn):?>
			<div class="newSeason">
				<?=form_open("xem/newSeason")?>
					<?=form_hidden("element_id",$fullelement->id)?>
					<?=form_hidden("location_id",$curLocation->id)?>
					<ul>
						<li><label>Season</label><input class="season" name="season" value="<? if(isset($curElementLocation->season)) echo $curElementLocation->season+1?>" <?=$disabled?>/></li>
						<li><label>Size</label><input class="season" name="season_size" value="" <?=$disabled?>/></li>
						<li><label>Identifier</label><input class="season" name="identifier" <?=$disabled?>/></li>
						<li><input class="fullWidthButton" type="submit" value="Add New Season" <?=$disabled?>/></li>
					</ul>
				</form>
				
				<div style="heigth:20px;"></div>
			</div>
			<?else:?>
			<?if(count($fullelement->seasonForLocationId($curLocation->id))==0):?>
				<div class="seasonHeaderFake">No Unique Data</div>
			<?else:?>		
				<div class="newSeason"></div>
			<?endif?>
			<?endif?>
		</li>
	</ul>
</div>
<div class="seasonConnection" id="con_after_<?=$curLocation->name?>" data-locationName="<?=$curLocation->name?>">
	
	<div class="editConIcon" id="editConIcon_<?=$curLocation->name?>">
	</div>
</div>

<div class="clear"></div>
</li>