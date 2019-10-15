{if fn_allowed_for('ULTIMATE') && !$runtime.company_id || $runtime.simple_ultimate || fn_allowed_for('MULTIVENDOR')}
    <div id="leanpay_callback_url" class="in collapse">
        <div class="control-group">
            <strong class="control-label">{__('leanpay_callback_url')}</strong>
            <div class="controls">
                <strong style="float: left; padding-top: 5px;"><pre><code>{$leanpay_settings.callback_url}</code></pre></strong>
                <div class="clearfix"></div>
                <span style="color: #868686; font-size: 11px;">{__('leanpay_callback_url_description')}</span>

            </div>

        </div>
    </div>
{/if}