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
<?if(grantAcces(4)):?>
<h3>Get email notifications on</h3>
<?=form_open("user/emailSettings")?>
    <ul>
        <li><label style="width: 160px;display: inline-block;" >New user activation</label><input name="email_new_account" type="checkbox" <?if($config_email_new_account)echo 'checked="checked"'; ?> value="1"/></li>
        <li><label style="width: 160px;display: inline-block;" >New show creation</label><input name="email_new_show" type="checkbox" <?if($config_email_new_show)echo 'checked="checked"'; ?> value="1"/></li>
        <li><label style="width: 160px;display: inline-block;" >Draft public request</label><input name="email_public_request" type="checkbox" <?if($config_email_public_request)echo 'checked="checked"'; ?> value="1"/></li>
    </ul>
    <input type="submit" value="Save Email Settings" class="btn" />
    <br/>
</form>
<?endif;?>