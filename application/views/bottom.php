
                <?if(isset($cmse))echo $cmse; ?>

            </div><!-- /container-fluid -->
        </div><!-- /row-fluid -->
    </div><!-- /span12 -->

    <br>
    <footer class="footer">
        <p class="pull-right"><a href="#"><img alt="top" src="<?php echo base_url();?>images/top.gif"></a></p>
        <div><strong>{elapsed_time}</strong>s says <?=anchor('http://codeigniter.com/', img( array('src'=>'images/social-codeigniter.png', 'title'=>'CodeIgniter', 'class'=>'social') ));?></div>
        <div><?=anchor('https://github.com/thezoggy/xem', img( array('src'=>'images/social-github.png', 'title'=>'GitHub', 'class'=>'social') ));?></div>
        <div><?=anchor('irc://irc.freenode.net/#xem', img( array('src'=>'images/social-contact.png', 'title'=>'#xem', 'class'=>'social') ));?></div>
        <div><?=anchor('https://www.paypal.me/zoggy/3', img( array('src'=>'images/btn_donate_SM.gif', 'title'=>'Donate a beer/redbull to the cause?', 'class'=>'social') ));?></div>
    </footer>

</div><!-- /page -->

<div id="overlay"></div>
</body>
</html>