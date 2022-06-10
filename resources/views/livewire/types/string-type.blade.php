<div class="card card-info">
    <div class="card-header">
        {{ __('fields-kit::edit.field-content') }}
    </div>
    <div class="card-body">
        <div class="field-group">
            <label for="field-value">{{ __('fields-kit::edit.field-value') }}</label>
            @if(!empty($this->currentField->settings['multistring']))
                <textarea class="form-control"
                          id="field-value"
                          rows="{{ $this->currentField->settings['rows'] ?? 5 }}"
                          wire:model="value"></textarea>
            @else
                <input type="text"
                       class="form-control"
                       id="field-value"
                       wire:model="value">
            @endif
        </div>
    </div>
</div>
