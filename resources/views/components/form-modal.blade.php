<!-- Form Modal Component -->
<div id="{{ $modalId }}" class="uk-modal {{ $modalClass }}">
    <div class="uk-modal-dialog download-roadmap-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <!-- Header -->
        <div class="download-roadmap-header">
            <h2 class="download-roadmap-title">{{ $title }}</h2>
            @if(!empty($description))
                <div class="download-roadmap-description">{{ $description }}</div>
            @endif
        </div>
        
        <!-- Wrapper cho script nhúng -->
        @if(!empty($script))
            <div class="download-roadmap-form-wrapper">
                <div class="download-roadmap-script-wrapper">
                    {!! $script !!}
                </div>
            </div>
        @endif
        
        <!-- Footer -->
        @if(!empty($footer))
            <div class="download-roadmap-footer">
                {!! $footer !!}
            </div>
        @endif
    </div>
</div>

