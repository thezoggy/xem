
                <?if(isset($cmse))echo $cmse; ?>

            </div><!-- /container-fluid -->
        </div><!-- /row-fluid -->
    </div><!-- /span12 -->

    <div id="adds" class="clear-keep-height">
        <br/>
        <script type="text/javascript"><!--
        google_ad_client = "ca-pub-8047294342495073";
        /* xem_unten */
        google_ad_slot = "6026161732";
        google_ad_width = 728;
        google_ad_height = 90;
        //-->
        </script>
        <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>

    </div>

    <footer class="footer">
        <p class="pull-right"><a href="#"><img src="<?php echo base_url();?>images/top.gif"></a></p>
        <div><strong>{elapsed_time}</strong>s says <?=anchor('http://codeigniter.com/', img( array('src'=>'images/social-codeigniter.png', 'title'=>'CodeIgniter', 'class'=>'social') ));?></div>
        <div><?=anchor('https://github.com/lad1337/xem', img( array('src'=>'images/social-github.png', 'title'=>'GitHub', 'class'=>'social') ));?></div>
        <div><?=anchor('imprint', img( array('src'=>'images/social-imprint.png', 'title'=>'Imprint', 'class'=>'social') ));?></div>
        <div><?=anchor('contact', img( array('src'=>'images/social-contact.png', 'title'=>'Contact', 'class'=>'social', 'height'=>'22') ));?></div>
        <div><?=anchor('http://validator.w3.org/check?uri='.curPageURL(), img( array('src'=>'images/html5_bw_badge22.png', 'title'=>'HTML5 Powered with CSS3 / Styling, Graphics, 3D &amp; Effects, and Semantics', 'class'=>'social') ));?></div>
        <div>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_donations">
                <input type="hidden" name="business" value="lad1337@gmail.com">
                <input type="hidden" name="lc" value="US">
                <input type="hidden" name="item_name" value="lad1337">
                <input type="hidden" name="no_note" value="0">
                <input type="hidden" name="currency_code" value="EUR">
                <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
            </form>
        </div>
    </footer>

</div><!-- /page -->

<div id="overlay"></div>
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-22409438-2']);
	_gaq.push(['_gat._anonymizeIp']);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
</body>
</html>