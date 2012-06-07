<div class="page-header">
    <h1><?=$user_nick?> <small>level <?=$user_lvl?></small></h1>
</div>

<h3>Change Password</h3>

<?=form_open("user/changePw",array('id'=>'changePw'))?>
    <ul>
        <li><label style="width: 160px;" >Current Password</label><input name="old_pw" type="password"/></li>
        <li><label style="width: 160px;" >New Password</label><input name="new_pw" type="password"/></li>
        <li><label style="width: 160px;" >New Password Again</label><input name="new_pw_check" type="password"/></li>
    </ul>
    <input type="submit" value="Save New Password" class="btn" />
    <br/>
    <small>Note: Upon changing your password, you must log back in</small>
</form>
