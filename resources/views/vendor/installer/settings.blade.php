@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.settings.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-cog fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.settings.title') }}
@endsection

@section('container')
    <div class="tabs tabs-full">

        <input id="tab1" type="radio" name="tabs" class="tab-input" checked/>
        <label for="tab1" class="tab-label">
            <i class="fa fa-cog fa-2x fa-fw" aria-hidden="true"></i>
            <br/>
            {{ trans('installer_messages.settings.tab') }}
        </label>

        <form method="post" action="{{ route('LaravelInstaller::settingsSaveWizard') }}" class="tabs-wrap">
            <div class="tab" id="tab1content">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group {{ $errors->has('app_limit_shopping') ? ' has-error ' : '' }}">
                    <label for="app_limit_shopping">
                        {{ trans('installer_messages.settings.form.app_limit_shopping') }}
                    </label>
                    <input type="text" name="app_limit_shopping" id="app_limit_shopping" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_limit_shopping_label') }}"/>
                    @if ($errors->has('app_limit_shopping'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_limit_shopping') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_currency') ? ' has-error ' : '' }}">
                    <label for="app_currency">
                        {{ trans('installer_messages.settings.form.app_currency') }}
                    </label>
                    <input type="text" name="app_currency" id="app_currency" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_currency_label') }}"/>
                    @if ($errors->has('app_currency'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_currency') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_feed') ? ' has-error ' : '' }}">
                    <label for="app_feed">
                        {{ trans('installer_messages.settings.form.app_feed') }}
                    </label>
                    <input type="text" name="app_feed" id="app_feed" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_feed_label') }}"/>
                    @if ($errors->has('app_feed'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_feed') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_feed_amount') ? ' has-error ' : '' }}">
                    <label for="app_feed_amount">
                        {{ trans('installer_messages.settings.form.app_feed_amount') }}
                    </label>
                    <input type="text" name="app_feed_amount" id="app_feed_amount" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_feed_amount_label') }}"/>
                    @if ($errors->has('app_feed_amount'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_feed_amount') }}
                        </span>
                    @endif
                </div>


                <div class="form-group {{ $errors->has('app_feed_limit') ? ' has-error ' : '' }}">
                    <label for="app_feed_limit">
                        {{ trans('installer_messages.settings.form.app_feed_limit') }}
                    </label>
                    <input type="text" name="app_feed_limit" id="app_feed_limit" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_feed_limit_label') }}"/>
                    @if ($errors->has('app_feed_amount'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_feed_limit') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_delivery') ? ' has-error ' : '' }}">
                    <label for="app_delivery">
                        {{ trans('installer_messages.settings.form.app_delivery') }}
                    </label>
                    <input type="text" name="app_delivery" id="app_delivery" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_delivery_label') }}"/>
                    @if ($errors->has('app_delivery'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_delivery') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_delivery_tax') ? ' has-error ' : '' }}">
                    <label for="app_delivery_tax">
                        {{ trans('installer_messages.settings.form.app_delivery_tax') }}
                    </label>
                    <input type="text" name="app_delivery_tax" id="app_delivery_tax" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_delivery_tax_label') }}"/>
                    @if ($errors->has('app_delivery_tax'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_delivery_tax') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_delivery_countries') ? ' has-error ' : '' }}">
                    <label for="app_delivery_countries">
                        {{ trans('installer_messages.settings.form.app_delivery_countries') }}
                    </label>
                    <input type="text" name="app_delivery_countries" id="app_delivery_countries" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_delivery_countries_label') }}"/>
                    @if ($errors->has('app_delivery_countries'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_delivery_countries') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_redirect') ? ' has-error ' : '' }}">
                    <label for="app_redirect">
                        {{ trans('installer_messages.settings.form.app_redirect') }}
                        <a href="https://dashboard.stripe.com/settings/applications" target="_blank">
                            {{trans('installer_messages.environment.wizard.form.app_stripe_app_id_click')}}
                        </a>
                    </label>
                    <input type="text" name="app_redirect" id="app_delivery_countries" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_redirect_label') }}"/>
                    @if ($errors->has('app_redirect'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_redirect') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_search_range') ? ' has-error ' : '' }}">
                    <label for="app_search_range">
                        {{ trans('installer_messages.settings.form.app_search_range') }}
                    </label>
                    <input type="text" name="app_search_range" id="app_search_range" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_search_range_label') }}"/>
                    @if ($errors->has('app_search_range'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_search_range') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_edge_time') ? ' has-error ' : '' }}">
                    <label for="app_edge_time">
                        {{ trans('installer_messages.settings.form.app_edge_time') }}
                    </label>
                    <input type="text" name="app_edge_time" id="app_edge_time" value=""
                           placeholder="{{ trans('installer_messages.settings.form.app_edge_time_label') }}"/>
                    @if ($errors->has('app_edge_time'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_edge_time') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_discount_supplier') ? ' has-error ' : '' }}">
                    <label for="app_discount_supplier">
                        {{ trans('installer_messages.settings.form.app_discount_supplier') }}
                    </label>
                    <select name="app_discount_supplier" id="app_discount_supplier">
                        <option value="1" selected>{{ trans('installer_messages.settings.form.true') }}</option>
                        <option value="0" selected>{{ trans('installer_messages.settings.form.false') }}</option>
                    </select>
                    @if ($errors->has('app_discount_supplier'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_discount_supplier') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_delivery_auto') ? ' has-error ' : '' }}">
                    <label for="app_delivery_auto">
                        {{ trans('installer_messages.settings.form.app_delivery_auto') }}
                    </label>
                    <select name="app_delivery_auto" id="app_delivery_auto">
                        <option value="1" selected>{{ trans('installer_messages.settings.form.true') }}</option>
                        <option value="0" selected>{{ trans('installer_messages.settings.form.false') }}</option>
                    </select>
                    @if ($errors->has('app_delivery_auto'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_delivery_auto') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_sms') ? ' has-error ' : '' }}">
                    <label for="app_sms">
                        {{ trans('installer_messages.settings.form.app_sms') }}
                    </label>
                    <select name="app_sms" id="app_sms">
                        <option value="1" selected>{{ trans('installer_messages.settings.form.true') }}</option>
                        <option value="0" selected>{{ trans('installer_messages.settings.form.false') }}</option>
                    </select>
                    @if ($errors->has('app_sms'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_sms') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_range_closed') ? ' has-error ' : '' }}">
                    <label for="app_range_closed">
                        {{ trans('installer_messages.settings.form.app_range_closed') }}
                    </label>
                    <select name="app_range_closed" id="app_range_closed">
                        <option value="1" selected>{{ trans('installer_messages.settings.form.true') }}</option>
                        <option value="0" selected>{{ trans('installer_messages.settings.form.false') }}</option>
                    </select>
                    @if ($errors->has('app_range_closed'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_range_closed') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_google_vision') ? ' has-error ' : '' }}">
                    <label for="app_google_vision">
                        {{ trans('installer_messages.settings.form.app_google_vision') }}
                    </label>
                    <select name="app_google_vision" id="app_google_vision">
                        <option value="1" selected>{{ trans('installer_messages.settings.form.true') }}</option>
                        <option value="0" selected>{{ trans('installer_messages.settings.form.false') }}</option>
                    </select>
                    @if ($errors->has('app_google_vision'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_google_vision') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_user_confirmed') ? ' has-error ' : '' }}">
                    <label for="app_user_confirmed">
                        {{ trans('installer_messages.settings.form.app_user_confirmed') }}
                    </label>
                    <select name="app_user_confirmed" id="app_user_confirmed">
                        <option value="1" selected>{{ trans('installer_messages.settings.form.true') }}</option>
                        <option value="0" selected>{{ trans('installer_messages.settings.form.false') }}</option>
                    </select>
                    @if ($errors->has('app_user_confirmed'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_user_confirmed') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_market_place') ? ' has-error ' : '' }}">
                    <label for="app_market_place">
                        {{ trans('installer_messages.settings.form.app_market_place') }}
                    </label>
                    <select name="app_market_place" id="app_market_place">
                        <option value="1" selected>{{ trans('installer_messages.settings.form.true') }}</option>
                        <option value="0" selected>{{ trans('installer_messages.settings.form.false') }}</option>
                    </select>
                    @if ($errors->has('app_market_place'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_market_place') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_schedule') ? ' has-error ' : '' }}">
                    <label for="app_schedule">
                        {{ trans('installer_messages.settings.form.app_schedule') }}
                    </label>
                    <select name="app_schedule" id="app_schedule">
                        <option value="1" selected>{{ trans('installer_messages.settings.form.true') }}</option>
                        <option value="0" selected>{{ trans('installer_messages.settings.form.false') }}</option>
                    </select>
                    @if ($errors->has('app_schedule'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_schedule') }}
                        </span>
                    @endif
                </div>
                <div class="buttons">
                    <button class="button" onclick="showDatabaseSettings();return false">
                        {{ trans('installer_messages.environment.wizard.form.buttons.setup_database') }}
                        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </form>

    </div>
@endsection
