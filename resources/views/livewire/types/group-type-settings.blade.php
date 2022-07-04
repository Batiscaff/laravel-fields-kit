<div class="field-group pt-3">
    <label for="field-max-count">{{ __('fields-kit::edit.settings-max-number') }}</label>
    <input type="text"
           class="form-control"
           id="field-max-count"
           wire:model="settings.max_count">
</div>
<div class="field-group pt-3">
    <label for="field-group-type">{{ __('fields-kit::edit.settings-group-type') }}</label>
    <select class="form-control"
            id="field-group-type"
            wire:model="settings.group-type">
        <option value="{{ \Batiscaff\FieldsKit\Types\GroupType::GROUP_TYPE_COMMON }}">
            {{ __('fields-kit::edit.group-type-common') }}
        </option>
        <option value="{{ \Batiscaff\FieldsKit\Types\GroupType::GROUP_TYPE_FLAT }}">
            {{ __('fields-kit::edit.group-type-flat') }}
        </option>
    </select>
</div>
