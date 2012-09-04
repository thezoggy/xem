<div class="page-header">
    <h1>Register</h1>
</div>

<?=form_open("user/register/", array('class'=>'form-horizontal'))?>

    <?if(isset($register_unsuccessfull)): ?>
    <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> <?=$reason ?>
    </div>
    <? endif;?>

    <div class="control-group">
        <label class="control-label" for="email">Username</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-user"></i></span>
                <input class="span12" name="user" id="user" type="text" value="<?=set_value('user')?>">
            </div>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="email">Email</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-envelope"></i></span>
                <input class="span12" name="email" id="email" type="text" value="<?=set_value('email')?>">
            </div>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="pw">Password</label>
        <div class="controls">
            <input class="span2" type="password" id="pw" name="pw" placeholder="Password">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="pw_check">Verify Password</label>
        <div class="controls">
            <input class="span2" type="password" id="pw_check" name="pw_check" placeholder="Verify Password">
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <?=$recaptcha?>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Register</button>
    </div>

</form>
