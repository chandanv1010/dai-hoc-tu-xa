<div class="post-content-wrapper-full">
    <article class="post-article">
        <div class="post-content">
            <h1 class="post-title">{{ $postName }}</h1>
            @if($postDescription)
                <div class="post-description">
                    <strong>{!! $postDescription !!}</strong>
                </div>
            @endif
            @if($postPivot && $postPivot->canonical !== 'lich-khai-giang-du-kien')
                <x-table-of-contents :content="$contentWithToc" />
            @endif
            {!! $postContent !!}
            
            @if($postPivot && $postPivot->canonical !== 'lich-khai-giang-du-kien')
                <!-- Social Share Buttons -->
                <div class="post-social-share">
                    <div class="social-share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" rel="noopener noreferrer" class="social-share-btn social-facebook" title="Chia sẻ trên Facebook">
                            <i class="fa fa-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" class="social-share-btn social-twitter" title="Chia sẻ trên Twitter">
                            <i class="fa fa-twitter"></i>
                        </a>
                        <a href="mailto:?subject={{ $encodedTitle }}&body={{ $encodedUrl }}" class="social-share-btn social-email" title="Chia sẻ qua Email">
                            <i class="fa fa-envelope"></i>
                        </a>
                        <a href="https://pinterest.com/pin/create/button/?url={{ $encodedUrl }}&description={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" class="social-share-btn social-pinterest" title="Chia sẻ trên Pinterest">
                            <i class="fa fa-pinterest"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $encodedUrl }}&title={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" class="social-share-btn social-linkedin" title="Chia sẻ trên LinkedIn">
                            <i class="fa fa-linkedin"></i>
                        </a>
                        <a href="https://www.tumblr.com/widgets/share/tool?canonicalUrl={{ $encodedUrl }}&title={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" class="social-share-btn social-tumblr" title="Chia sẻ trên Tumblr">
                            <i class="fa fa-tumblr"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </article>
</div>

