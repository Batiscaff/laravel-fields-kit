<div class="card card-info">
    <div class="card-header">
        {{ __('fields-kit::edit.field-content') }}
    </div>
    <div class="card-body">
        <div class="field-group">
            <label for="field-value">{{ __('fields-kit::edit.field-value') }}</label>
            @can(config('fields-kit.permission.peculiar-field.update-value'))
                <textarea class="form-control"
                          id="field-value"
                          rows="{{ $this->currentField->settings['rows'] ?? 5 }}"
                          wire:model="value"></textarea>
            @else
                <textarea class="form-control"
                          id="field-value"
                          rows="{{ $this->currentField->settings['rows'] ?? 5 }}"
                          readonly>{{ $value }}</textarea>
            @endcan
        </div>
    </div>
</div>
