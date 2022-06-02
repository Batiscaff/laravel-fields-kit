<div class="card card-info">
    <div class="card-header">
        {{ __('fields-kit::edit.field-content') }}
    </div>
    <div class="card-body">
        <div class="field-group">
            <label for="field-value">{{ __('fields-kit::edit.field-value') }}</label>
            @if($this->currentField->settings['multistring'] == 1)
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
    <div class="card-footer">
        <button class="btn btn-lg btn-success" wire:click="save(true)" wire:loading.attr="disabled">{{ __('fields-kit::edit.save') }}</button>
        <button class="btn btn-lg btn-info" name="apply" wire:click="save()" wire:loading.attr="disabled">{{ __('fields-kit::edit.apply') }}</button>
        <a href="{{ $cancelLink ?? '.' }}" class="btn btn-lg btn-secondary" wire:loading.attr="disabled">{{ __('fields-kit::edit.cancel') }}</a>
    </div>
</div>
