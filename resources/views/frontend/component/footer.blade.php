@php
    // Lấy dữ liệu từ system
    $logo = $system['homepage_logo'] ?? '';
    $logoUrl = $logo ? (is_array($logo) ? asset($logo[0]) : asset($logo)) : asset('frontend/resources/img/logo.png');
    
    $email = $system['contact_email'] ?? '';
    $address = $system['contact_address'] ?? ($system['contact_office'] ?? '');
    $hotline = $system['contact_hotline'] ?? '';
    $copyright = $system['homepage_copyright'] ?? '';
    
    // Lấy menu footer
    $footerMenu = $menu['footer-menu'] ?? ($menu['footer-menu_array'] ?? []);
@endphp

<footer id="footer" class="panel-footer">
    <div class="uk-container uk-container-center">
        <div class="footer-wrapper">
            <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                <!-- Left Column: Logo & Contact Info -->
                <div class="uk-width-medium-1-2 uk-width-large-1-3">
                    <div class="footer-left">
                        @if($logoUrl)
                            <div class="footer-logo">
                                <a href="{{ config('app.url') }}">
                                    <img src="{{ $logoUrl }}" alt="Logo">
                                </a>
                            </div>
                        @endif
                        <div class="footer-description">
                            Nền tảng kết nối học viên với các trường đại học uy tín, đào tạo từ xa chất lượng cao.
                        </div>
                        
                        <div class="footer-contact">
                            @if($address)
                                <div class="footer-contact-item">
                                    <i class="fa fa-map-marker"></i>
                                    <span>{{ $address }}</span>
                                </div>
                            @endif
                            
                            @if($hotline)
                                <div class="footer-contact-item">
                                    <i class="fa fa-phone"></i>
                                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $hotline) }}">{{ $hotline }}</a>
                                </div>
                            @endif
                            
                            @if($email)
                                <div class="footer-contact-item">
                                    <i class="fa fa-envelope"></i>
                                    <a href="mailto:{{ $email }}">{{ $email }}</a>
                                </div>
                            @endif
                        </div>
                        <div class="footer-social">
                            <div class="uk-flex uk-flex-middle">
                                <a href="{{ $system['social_facebook'] }}" class="footer-social-item">
                                    <i class="fa fa-facebook"></i>
                                </a>
                                <a href="{{ $system['social_twitter'] }}" class="footer-social-item">
                                    <i class="fa fa-twitter"></i>
                                </a>
                                <a href="{{ $system['social_instagram'] }}" class="footer-social-item">
                                    <i class="fa fa-instagram"></i>
                                </a>
                                <a href="#" class="footer-social-item">
                                    <i class="fa fa-linkedin"></i>
                                </a>
                                <a href="{{ $system['social_youtube'] }}" class="footer-social-item">
                                    <i class="fa fa-youtube"></i>
                                </a>
                                <a href="#" class="footer-social-item">
                                    <i class="fa fa-tiktok"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Footer Menu -->
                <div class="uk-width-medium-1-2 uk-width-large-2-3">
                    <div class="footer-right">
                        @if(!empty($footerMenu) && is_array($footerMenu))
                            <div class="footer-menu">
                                @foreach($footerMenu as $index => $menuItem)
                                    @php
                                        $itemName = '';
                                        $itemCanonical = '';
                                        $itemChildren = [];
                                        $isLastItem = ($index === count($footerMenu) - 1); // Kiểm tra item cuối cùng
                                        
                                        if (isset($menuItem['item'])) {
                                            $item = $menuItem['item'];
                                            if (isset($item->languages) && $item->languages->count() > 0) {
                                                $pivot = $item->languages->first()->pivot;
                                                $itemName = $pivot->name ?? '';
                                                $itemCanonical = $pivot->canonical ?? '';
                                            }
                                            $itemChildren = $menuItem['children'] ?? [];
                                        } else {
                                            $itemName = $menuItem['name'] ?? '';
                                            $itemCanonical = $menuItem['canonical'] ?? '';
                                            $itemChildren = $menuItem['children'] ?? [];
                                        }
                                    @endphp
                                    
                                    <div class="footer-menu-item">
                                        <div class="footer-menu-link">
                                            {{ $itemName }}
                                        </div>
                                        
                                        @if(!empty($itemChildren))
                                            <ul class="footer-submenu">
                                                @foreach($itemChildren as $subItem)
                                                    @php
                                                        $subName = '';
                                                        $subCanonical = '';
                                                        
                                                        if (isset($subItem['item'])) {
                                                            $sub = $subItem['item'];
                                                            if (isset($sub->languages) && $sub->languages->count() > 0) {
                                                                $subPivot = $sub->languages->first()->pivot;
                                                                $subName = $subPivot->name ?? '';
                                                                $subCanonical = $subPivot->canonical ?? '';
                                                            }
                                                        } else {
                                                            $subName = $subItem['name'] ?? '';
                                                            $subCanonical = $subItem['canonical'] ?? '';
                                                        }
                                                    @endphp
                                                    <li>
                                                        @if($isLastItem)
                                                            {{-- Item cuối cùng: dùng span thay vì a (địa chỉ không cần click) --}}
                                                            <span>{{ $subName }}</span>
                                                        @else
                                                            <a href="{{ write_url($subCanonical) }}">
                                                                {{ $subName }}
                                                            </a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            @if($copyright)
                <div class="footer-copyright">
                    <p>{{ $copyright }}</p>
                </div>
            @endif
        </div>
    </div>
</footer>
