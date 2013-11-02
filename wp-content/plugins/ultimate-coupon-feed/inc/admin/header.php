<link rel="stylesheet" href="<?php echo $this->tplpath ?>/css/admin.css" type="text/css" media="all" title="" />
<?php if($this->newadmin): ?>
<link rel="stylesheet" href="<?php echo $this->tplpath ?>/css/admin-new.css" type="text/css" media="all" title="" />
<?php endif ?>
<div id="wpomain">
<?php if($_GET['s']!='edit'){ ?>
<div id="instructionsSet">
<div id="hideinst" onclick="hideinst()">Hide Instructions</div>
<div class="instructions" id="instructions">
  <h2>Instructions:</h2>
  (to see a preview of the content that this plugin provides simply visit <a href="http://www.HomeBudgetideas.com" target="blank">HomeBudgetideas.com</a>) <br />
  <br />
  <strong>1) Updates:</strong> By default all feeds are currently active and will start to appear as posts in your admin (all posts page) as soon as new content is available. Every campaign is set to "draft" meaning that it will appear in your admin but you will need to change it from draft to published to make it live. This way you can edit the post before it becomes live. Alternatlivly you can press "Edit" next to any feed and set the type of post to be "Published" so that it automically goes live.<br />
  <br />
  <strong>2) Fetch:</strong> After you first load the plugin you will not see any new content in your admin (all posts page) until new content is available, however you can push the "Fetch" link next to any feed to initialy pull in the last 10 posts for that feed. Otherwise if you do nothing in 24 hours or so you will start to see new content appear in your admin.<br />
  <br />
  <strong>3) Edit:</strong> You can edit any feed. You can deactivate certain feeds if you don't want them to appear. Perhaps you only want "Walmart" and "Target" content... simply deactivate the other feeds. You can also select which of your pre-xisting WordPress categories each feed show be auto posted to.<br />
  <br />
  <strong>4) Links</strong>...Each feed ends with a link "Thanks to HomeBudgetIdeas.com for this Grocery Coupon". This widget and all of our content is free, in return we ask for this simple link on each post. Alternativley if you don't want this link to appear under every post- we offer a premium, link free version of this plugin for only $10 per month! You can cancel anytime. <br />
  <br />
  Signup today and get $20 off for the year! After payment you will be redirected to a page to download the link free version of this plugin instantly.<br />
  <br />
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <table>
      <tr>
        <td><input type="hidden" name="on0" value="Payment Options">
          Payment Options</td>
      </tr>
      <tr>
        <td><select name="os0">
            <option value="Monthly">Monthly : $10.00USD - monthly</option>
            <option value="Yearly (Save $20)">Yearly (Save $20) : $100.00USD - yearly</option>
          </select></td>
      </tr>
    </table>
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIIQQYJKoZIhvcNAQcEoIIIMjCCCC4CAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYCcJ/j2Fo81m4QIYgIBJzTz5HxSJRe75YOHpapORupkZnJF6M5W7LmgCYwdjHCGqIrpgH0XFF4rJMtv2DO5cc/lTyZjLkGVTmodHKILYTzzvSxrtvORW6s3q6GjdQ8fb6bBU7pjjpQ1CDtmNlxFJzFQ8sIZYAYzeXZKO0/qHH2V7zELMAkGBSsOAwIaBQAwggG9BgkqhkiG9w0BBwEwFAYIKoZIhvcNAwcECF1kEst7S7/ngIIBmFIdkQdUf5MZcdGgg7pmmO7YkVJfn/iacpgJw4JAXV2lDkz1iqEU6vF7FxSwbB06gAiuaIi6KMl6IwGDT1N24CPbdByhelb00L5Tn14DDOlP0rFsuThD0rNrQ8E/seAMdmf9/SZ3RIONbqOUUiSbDquehj7BrtoXaSFXcLb/i0t2KMG4iV7/AkV5kIe79wfuCRvkqEmOe4mIqC57cjw3lBCGn27gj03uj2HWuZKF3KeZQNhilY5f3z3mJjRMxw/0mW20/LQI1zYdBROdHuLQy5tyKcvNkEDumiRTrRDqTDhOasQiMZ4MBc9VUmAy/qVC4KLySKQHcS3cj44Fi9W+nnojUnOM7vLcuEOSycpjlB3GLYzQyhGGdFKVRtFNg1F3tRRWgLHK3ZyrEhKPM9a17Yvl3Y1vRa0XKgXXUSmAazyq4yLAhZJ62S6GeOF00tAFbfLvfMpobYQCstMKA5x2Yq8cCuay4Pe7njrJ5YExqWO0w6llMQTWYu501ozgvrFJ5V3+Y+u0DrM1GxUe+XwNeZfgAbqX01OVCKCCA4cwggODMIIC7KADAgECAgEAMA0GCSqGSIb3DQEBBQUAMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbTAeFw0wNDAyMTMxMDEzMTVaFw0zNTAyMTMxMDEzMTVaMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAwUdO3fxEzEtcnI7ZKZL412XvZPugoni7i7D7prCe0AtaHTc97CYgm7NsAtJyxNLixmhLV8pyIEaiHXWAh8fPKW+R017+EmXrr9EaquPmsVvTywAAE1PMNOKqo2kl4Gxiz9zZqIajOm1fZGWcGS0f5JQ2kBqNbvbg2/Za+GJ/qwUCAwEAAaOB7jCB6zAdBgNVHQ4EFgQUlp98u8ZvF71ZP1LXChvsENZklGswgbsGA1UdIwSBszCBsIAUlp98u8ZvF71ZP1LXChvsENZklGuhgZSkgZEwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tggEAMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADgYEAgV86VpqAWuXvX6Oro4qJ1tYVIT5DgWpE692Ag422H7yRIr/9j/iKG4Thia/Oflx4TdL+IFJBAyPK9v6zZNZtBgPBynXb048hsP16l2vi0k5Q2JKiPDsEfBhGI+HnxLXEaUWAcVfCsQFvd2A1sxRr67ip5y2wwBelUecP3AjJ+YcxggGaMIIBlgIBATCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwCQYFKw4DAhoFAKBdMBgGCSqGSIb3DQEJAzELBgkqhkiG9w0BBwEwHAYJKoZIhvcNAQkFMQ8XDTExMDkzMDIwMDAzOFowIwYJKoZIhvcNAQkEMRYEFAVhische6gsp863WGSp9ZNhJ82bMA0GCSqGSIb3DQEBAQUABIGAsBByfWOtj1ft9pv0ulgiyTNjiTrG9+P2XWFoee7N8MsDdPLNilOa0+tTUTYkUtlqyjGu5NSNortV7MQfNCFuPN/cw4M99yym7/78BDOpbbjnmbd7Vzwzc8F1ZN/W2sNxstBBOeREqCFmt9bfSelhJglOFikBQeJaq83/drxQ/uo=-----END PKCS7-----">
    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
  </form>
  <br />
</div>
<script>
  function hideinst()
  {
	if(document.getElementById('instructions').style.display=='none'){
		document.getElementById('instructions').style.display='block';
		document.getElementById('hideinst').innerHTML='Hide Instructions';
	}
	else{
		document.getElementById('instructions').style.display='none';
		document.getElementById('hideinst').innerHTML='Show Instructions';
	}	
	}
</script>
    </div>
<?php } ?>
<div id="wpomenu" class="wrap">
  <div>
    <ul>
      <li <?php echo $current['list'] ?>><a id="menu_list" href="<?php echo $this->adminurl ?>&amp;s=list">
        <?php _e('Campaigns', 'wpomatic') ?>
        </a></li>
    </ul>
  </div>
</div>
<div id="wpocontent">
