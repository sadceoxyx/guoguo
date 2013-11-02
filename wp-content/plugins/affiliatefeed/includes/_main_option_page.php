<?php if(is_admin()){ ?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2>Affiliate Feed Administration</h2>
        <?php print $msg ?>

        <form action ="" method="post" id="af_admin_option_form">
                <input id="fetch" type="button" name="fetch" value="fetch"/>
                <input id="delete" type="button" name="delete" value="delete"/>
                <input id="add" type="button" name="add" value="add"/>
                <br>
                <hr>
                <h3>please add or edit your affiliate network</h3>
                <ul id="textvalue">
                    <li class="af_item">
                        <input id="check" type="checkbox" name="check" value="0"/>
                        <input id ="name" type ="textbox" size="20" name="name0"/>
                        <input id="url" type ="textbox" size="80" name="url0"/>
                    </li>
                </ul>
                <hr>
                <input class ="button-primary"id="save" type="submit" name="save" value="save"/>
                <?php //wp_nonce_field('af_admin_options', 'af_admin_option_nonce'); ?>
        </form>
    </div>
<?php } ?>