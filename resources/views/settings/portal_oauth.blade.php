<form class="form-horizontal margin-top margin-bottom" method="POST" action="">
	{{ csrf_field() }}

	<h2>{{ __('Portal OAuth Configuration') }}</h2>

	<div class="form-group{{ $errors->has('settings.' . EndUserPortal::getPluginOptionName('portal_oauth_enabled')) ? ' has-error' : '' }}">
		<label for="settings_{{ EndUserPortal::getPluginOptionName('portal_oauth_enabled') }}" class="col-sm-2 control-label">{{ __('Enable Portal OAuth') }}</label>

		<div class="col-sm-6">
			<div class="controls">
				<div class="onoffswitch-wrap">
					<div class="onoffswitch">
						<input type="checkbox" name="settings[{{ EndUserPortal::getPluginOptionName('portal_oauth_enabled') }}]" value="1" id="settings_{{ EndUserPortal::getPluginOptionName('portal_oauth_enabled') }}" class="onoffswitch-checkbox" @if (old('settings.' . EndUserPortal::getPluginOptionName('portal_oauth_enabled'), $settings[EndUserPortal::getPluginOptionName('portal_oauth_enabled')]))checked="checked"@endif>
						<label class="onoffswitch-label" for="settings_{{ EndUserPortal::getPluginOptionName('portal_oauth_enabled') }}"></label>
					</div>
				</div>
			</div>
			@include('partials/field_error', ['field'=>'settings.' . EndUserPortal::getPluginOptionName('portal_oauth_enabled')])
		</div>
	</div>

	<div class="form-group{{ $errors->has('settings.' . EndUserPortal::getPluginOptionName('portal_server_url')) ? ' has-error' : '' }}">
		<label for="settings_{{ EndUserPortal::getPluginOptionName('portal_server_url') }}" class="col-sm-2 control-label">{{ __('Portal Server URL') }}</label>

		<div class="col-sm-6">
			<input id="settings_{{ EndUserPortal::getPluginOptionName('portal_server_url') }}" type="url" class="form-control input-sized" name="settings[{{ EndUserPortal::getPluginOptionName('portal_server_url') }}]" value="{{ old('settings.' . EndUserPortal::getPluginOptionName('portal_server_url'), $settings[EndUserPortal::getPluginOptionName('portal_server_url')]) }}" required>
			<p class="form-help">
				{{ __('The base URL of your portal server (e.g., https://portal.example.com)') }}
			</p>
			@include('partials/field_error', ['field'=>'settings.' . EndUserPortal::getPluginOptionName('portal_server_url')])
		</div>
	</div>

	<div class="form-group{{ $errors->has('settings.' . EndUserPortal::getPluginOptionName('portal_client_id')) ? ' has-error' : '' }}">
		<label for="settings_{{ EndUserPortal::getPluginOptionName('portal_client_id') }}" class="col-sm-2 control-label">{{ __('Portal Client ID') }}</label>

		<div class="col-sm-6">
			<input id="settings_{{ EndUserPortal::getPluginOptionName('portal_client_id') }}" type="text" class="form-control input-sized" name="settings[{{ EndUserPortal::getPluginOptionName('portal_client_id') }}]" value="{{ old('settings.' . EndUserPortal::getPluginOptionName('portal_client_id'), $settings[EndUserPortal::getPluginOptionName('portal_client_id')]) }}" required>

			@include('partials/field_error', ['field'=>'settings.' . EndUserPortal::getPluginOptionName('portal_client_id')])
		</div>
	</div>

	<div class="form-group{{ $errors->has('settings.' . EndUserPortal::getPluginOptionName('portal_client_secret')) ? ' has-error' : '' }}">
		<label for="settings_{{ EndUserPortal::getPluginOptionName('portal_client_secret') }}" class="col-sm-2 control-label">{{ __('Portal Client Secret') }}</label>

		<div class="col-sm-6">
			<input id="settings_{{ EndUserPortal::getPluginOptionName('portal_client_secret') }}" type="password" class="form-control input-sized" name="settings[{{ EndUserPortal::getPluginOptionName('portal_client_secret') }}]" value="{{ old('settings.' . EndUserPortal::getPluginOptionName('portal_client_secret'), $settings[EndUserPortal::getPluginOptionName('portal_client_secret')]) }}" required>

			@include('partials/field_error', ['field'=>'settings.' . EndUserPortal::getPluginOptionName('portal_client_secret')])
		</div>
	</div>

	<div class="form-group margin-top">
		<div class="col-sm-6 col-sm-offset-2">
			<button type="submit" class="btn btn-primary">
				{{ __('Save Portal OAuth Settings') }}
			</button>
		</div>
	</div>
</form>
