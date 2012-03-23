
<h1>Register</h1>
<?=form_open("user/register/")?>
<ul class="no">
<?if(isset($register_unsuccessfull)): ?><li><strong style="color:red;"><?=$reson ?></strong></li><? endif;?>
    <li><label>User</label><input name="user" value="<?=set_value('user')?>"/></li>
    <li><label>Email</label><input name="email" value="<?=set_value('email')?>"/></li>
    <li><label>Password</label><input name="pw" type="password"/></li>
    <li><label>Pw Again</label><input name="pw_check" type="password"/></li>
    <li>
        <?=$recaptcha?>
    </li>
</ul>
<input type="submit" value="Register">
</form>

