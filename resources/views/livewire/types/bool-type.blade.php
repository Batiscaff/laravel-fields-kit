<div class="card card-info">
    <div class="card-header">
        {{ __('fields-kit::edit.field-content') }}
    </div>
    <div class="card-body">
        <div class="field-group">
            <label for="field-value">{{ __('fields-kit::edit.field-value') }}</label>
            @can(config('fields-kit.permission.peculiar-field.update-value'))
                <select class="form-control"
                        id="field-value"
                        wire:model="value"
                >
            @else
                <select class="form-control" readonly>
            @endcan
                    <option value="1">{{ __('fields-kit::section.yes') }}</option>
                    <option value="0">{{ __('fields-kit::section.no') }}</option>
                </select>
        </div>
    </div>
</div>
