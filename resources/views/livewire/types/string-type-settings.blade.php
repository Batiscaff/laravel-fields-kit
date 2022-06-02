<div class="field-group">
    <label for="field-multistring">{{ __('fields-kit::edit.settings-multistring') }}</label>
    <select class="form-control"
           id="field-multistring"
           wire:model="settings.multistring">
        <option value="0">{{ __('fields-kit::edit.no') }}</option>
        <option value="1">{{ __('fields-kit::edit.yes') }}</option>
    </select>
</div>
<div class="field-group">
    <label for="field-rows">{{ __('fields-kit::edit.settings-rows') }}</label>
    <input type="text"
           class="form-control"
           id="field-rows"
           wire:model="settings.rows">
</div>
