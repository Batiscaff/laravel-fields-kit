<div class="card card-info">
    <div class="card-header">
        {{ __('fields-kit::edit.field-content') }} @livewire('peculiar-field-language-flag')
    </div>
    <div class="card-body">
        <div class="field-group">
            <label for="field-value">{{ __('fields-kit::edit.field-value') }}</label>
            @if(!empty($this->currentField->settings['multistring']))
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
            @else
                @can(config('fields-kit.permission.peculiar-field.update-value'))
                    <input type="text"
                           class="form-control"
                           id="field-value"
                           wire:model="value">
                @else
                    <input type="text" class="form-control" id="field-value" value="{{ $value }}" readonly>
                @endcan
            @endif
        </div>
    </div>
</div>
