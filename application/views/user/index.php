
<h1><?=$user_nick?></h1>
<h3>user config settings stuff</h3>


<?=form_open("user/changePw",array('id'=>'changePw'))?>
	<ul>
		<li><label style="width: 160px;" >Current Password</label><input name="old_pw" type="password"/></li>
		<li><label style="width: 160px;" >New Password</label><input name="new_pw" type="password"/></li>
		<li><label style="width: 160px;" >New Password Again</label><input name="new_pw_check" type="password"/></li>
	</ul>
	<input type="submit" value="Save New Password"/><br/>
	<span style="font-size:80%;">Note: Upon changing your password, you must log back in</span>
</form>


<p>
you are level <?=$user_lvl?>
<p>