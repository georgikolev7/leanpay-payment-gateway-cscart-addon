{if fn_allowed_for('ULTIMATE') && !$runtime.company_id || $runtime.simple_ultimate || fn_allowed_for('MULTIVENDOR')}
<div id="leanpay_status_map_settings" class="in collapse">
    <div class="control-group">
        <strong class="control-label">{__('leanpay_ipn_transaction_status')}</strong>
        <div class="controls">
            <strong style="float: left; padding-top: 5px;">{__('order_status')}</strong>
        </div>
    </div>
    {assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}
    <div class="control-group">
        <label class="control-label" for="elm_leanpay_pending">{__("PENDING")}:</label>
        <div class="controls">
            <select name="leanpay_settings[leanpay_statuses][PENDING]" id="elm_leanpay_pending">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($leanpay_settings.leanpay_statuses.PENDING) && $leanpay_settings.leanpay_statuses.PENDING == $k) || (!isset($leanpay_settings.leanpay_statuses.PENDING) && $k == 'O')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_leanpay_completed">{__("COMPLETED")}:</label>
        <div class="controls">
            <select name="leanpay_settings[leanpay_statuses][COMPLETED]" id="elm_leanpay_completed">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($leanpay_settings.leanpay_statuses.COMPLETED) && $leanpay_settings.leanpay_statuses.COMPLETED == $k) || (!isset($leanpay_settings.leanpay_statuses.COMPLETED) && $k == 'C')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_leanpay_process">{__("PROCESS")}:</label>
        <div class="controls">
            <select name="leanpay_settings[leanpay_statuses][PROCESS]" id="elm_leanpay_process">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($leanpay_settings.leanpay_statuses.PROCESS) && $leanpay_settings.leanpay_statuses.PROCESS == $k) || (!isset($leanpay_settings.leanpay_statuses.PROCESS) && $k == 'F')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_leanpay_on_hold">{__("ON_HOLD")}:</label>
        <div class="controls">
            <select name="leanpay_settings[leanpay_statuses][ON_HOLD]" id="elm_leanpay_on_hold">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($leanpay_settings.leanpay_statuses.ON_HOLD) && $leanpay_settings.leanpay_statuses.ON_HOLD == $k) || (!isset($leanpay_settings.leanpay_statuses.ON_HOLD) && $k == 'O')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_leanpay_canceled">{__("CANCELED")}:</label>
        <div class="controls">
            <select name="leanpay_settings[leanpay_statuses][CANCELED]" id="elm_leanpay_canceled">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($leanpay_settings.leanpay_statuses.CANCELED) && $leanpay_settings.leanpay_statuses.CANCELED == $k) || (!isset($leanpay_settings.leanpay_statuses.CANCELED) && $k == 'I')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_leanpay_not_finished">{__("NOT_FINISHED")}:</label>
        <div class="controls">
            <select name="leanpay_settings[leanpay_statuses][NOT_FINISHED]" id="elm_leanpay_not_finished">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($leanpay_settings.leanpay_statuses.NOT_FINISHED) && $leanpay_settings.leanpay_statuses.NOT_FINISHED == $k) || (!isset($leanpay_settings.leanpay_statuses.NOT_FINISHED) && $k == 'O')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
{/if}