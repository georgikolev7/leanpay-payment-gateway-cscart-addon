<p align="center"><img src="https://www.leanpay.si/assets/images/leanpay-tree.png" width="220"></p>

## LeanPay payment gateway (CS-Cart addon)

<a href="http://docs.leanpay.si/">http://docs.leanpay.si</a>

<p>The addon require <strong>manually</strong> set up of cron job, to synchronize installment plans from LeanPay API.</p>
<code>0 */12 * * * curl "http://yourdomain.com/index.php?dispatch=leanpay.fetch" > /dev/null 2>&1</code>