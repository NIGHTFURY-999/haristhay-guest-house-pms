<div class="container online-checkin">
    <div class="row">
        <div class="col-xs-12">
            <h1>Online Check-In</h1>

            <p>Please review your booking information and complete the required guest details before arrival.</p>

            {if isset($checkin_success) && $checkin_success}
                <div class="alert alert-success">
                    {$checkin_success|escape:'html':'UTF-8'}
                </div>
            {/if}

            {if isset($checkin_error) && $checkin_error}
                <div class="alert alert-danger">
                    {$checkin_error|escape:'html':'UTF-8'}
                </div>
            {/if}


            {if !isset($checkin_success) || !$checkin_success}
            <form method="post" enctype="multipart/form-data">

                <h3>Stay Information</h3>

                <div class="form-group">
                    <label>Booking Reference</label>
                    <input type="text"
                           class="form-control"
                           value="{$booking->id|intval}"
                           readonly>
                </div>

                <div class="form-group">
                    <label>Room Type</label>
                    <input type="text"
                           class="form-control"
                           value="{$booking->room_type_name|escape:'html':'UTF-8'}"
                           readonly>
                </div>

                <div class="form-group">
                    <label>Room Number</label>
                    <input type="text"
                           class="form-control"
                           value="{$booking->room_num|escape:'html':'UTF-8'}"
                           readonly>
                </div>

                <div class="form-group">
                    <label>Check-In Date</label>
                    <input type="text"
                           class="form-control"
                           value="{$booking->date_from|escape:'html':'UTF-8'}"
                           readonly>
                </div>

                <div class="form-group">
                    <label>Check-Out Date</label>
                    <input type="text"
                           class="form-control"
                           value="{$booking->date_to|escape:'html':'UTF-8'}"
                           readonly>
                </div>

                <div class="form-group">
                    <label>Number of Guests</label>
                    <input type="text"
                           class="form-control"
                           value="{($booking->adults + $booking->children)|intval}"
                           readonly>
                </div>

                <h3>Guest Information</h3>

                <div class="form-group">
                    <label>Full Name as per ID *</label>
                    <input type="text"
                           name="full_name"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Mobile *</label>
                    <input type="tel"
                           name="phone"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Date of Birth *</label>
                    <input type="date"
                           name="date_of_birth"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Nationality *</label>
                    <input type="text"
                           name="nationality"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Address *</label>
                    <textarea name="address"
                              class="form-control"
                              rows="3"
                              required></textarea>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>City *</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" class="form-control">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Postal Code</label>
                            <input type="text" name="postal_code" class="form-control">
                        </div>
                    </div>
                </div>

                <h3>Identification</h3>

                <div class="form-group">
                    <label>ID Type *</label>
                    <select name="id_type" class="form-control" required>
                        <option value="">Select ID type</option>
                        <option value="passport">Passport</option>
                        <option value="driving_license">Driving License</option>
                        <option value="national_id">National ID</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>ID Number *</label>
                    <input type="text"
                           name="id_number"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>ID Document *</label>
                    <input type="file"
                           name="id_document"
                           class="form-control"
                           accept=".jpg,.jpeg,.png,.pdf"
                           required>
                    <small>Accepted formats: JPG, JPEG, PNG, PDF.</small>
                </div>

                <div id="foreign-passport-fields" style="display:none;">
                    <div class="form-group">
                        <label>Passport Number</label>
                        <input type="text"
                               name="passport_number"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Passport Expiry</label>
                        <input type="date"
                               name="passport_date_of_expiry"
                               class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Place of Issue</label>
                    <input type="text"
                           name="id_place_of_issue"
                           class="form-control">
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>ID Date of Issue</label>
                            <input type="date"
                                   name="id_date_of_issue"
                                   class="form-control">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>ID Date of Expiry</label>
                            <input type="date"
                                   name="id_date_of_expiry"
                                   class="form-control">
                        </div>
                    </div>
                </div>

                <h3>Travel Information</h3>

                <div class="form-group">
                    <label>Purpose of Visit *</label>
                    <select name="purpose_of_visit" class="form-control" required>
                        <option value="">Select purpose</option>
                        <option value="tourism">Tourism</option>
                        <option value="business">Business</option>
                        <option value="work">Work</option>
                        <option value="medical">Medical</option>
                        <option value="education">Education</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <h3>Additional Guests</h3>

                <p>Please enter the details of other guests staying under this booking.</p>

                {for $guestIndex=1 to 4}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Additional Guest {$guestIndex}
                        </div>

                        <div class="panel-body">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text"
                                       name="additional_guest[{$guestIndex}][full_name]"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Nationality</label>
                                <input type="text"
                                       name="additional_guest[{$guestIndex}][nationality]"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date"
                                       name="additional_guest[{$guestIndex}][date_of_birth]"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label>ID Type</label>
                                <select name="additional_guest[{$guestIndex}][id_type]"
                                        class="form-control">
                                    <option value="">Select ID Type</option>
                                    <option value="passport">Passport</option>
                                    <option value="driving_license">Driving License</option>
                                    <option value="national_id">National ID</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>ID Number</label>
                                <input type="text"
                                       name="additional_guest[{$guestIndex}][id_number]"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                {/for}
                <h3>Foreign Guest Information</h3>

                <div class="checkbox">
                    <label>
                        <input type="checkbox"
                               name="is_foreign_guest"
                               value="1">
                        I am a foreign guest
                    </label>
                </div>

                <div id="foreign-guest-fields" style="display:none;">
                    <div class="form-group">
                        <label>Visa Number</label>
                        <input type="text"
                               name="visa_number"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Visa Valid Until</label>
                        <input type="date"
                               name="visa_valid_until"
                               class="form-control">
                    </div>
                </div>

                <h3>Declaration</h3>

                <div class="checkbox">
                    <label>
                        <input type="checkbox"
                               name="terms_accepted"
                               value="1"
                               required>
                        I accept the hotel policies and confirm that the information provided is accurate.
                    </label>
                </div>

                <div class="form-group">
                    <label>Guest Declaration</label>
                    <textarea name="guest_declaration"
                              class="form-control"
                              rows="4"
                              placeholder="Enter your declaration"></textarea>
                </div>

                <div class="form-group">
                    <label>Digital Signature / Confirmation</label>
                    <input type="text"
                           name="guest_signature"
                           class="form-control"
                           placeholder="Type your full name as confirmation">
                </div>

                <button type="submit"
                        name="submitOnlineCheckin"
                        class="btn btn-primary">
                    Submit Online Check-In
                </button>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var checkbox = document.querySelector('input[name="is_foreign_guest"]');
                    var foreignFields = document.getElementById('foreign-guest-fields');
                    var passportFields = document.getElementById('foreign-passport-fields');

                    if (!checkbox || !foreignFields || !passportFields) {
                        return;
                    }

                    function toggleForeignFields() {
                        var displayValue = checkbox.checked ? 'block' : 'none';

                        foreignFields.style.display = displayValue;
                        passportFields.style.display = displayValue;

                        var passportNumber = document.querySelector('input[name="passport_number"]');
                        var passportExpiry = document.querySelector('input[name="passport_date_of_expiry"]');
                        var visaNumber = document.querySelector('input[name="visa_number"]');
                        var visaValidUntil = document.querySelector('input[name="visa_valid_until"]');

                        if (passportNumber) {
                            passportNumber.required = checkbox.checked;
                        }

                        if (passportExpiry) {
                            passportExpiry.required = checkbox.checked;
                        }

                        if (visaNumber) {
                            visaNumber.required = checkbox.checked;
                        }

                        if (visaValidUntil) {
                            visaValidUntil.required = checkbox.checked;
                        }
                    }

                    checkbox.addEventListener('change', toggleForeignFields);
                    toggleForeignFields();
                });
            </script>
            </form>
            {/if}
        </div>
    </div>
</div>
