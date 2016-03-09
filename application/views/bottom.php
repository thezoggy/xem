
                <?if(isset($cmse))echo $cmse; ?>

            </div><!-- /container-fluid -->
        </div><!-- /row-fluid -->
    </div><!-- /span12 -->

    <br>
    <footer class="footer">
        <p class="pull-right"><a href="#"><img alt="top" src="<?php echo base_url();?>images/top.gif"></a></p>
        <div><strong>{elapsed_time}</strong>s says <?=anchor('http://codeigniter.com/', img( array('src'=>'images/social-codeigniter.png', 'title'=>'CodeIgniter', 'class'=>'social') ));?></div>
        <div><?=anchor('https://github.com/thezoggy/xem', img( array('src'=>'images/social-github.png', 'title'=>'GitHub', 'class'=>'social') ));?></div>
        <div><?=anchor('imprint', img( array('src'=>'images/social-imprint.png', 'title'=>'Imprint', 'class'=>'social') ));?></div>
        <div><?=anchor('http://validator.w3.org/check?uri='.curPageURL(), img( array('src'=>'images/html5_bw_badge22.png', 'title'=>'Powered by HTML5', 'class'=>'social') ));?></div>
        <div><?=anchor('https://www.paypal.me/zoggy/3', img( array('src'=>'images/btn_donate_SM.gif', 'title'=>'Donate a beer/redbull to the cause?', 'class'=>'social') ));?></div>
        <!-- olddonate
        <div>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_donations">
                <input type="hidden" name="business" value="lad1337@gmail.com">
                <input type="hidden" name="lc" value="US">
                <input type="hidden" name="item_name" value="lad1337">
                <input type="hidden" name="no_note" value="0">
                <input type="hidden" name="currency_code" value="EUR">
                <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
                <input type="image" src="<?php echo base_url();?>images/btn_donate_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" class="social">
            </form>
        </div>
        <!-- /olddonate -->
    </footer>

</div><!-- /page -->

<div id="overlay"></div>
</body>
</html>