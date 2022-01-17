<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger">
            {foreach from=$endrc_errormessages item=message}
                <p>{$message}</p>
            {/foreach}
            {if $endrc_translations['endereco_jtl4_phs_please_correct']}
                <p>{$endrc_translations['endereco_jtl4_phs_please_correct']}</p>
            {/if}
            {if $endrc_translations['endereco_jtl4_phs_contact_support']}
                <p>{$endrc_translations['endereco_jtl4_phs_contact_support']}</p>
            {/if}
        </div>
    </div>
</div>
