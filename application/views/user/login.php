<div class="page-header">
    <h1>Login</h1>
</div>

<?=form_open("user/login/".$uri2, array('class'=>'form-horizontal'))?>
    <?if(isset($login_unsuccessfull)): ?>
    <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> <?=$reason ?>
    </div>
    <? endif;?>

    <div class="control-group">
        <label class="control-label" for="email">Username</label>
        <div class="controls">
            <input class="span2" name="user" id="user" type="text">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="pw">Password</label>
        <div class="controls">
            <input class="span2" type="password" id="pw" name="pw">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Login</button>
        <button type="button" class="btn">Cancel</button>
    </div>

    <p>Need an account <?=anchor('user/register','Register!')?></p>

</form>