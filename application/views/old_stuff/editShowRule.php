<table border=1>
	<tr>
		<th>id</th>
		<th>origin</th>
		<th>destination</th>
		<th>show</th>
		<th>action</th>
	</tr>
	<?foreach($rule_maps as $ruleMap):?>
	<tr class="<?if(isset($rule_map))if($rule_map->id == $ruleMap->id)echo 'inEdit';?>">
		<td><?=$ruleMap->id?></td>
		<td><?=pretty_locations($ruleMap->originNames())?></td>
		<td><?=pretty_locations($ruleMap->destinationNames())?></td>
		<td><?=anchor("xem/editElement/".$ruleMap->element->id,$ruleMap->element->main_name)?></td>
		<td><?=anchor("xem/editShowRule/".$ruleMap->id,"Edit")?></td>
	</tr>
	<?endforeach?>
</table>
<p><?=anchor("xem/addShowRule/","add new map")?></p>

<?if(isset($rule_map)):?>
<h3>Map <?=$rule_map->id?>: <?=$rule_map->element->main_name?></h3>
<?=form_open("xem/editShowRuleProcess")?>
	<fieldset>
		<?=form_hidden("rule_map_id",$rule_map->id)?>
		<ul>
			<li>
				<label for="origin_id">Origin:</label>
				<select name="origin_id[]" size="3" multiple>
					<? foreach($rule_map->locations as $location):?>
					<option value="<?=$location->id?>"<?if($rule_map->isLocationAOrigin($location->id))echo " selected";?>><?=$location->name?></option>
					<? endforeach?>
				</select>
			</li>
			<li>
				<label for="destination_id">Destination:</label>
				<select name="destination_id[]" size="3" multiple>
					<? foreach($rule_map->locations as $location):?>
					<option value="<?=$location->id?>"<?if($rule_map->isLocationADestination($location->id))echo " selected";?>><?=$location->name?></option>
					<? endforeach?>
				</select>
			</li>
			<li>
				<input type="submit" value="set new range for this map" <?=$disabeld?>/>
			</li>
		</ul>
	</fieldset>
</form>



<?foreach($rule_map->offsetrules as $offset_rule):?>
<div class="rule">
	<?=form_open("xem/editShowRuleProcess")?>
		<fieldset>
			<?=form_hidden("rule_map_id",$rule_map->id)?>
			<?=form_hidden("offset_rule_id",$offset_rule->id)?>
			<ul>
				<li>
					<label for="season_from">Season from</label>
					<input name="season_from" value="<?if($offset_rule->season_from == -1)echo 'start';else echo $offset_rule->season_from?>" <?=$disabeld?>/>
				</li>
				<li>
					<label for="season_to">Season to</label>
					<input name="season_to" value="<?if($offset_rule->season_to == -1)echo 'end';else echo $offset_rule->season_to?>" <?=$disabeld?>/>
				</li>
				<li>
					<label for="season_offset">Season Offset</label>
					<input name="season_offset" value="<?=$offset_rule->season_offset?>" <?=$disabeld?>/>
				</li>
				<li>
					<label for="episode_from">Episdode from</label>
					<input name="episode_from" value="<?if($offset_rule->episode_from == -1)echo 'start';else echo $offset_rule->episode_from?>" <?=$disabeld?>/>
				</li>
				<li>
					<label for="episode_to">Episdode to</label>
					<input name="episode_to" value="<?if($offset_rule->episode_to == -1)echo 'end';else echo $offset_rule->episode_to?>" <?=$disabeld?>/>
				</li>
				<li>
					<label for="episode_offset">Episdode Offset</label>
					<input name="episode_offset" value="<?=$offset_rule->episode_offset?>" <?=$disabeld?>/>
				</li>
				<li>
					<label for="absolute_ep_offset">Absolute Ep Offset</label>
					<input name="absolute_ep_offset" value="<?=$offset_rule->absolute_episode_offset?>" <?=$disabeld?>/>
				</li>
				<li <?if(!$offset_rule->id)echo 'style="visibility:hidden;"';?>>
					<label for="absolute_ep_offset">Delete this rule</label>
					<input type="checkbox" name="delete"  <?=$disabeld?>/>
				</li>
				<li>
					<input type="submit" value="<?if($offset_rule->id)echo 'save this';else echo 'add new'?>" <?=$disabeld?>/>
				</li>
			</ul>	
			<p>Offset rule id: <?=$offset_rule->id?></p>
		</fieldset>
	</form>
	
</div>
<?endforeach?>

<?endif?>
