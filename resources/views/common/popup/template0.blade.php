
    <div class="win-popup-wrap popup-wrap type0" style="top: {{ $popup->position_y }}px; left: {{ $popup->position_x }}px; width: {{ $popup->width }}px; height: {{ $popup->height }}px; display: block;" id="popup_{{ $popup->sid }}">
        <div class="popup-contents">
            <div class="popup-conbox">

                <div class="popup-tit-wrap">
                    <h3 class="popup-tit">
                        {!! $popup->subject !!}
                    </h3>
                </div>

                <!-- content -->
                <div class="view-contents editor-contents">

                    {!! $popup->contents ?? $popup->popup_contents !!}

                    @if($popup->popup_detail === "Y")
                        <div class="btn-wrap text-center">
                            <a href="{{ $popup->popup_link ?? '' }}" class="btn btn-pop-link">자세히보기</a>
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
