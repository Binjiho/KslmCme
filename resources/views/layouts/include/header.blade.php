<header id="header" class="js-header">
    <div class="header-wrap inner-layer wide">
        <h1 class="header-logo">
            <a href="/"><img src="{{ asset('assets/image/common/h1_logo.png') }}" alt="M2community 2025"></a>
        </h1>
        <nav id="gnb" class="js-gnb">
            <ul class="gnb cf">
                @foreach($menu['main'] as $key => $val)
                    @if($val['continue']) @continue @endif
                    <li>
                        <a href="{{ empty($val['url']) ? route($val['route'], $val['param']) : $val['url'] }}">{{ $val['name'] }}</a>

                        @foreach($menu['sub'][$key] ?? [] as $sKey => $sVal)
                            @if($loop->first)
                            <ul>
                            @endif
                                <li><a href="{{ empty($sVal['url']) ? route($sVal['route'], $sVal['param']) : $sVal['url'] }}">{{ $sVal['name'] }}</a></li>
                            @if($loop->last)
                            </ul>
                            @endif
                        @endforeach
                    </li>
                @endforeach

            </ul>
            <a href="#n" class="btn" target="_blank">대한진단검사의학회 홈페이지 바로가기</a>
            <button type="button" class="btn btn-menu-close js-btn-menu-close"><span class="hide">메뉴 닫기</span></button>
        </nav>
        <div class="util-menu-wrap">
            <ul class="util-menu">
                @if(thisAuth()->check())
                    <li><a href="javascript:logout();">로그아웃</a></li>
                @else
                    <li><a href="{{ route('login') }}">로그인</a></li>
                @endif
                <li><a href="https://www.kslm.org/intro.html" class="btn" target="_blank">대한진단검사의학회</a></li>

                @if(thisAuth()->check() && thisLevel() === 'M')
                    <li class="admin">
                        <a href="{{ env('APP_URL') }}/admin">ADMIN</a>
                    </li>
                @endif
            </ul>
            <button type="button" class="btn btn-menu-open js-btn-menu-open"><span class="hide">메뉴 열기</span></button>
        </div>
    </div>
</header>