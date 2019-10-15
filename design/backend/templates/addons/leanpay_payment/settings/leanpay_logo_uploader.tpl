<div id="leanpay_logo_uploader" class="in collapse{if !$runtime.company_id && !fn_allowed_for('MULTIVENDOR') && !$runtime.simple_ultimate} disable-overlay-wrap{/if}">
    {if !$runtime.company_id && !fn_allowed_for('MULTIVENDOR') && !$runtime.simple_ultimate}
        <div class="disable-overlay" id="leanpay_logo_disable_overlay"></div>
    {/if}
    <div class="control-group">
        <label class="control-label" for="elm_leanpay_logo">{__("leanpay_logo")}:</label>
        <div class="controls">
            {include file="common/attach_images.tpl" hide_alt=true image_name="leanpay_logo" image_object_type="leanpay_logo" image_pair=$leanpay_settings.main_pair no_thumbnail=true}
            {if fn_allowed_for("ULTIMATE") && !$runtime.company_id}
                <div class="right update-for-all">
                    {include file="buttons/update_for_all.tpl" display=true object_id="leanpay_settings" name="leanpay_settings[leanpay_logo_update_all_vendors]" hide_element="leanpay_logo_uploader"}
                </div>
            {/if}
        </div>
    </div>
</div>
<script type="text/javascript">
    Tygh.$(document).ready(function(){
        var $ = Tygh.$;
        $('.cm-update-for-all-icon[data-ca-hide-id=leanpay_logo_uploader]').on('click', function() {
            $('#leanpay_logo_uploader').toggleClass('disable-overlay-wrap');
            $('#leanpay_logo_disable_overlay').toggleClass('disable-overlay');
        });
    });
</script>
