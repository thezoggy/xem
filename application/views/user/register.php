<div class="page-header">
    <h1>Register</h1>
</div>
<?if(!REGISTRATION_OPEN):?>
    <div class="alert alert-block alert-error">
        <h4>Registration closed!!</h4>
        Sorry we have registrations closed at the moment, join #xem on irc.freenode.net for further help.
    </div>
<?else:?>
<?=form_open("user/register/", array('class' => 'form-horizontal'))?>
<?if(isset($register_unsuccessfull)): ?><div class="alert alert-error"><? echo validation_errors(); ?><?=$reason ?></div><? endif;?>
    <div class="control-group">
        <label class="control-label" for="user">Username</label>
        <div class="controls">
            <input class="input-large" name="user" type="text" value="<?=set_value('user')?>" id="user">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="email">E-mail</label>
        <div class="controls">
            <input class="input-large" name="email" type="text" value="<?=set_value('email')?>" id="email">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="pw">Password</label>
        <div class="controls">
            <input class="input-large" type="password" name="pw" id="pw">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="pw_check">Password (Confirm)</label>
        <div class="controls">
            <input class="input-large" type="password" name="pw_check" id="pw_check">
        </div>
    </div>
    <div class="control-group" style="padding-left: 125px;">
        <?php echo $this->recaptcha->getWidget(); ?>
        <?php echo $this->recaptcha->getScriptTag(); ?>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-success">Register</button>
        <button type="button" class="btn">Cancel</button>
    </div>
</form>
<?endif;?>
