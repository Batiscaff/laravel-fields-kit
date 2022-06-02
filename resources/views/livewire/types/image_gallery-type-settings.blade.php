<div class="field-group">
    <label for="field-max-count">{{ __('fields-kit::edit.settings-list-columns') }}</label>
    <textarea class="form-control"
              id="field-value"
              rows="10"
              wire:model="settings.columns"></textarea>
</div>
<div class="field-group">
    <label for="field-max-count">{{ __('fields-kit::edit.settings-max-count') }}</label>
    <input type="text"
           class="form-control"
           id="field-max-count"
           wire:model="settings.max_count">
</div>
