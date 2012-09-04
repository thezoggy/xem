<div class="page-header">
    <h1><?=$user_nick?> <small>level <?=$user_lvl?></small></h1>
</div>

<h3>Change Password</h3>

<?=form_open("user/changePw",array('id'=>'changePw', 'class'=>'form-horizontal'))?>
    <div class="control-group">
        <label class="control-label" for="old_pw">Current Password</label>
        <div class="controls">
            <input class="span2" type="password" id="old_pw" name="old_pw">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="new_pw">New Password</label>
        <div class="controls">
            <input class="span2" type="password" id="new_pw" name="new_pw">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="new_pw_check">Verify Password</label>
        <div class="controls">
            <input class="span2" type="password" id="new_pw_check" name="new_pw_check">
            <span class="help-block"><b>Note:</b> Upon changing your password, you must log back in!</span>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save New Password</button>
    </div>

</form>

<?if(grantAcces(4)):?>
<h3>E-Mail Notices</h3>

<?=form_open("user/emailSettings",array('class'=>'form-horizontal'))?>
    <div class="control-group controls">
        <label class="checkbox">
            <input name="email_new_account" type="checkbox" <?if($config_email_new_account)echo 'checked="checked"'; ?> value="1">
            New user activation
        </label>
    </div>
    <div class="control-group controls">
        <label class="checkbox">
            <input name="email_new_show" type="checkbox" <?if($config_email_new_show)echo 'checked="checked"'; ?> value="1">
            New show creation
        </label>
    </div>
    <div class="control-group controls">
        <label class="checkbox">
            <input name="email_public_request" type="checkbox" <?if($config_email_public_request)echo 'checked="checked"'; ?> value="1">
            Draft public request
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Notice Settings</button>
    </div>

</form>
<?endif;?>