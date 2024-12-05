{{--@php--}}
{{--    dd($popup);--}}
{{--@endphp--}}
{{--    <div class="win-popup-wrap popup-wrap type0" style="top: {{ $popup->position_y }}px; left: {{ $popup->position_x }}px; width: {{ $popup->width }}px; height: {{ $popup->height }}px; display: block;" id="popup_{{ $popup->sid }}">--}}

@php
    $main_pop = strpos(request()->url(),'/main_popup');
@endphp
@if($main_pop !== false)
    <script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="/assets/js/jquery-ui.min.js"></script>
    <script src="{{ asset('plugins/plupload/2.3.6/plupload.full.min.js') }}"></script>
    <script src="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/jquery.plupload.queue.min.js') }}"></script>
@endif
<!-- popup css -->
<link rel="stylesheet" href="{{ asset('assets/css/popup.css') }}">

    <div class="win-popup-wrap popup-wrap type0" style="display: block;" id="popup_{{ $board->sid }}">
        <div class="popup-contents">
            <div class="popup-conbox">

                <div class="popup-tit-wrap">
                    <h3 class="popup-tit">
                        {!! $popup->subject !!}
                    </h3>
                </div>

                <!-- content -->
                <div class="view-contents editor-contents">

                    {!! $board->contents ?? $popup->popup_contents !!}

                    @if($popup->popup_detail === "Y")
                        <div class="btn-wrap text-center" style="text-align: center;">
                            <a href="{{ $popup->popup_link ?? '' }}" target="_blank" class="btn btn-pop-link">자세히보기</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- //content -->

        <!-- popClose -->
        <div class="popup-footer">
{{--            <div class="checkbox-wrap">--}}
{{--                <input type="checkbox" name="popup_yn" id="popup_yn_{{ $popup->sid }}" value="Y">--}}
{{--                <label for="popup_yn_{{ $popup->sid }}">오늘하루 그만보기</label>--}}
{{--            </div>--}}
            <a href="#n" class="btn-pop-today-close full-left"  data-sid="{{ $popup->sid }}">오늘하루 그만보기</a>
            <a href="#n" class="popup_close_btn full-right"  data-sid="{{ $popup->sid }}">닫기</a>
        </div>
        <!-- //popClose -->
    </div>


<script>
    $(document).on('click', '.popup_close_btn', function () {
        self.close();
    });

    @if($main_pop !== false)
        $(document).on('click', '.btn-pop-today-close', function () {
            const layer = $(this).closest('.win-popup-wrap');

            setCookie24(layer.attr('id'), 'done', 1);

            self.close();
        });
   @endif

    function setCookie24(name, value, expiredays) {
        var todayDate = new Date();

        todayDate.setDate(todayDate.getDate() + expiredays);

        document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";";
    }
</script>