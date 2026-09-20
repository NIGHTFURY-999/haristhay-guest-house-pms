{*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 *
 * DISCLAIMER
 *
 * This file is part of the Haristhay Guest House PMS customization.
 * It retains the original QloApps/Webkul licensing and attribution.
 *}

<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Haristhay Guest House Wi-Fi</h3>
                </div>

                <div class="panel-body">
                    <p>
                        Enter the Wi-Fi username and temporary password provided
                        by Haristhay Guest House.
                    </p>

                    <form method="post"
                          action="{$wifi_auth_action|escape:'htmlall':'UTF-8'}">

                        <div class="form-group">
                            <label for="username">Wi-Fi Username</label>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                class="form-control"
                                autocomplete="username"
                                required
                            >

                        </div>

                        <div class="form-group">
                            <label for="password">Wi-Fi Password</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            name="submitWifiAuth"
                            class="btn btn-primary btn-block"
                        >
                            Connect to Wi-Fi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>