<div class="page-header">
    <h1><?=$user_nick?> <small>level <?=$user_lvl?></small></h1>
</div>

<?if(isset($error)): ?><div class="alert alert-error"><? echo validation_errors(); ?><?=$reason ?></div><? endif;?>

<?=form_open("user/changePw",array('id'=>'changePw', 'class'=>'form-inline'))?>
    <legend>Change Password</legend>
    <ul>
        <li><label style="width: 160px;" >Current Password</label><input name="old_pw" type="password"/></li>
        <li><label style="width: 160px;" >New Password</label><input name="new_pw" type="password"/></li>
        <li><label style="width: 160px;" >New Password Confirm</label><input name="new_pw_check" type="password"/></li>
    </ul>
    <input type="submit" value="Save New Password" class="btn btn-danger" />
    <br/>
    <span class="help-block">Note: Upon changing your password, you must log back in!</span>
</form>

<?if(grantAccess(4)):?>
<br/>
<?=form_open("user/emailSettings",array('class'=>'form-inline'))?>
    <legend>E-mail Notifications:</legend>
    <ul>
        <li><label style="width: 160px;" >New user activation</label><input name="email_new_account" type="checkbox" <?if($config_email_new_account)echo 'checked="checked"'; ?> value="1"/></li>
        <li><label style="width: 160px;" >New show creation</label><input name="email_new_show" type="checkbox" <?if($config_email_new_show)echo 'checked="checked"'; ?> value="1"/></li>
        <li><label style="width: 160px;" >Draft public request</label><input name="email_public_request" type="checkbox" <?if($config_email_public_request)echo 'checked="checked"'; ?> value="1"/></li>
    </ul>
    <input type="submit" value="Save Email Settings" class="btn btn-primary" />
    <br/>
</form>
<?endif;?>
