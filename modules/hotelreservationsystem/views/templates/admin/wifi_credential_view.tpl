<div class="panel">
    <div class="panel-heading">
        <i class="icon-wifi"></i>
        {l s='Wi-Fi Credential'}
    </div>

    <div class="form-horizontal">

        <h4>{l s='Credential Information'}</h4>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Status'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    <strong>{$credential->status|escape:'html':'UTF-8'}</strong>
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Wi-Fi Username'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$credential->username|escape:'html':'UTF-8'}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Booking'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    #{$credential->id_htl_booking|intval}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Room'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {if $credential->id_room}
                        #{$credential->id_room|intval}
                    {else}
                        -
                    {/if}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Guest'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {if Validate::isLoadedObject($customer)}
                        {$customer->firstname|escape:'html':'UTF-8'}
                        {$customer->lastname|escape:'html':'UTF-8'}
                    {else}
                        {l s='Unknown Guest'}
                    {/if}
                </p>
            </div>
        </div>

        <h4>{l s='Access Period'}</h4>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Activated At'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$credential->activated_at|escape:'html':'UTF-8'|default:'-'}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Expires At'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$credential->expires_at|escape:'html':'UTF-8'|default:'-'}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Revoked At'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$credential->revoked_at|escape:'html':'UTF-8'|default:'-'}
                </p>
            </div>
        </div>

        <h4>{l s='Security'}</h4>

        <div class="alert alert-info">
            <i class="icon-info-circle"></i>
            {l s='The Wi-Fi password is stored securely and cannot be displayed after credential creation.'}
        </div>

    </div>
</div>