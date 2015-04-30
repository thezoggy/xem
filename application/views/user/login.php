<div class="page-header">
    <h1>Login</h1>
</div>

<?=form_open("user/login/".$uri2, array('class' => 'form-horizontal'))?>
    <?if(isset($login_unsuccessfull)): ?><div class="alert alert-error"><?=$reason ?></div><? endif;?>
    <div class="control-group">
        <label class="control-label" for="user">User</label>
        <div class="controls">
            <input class="input-large" name="user" type="text" value="<?=set_value('user')?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="pw">Password</label>
        <div class="controls">
            <input class="input-large" type="password" name="pw">
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Login</button>
        <?=anchor('user/register','Register!', array('class' => 'btn btn-warning'))?>
    </div>

</form>
