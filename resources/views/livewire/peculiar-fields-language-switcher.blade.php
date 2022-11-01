@if(isFieldsKitMultilingualEnabled())
    <ul class="nav nav-pills ml-auto">
        <li class="pr-2 mt-2" wire:loading>
            <i class="fas fa-sync fa-spin" style="opacity: 0.5"></i>
        </li>
        @foreach(config('fields-kit.multilingual.languages') as $key => $lang)
        <li class="nav-item">
            <a class="nav-link @if($key == $this->currentLanguage) active @endif"
               href="#lang_{{ $key }}"
               wire:click="setCurrentLanguage('{{ $key }}')"
               data-toggle="tab">
                <img src="{{ $lang['flag'] }}" class="flag">{{ $lang['title'] }}
            </a>
        </li>
        @endforeach
    </ul>
@endif
