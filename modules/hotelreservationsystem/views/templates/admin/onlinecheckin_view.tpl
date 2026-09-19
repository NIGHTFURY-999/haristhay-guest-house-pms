<div class="panel">
    <div class="panel-heading">
        <i class="icon-user"></i>
        {l s='Online Check-In Review'}
    </div>

    <div class="form-horizontal">

        <h4>{l s='Submission'}</h4>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Status'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    <strong>{$checkin.status|escape:'html':'UTF-8'}</strong>
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Booking'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    #{$checkin.id_htl_booking|intval}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Guest'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$customer->firstname|escape:'html':'UTF-8'}
                    {$customer->lastname|escape:'html':'UTF-8'}
                </p>
            </div>
        </div>

        <h4>{l s='Stay Information'}</h4>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Room Type'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$booking->room_type_name|escape:'html':'UTF-8'}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Room Number'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$booking->room_num|escape:'html':'UTF-8'}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Check-In'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$booking->date_from|escape:'html':'UTF-8'}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Check-Out'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$booking->date_to|escape:'html':'UTF-8'}
                </p>
            </div>
        </div>

        <h4>{l s='Identification'}</h4>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='ID Type'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$checkin.id_type|escape:'html':'UTF-8'|default:'-'}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='ID Number'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$checkin.id_number|escape:'html':'UTF-8'|default:'-'}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Passport Number'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$checkin.passport_number|escape:'html':'UTF-8'|default:'-'}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Passport Expiry'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$checkin.passport_date_of_expiry|escape:'html':'UTF-8'|default:'-'}
                </p>
            </div>
        </div>

        <h4>{l s='Travel Information'}</h4>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Purpose of Visit'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {$checkin.purpose_of_visit|escape:'html':'UTF-8'|default:'-'}
                </p>
            </div>
        </div>

        <h4>{l s='Foreign Guest'}</h4>

        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Foreign Guest'}</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {if $checkin.is_foreign_guest}
                        {l s='Yes'}
                    {else}
                        {l s='No'}
                    {/if}
                </p>
            </div>
        </div>

        {if $checkin.is_foreign_guest}
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Visa Number'}</label>
                <div class="col-lg-9">
                    <p class="form-control-static">
                        {$checkin.visa_number|escape:'html':'UTF-8'|default:'-'}
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Visa Valid Until'}</label>
                <div class="col-lg-9">
                    <p class="form-control-static">
                        {$checkin.visa_valid_until|escape:'html':'UTF-8'|default:'-'}
                    </p>
                </div>
            </div>
        {/if}

        <h4>{l s='Uploaded Documents'}</h4>

        {if !empty($documents)}
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Document'}</th>
                        <th>{l s='Type'}</th>
                        <th>{l s='Uploaded'}</th>
                        <th>{l s='Action'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$documents item=document}
                        <tr>
                            <td>
                                {$document.title|escape:'html':'UTF-8'}
                            </td>
                            <td>
                                {$document.file_type|intval}
                            </td>
                            <td>
                                {$document.date_add|escape:'html':'UTF-8'}
                            </td>
                            <td>
                                <a
                                    class="btn btn-default"
                                    href="{$link->getAdminLink('AdminBookingDocument')|escape:'html':'UTF-8'}&id_document={$document.id_htl_booking_document|intval}&is_preview=1"
                                    target="_blank"
                                >
                                    <i class="icon-eye"></i>
                                    {l s='View'}
                                </a>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {else}
            <p class="form-control-static">
                {l s='No documents uploaded.'}
            </p>
        {/if}

        <h4>{l s='Additional Guests'}</h4>

        {if !empty($additional_guests)}
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Name'}</th>
                        <th>{l s='Nationality'}</th>
                        <th>{l s='Date of Birth'}</th>
                        <th>{l s='ID Type'}</th>
                        <th>{l s='ID Number'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$additional_guests item=guest}
                        <tr>
                            <td>{$guest.full_name|escape:'html':'UTF-8'}</td>
                            <td>{$guest.nationality|escape:'html':'UTF-8'}</td>
                            <td>{$guest.date_of_birth|escape:'html':'UTF-8'}</td>
                            <td>{$guest.id_type|escape:'html':'UTF-8'}</td>
                            <td>{$guest.id_number|escape:'html':'UTF-8'}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {else}
            <p class="form-control-static">
                {l s='No additional guests.'}
            </p>
        {/if}

        <h4>{l s='Review'}</h4>

        {if $checkin.reviewed_at}
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Reviewed At'}</label>
                <div class="col-lg-9">
                    <p class="form-control-static">
                        {$checkin.reviewed_at|escape:'html':'UTF-8'}
                    </p>
                </div>
            </div>
        {/if}

        {if $checkin.rejection_reason}
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Rejection Reason'}</label>
                <div class="col-lg-9">
                    <p class="form-control-static">
                        {$checkin.rejection_reason|escape:'html':'UTF-8'|nl2br}
                    </p>
                </div>
            </div>
        {/if}

        <form method="post" class="form-horizontal">
            <input type="hidden" name="id_online_checkin" value="{$checkin.id_online_checkin|intval}" />

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Rejection Reason'}</label>
                <div class="col-lg-9">
                    <textarea
                        name="rejection_reason"
                        class="form-control"
                        rows="4"
                        placeholder="{l s='Required when rejecting'}"
                    ></textarea>
                </div>
            </div>

            <div class="panel-footer">
                <button
                    type="submit"
                    name="submitOnlineCheckinReview"
                    value="1"
                    class="btn btn-default"
                >
                    <i class="icon-search"></i>
                    {l s='Under Review'}
                </button>

                <button
                    type="submit"
                    name="submitOnlineCheckinReview"
                    value="1"
                    class="btn btn-success"
                    onclick="this.form.review_action.value='approve';"
                >
                    <i class="icon-check"></i>
                    {l s='Approve'}
                </button>

                <button
                    type="submit"
                    name="submitOnlineCheckinReview"
                    value="1"
                    class="btn btn-danger"
                    onclick="this.form.review_action.value='reject';"
                >
                    <i class="icon-remove"></i>
                    {l s='Reject'}
                </button>

                <input type="hidden" name="review_action" value="under_review" />
            </div>
        </form>

    </div>
</div>
