<p align="center"><img src="https://www.leanpay.si/assets/images/leanpay-tree.png" width="220"></p>

## LeanPay payment gateway (CS-Cart addon)

<a href="http://docs.leanpay.si/">http://docs.leanpay.si</a>

<p>The addon require <strong>manually</strong> set up of cron job, to synchronize installment plans from LeanPay API.</p>
<code>0 */12 * * * curl "http://yourdomain.com/index.php?dispatch=leanpay.fetch" > /dev/null 2>&1</code>

## How to integrate into template

1. Inside your "design/themes/<strong>NAME_OF_YOUR_THEME</strong>/templates/common/product_data.tpl", add the following code
```smarty
{capture name="leanpay_payment_`$obj_id`"}
    {if $product.leanpay_installments}
    
        <span class="cm-reload-{$obj_prefix}{$obj_id}" id="leanpay_payment_update_{$obj_prefix}{$obj_id}">
            <ul class="installments" data-price="{$product.price}">
                {foreach from=$product.leanpay_installments item=installment}
                    <li>polog	<strong>{$product.leanpay_downpayment|string_format:"%.2f"}€</strong> + {$installment.months} x <strong>{$installment.installment|string_format:"%.2f"}€</strong></li>
                {/foreach}
            </ul>
        <!--leanpay_payment_update_{$obj_prefix}{$obj_id}--></span>
        
    {/if}
{/capture}

{if $no_capture}
    {assign var="capture_name" value="leanpay_payment_`$obj_id`"}
    {$smarty.capture.$capture_name nofilter}
{/if}
```

2. Inside your "design/themes/<strong>NAME_OF_YOUR_THEME</strong>/templates/blocks/product_templates/default_template.tpl find
<code>
  {$smarty.capture.$list_discount nofilter}
</code>

and add after the capture
```smarty
{assign var="leanpay_payment" value="leanpay_payment_`$obj_id`"}
{$smarty.capture.$leanpay_payment nofilter}
```
