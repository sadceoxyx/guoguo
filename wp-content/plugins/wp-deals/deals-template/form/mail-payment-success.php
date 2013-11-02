<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<title>Invoice</title>

	</head>

	<body>

		<?php global $checkVerify, $invoice_options, $invoice_data; ?>

		<?php
			// format buy_date
			$buy_date_raw = date_create($checkVerify->buy_date);
			$buy_date = date_format($buy_date_raw, 'M d, y');

		?>

		<div id="page-wrap" style="width: 80%;max-width: 700px;margin: 2% 10%;font-family: arial, sans-serif;font-size: 13px;">

			<div id="header" style="font-weight: bold;font-size: 2em;">INVOICE</div>

			<div id="identity" style="display: block;overflow: hidden;margin: 4em 0;vertical-align: middle;">

				<div id="address" style="float: left;width: 30%"><?php echo $invoice_options['info']; ?></div>

				<div style="float: right" id="logo">
					<img id="image" src="<?php echo $invoice_options['logo_url']; ?>" alt="<?php echo $invoice_options['store_name']; ?>" />
				</div>

			</div>


			<div style="clear:both"></div>


			<div id="customer">

				<div id="customer-title" style="font-size: 1.5em;margin-bottom: 1em;border-bottom: 2px dotted #adadad;padding-bottom: .5em;"><?php echo $invoice_options['store_name']; ?></div>

				<table id="meta" style="width: 100%;text-align: right;border-collapse: collapse;">
					<tbody>
						<tr style="float-left;display: inline-table;width: 33%;padding: .5em 0;background:#ccc">
							<td class="meta-head" style="float:left;text-align:center;width:100%">Invoice</td>
							<td style="float:left;text-align:center;width:100%;font-size:1.1em;font-weight:bold">#<?php echo $checkVerify->transaction_id; ?></td>
						</tr>
						<tr style="float-left;display: inline-table;width: 33%;padding: .5em 0;background: #adadad;color: #fff;">
							<td class="meta-head" style="float:left;text-align:center;width:100%">Date</td>
							<td style="float:left;text-align:center;width:100%;font-size:1.3em;font-weight:bold"><div class="date"><?php echo $buy_date; ?></div></td>
						</tr>
						<tr style="float-left;display: inline-table;width: 33%;padding: .5em 0;background:#ccc">
							<td class="meta-head" style="float:left;text-align:center;width:100%">Amount Due</td>
							<td style="float:left;text-align:center;width:100%;font-size:1.1em;font-weight:bold"><div class="due">$<?php echo $checkVerify->total_price; ?></div></td>
						</tr>
					</tbody>
				</table>

			</div>

                        <p>Thank you for your interesting with our deals. You can check your history deal on this link:<?php echo site_url('/my-history')?></p>
                        <?php if ($checkVerify->isUsedCouponCode):?>
                        <p>This is your coupon code <?php echo $checkVerify->couponCode;?></p>
                        <?php endif;?>

                        <?php if ($checkVerify->isHaveCustomMessage):?>
                        <?php echo nl2br($checkVerify->customMessage)?>
                        <?php endif;?>

		</div>


	</body>
</html>