{{--@php--}}
{{--    if( $_SERVER['REMOTE_ADDR']=="218.235.94.247") {--}}
{{--        echo "<pre>"; print_r($main_menu ?? ''); echo "</pre>";--}}
{{--        echo "<pre>"; print_r($sub_menu ?? ''); echo "</pre>";--}}
{{--        echo "<pre>"; print_r($low_menu ?? ''); echo "</pre>";--}}
{{--        echo "<pre>"; print_r('===========--=='); echo "</pre>";--}}
{{--    }--}}
{{--@endphp--}}

<div class="sub-tab-wrap type3">
    <ul class="sub-tab-menu">
        @foreach($menu['sub'][$main_menu] ?? [] as $skey => $sval)
            <li class="{{ ($sub_menu ?? '') == $skey ? 'on':'' }}"><a href="{{ empty($sval['url']) ? route($sval['route'], $sval['param']) : $sval['url'] }}"><span>{{ $sval['name'] }}</span></a></li>
        @endforeach
    </ul>
</div>

@if(!empty($menu['low'][$main_menu][$sub_menu]))
<div class="sub-tab-wrap type1">
    <ul class="sub-tab-menu">
        @foreach($menu['low'][$main_menu][$sub_menu] ?? [] as $lkey => $lval)
            <li class="{{ ($low_menu ?? '') == $lkey ? 'on':'' }}"><a href="{{ empty($lval['url']) ? route($lval['route'], $lval['param']) : $lval['url'] }}">{{ $lval['name'] }}</a></li>
        @endforeach
    </ul>
</div>
@endif