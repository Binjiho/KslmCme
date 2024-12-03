@if(!empty($main_key))
    <div class="sub-tit-wrap">
        <h3 class="sub-tit">{{ $menu['main'][$main_key]['name'] }} {{ empty($sub_key) ? '' : " - {$menu['sub'][$main_key][$sub_key]['name']}" }}</h3>
    </div>
@endif
