<div class="page-header">
    <h1>Login</h1>
</div>

<?=form_open("user/login/".$uri2)?>
    <ul class="no">
        <?if(isset($login_unsuccessfull)): ?><li><strong style="color:red;"><?=$reson ?></strong></li><? endif;?>
        <li><label>User</label><input name="user"/></li>
        <li><label>Password</label><input name="pw" type="password"/></li>
    </ul>
    <input type="submit" value="Login" class="btn" >
</form>

<p>Need an account <?=anchor('user/register','Register!')?></p>
