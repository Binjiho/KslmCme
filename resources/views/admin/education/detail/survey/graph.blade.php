@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="popup-tit-wrap">
        <h3 class="popup-tit">설문 통계</h3>
    </div>

    <div class="popup-conbox">
        <ul class="quiz-list">
            <li>
                <div class="question">
                    <p>
                        {{ $survey->quiz ?? '' }}
                    </p>
                </div>

                @if($survey->gubun == 'A'/*객관식*/)
                    <div class="answer">
                        <ul class="statistics-list">
                            @for($i=1; $i<=5; $i++)
                                @php
                                    $survey_item = 'quiz_item_'.$i;
                                    if(empty($survey->{$survey_item})) continue;
                                @endphp
                                <li class="{{ $survey->answer == $i ? 'active' : '' }}">
                                    <p class="tit">{{ $i }}. {{ $survey->{$survey_item} ?? '' }}</p>
                                    <div class="progress">
                                        <div class="bar">
                                            <span class="percent" style="width: {{$survey->survey_static($i,'percent')}}%;"></span>
                                        </div>
                                        <span>{{$survey->survey_static($i,'percent')}}%</span>
                                    </div>
                                </li>
                            @endfor
                        </ul>
                    </div>
                @else
                    @foreach($survey->survey_view_cnt('get') as $answer)
                        <div class="answer" style="border-bottom: 1px solid #dcdcdc;">
                            ({{ $answer->user->uid }}) - {{ $answer->answer ?? '' }}
                        </div>
                    @endforeach
                @endif
            </li>
        </ul>

        <div class="btn-wrap text-center">
            <a href="javascript:window.close();" class="btn btn-type1 color-type4">확인</a>
        </div>

    </div>
@endsection

@section('addScript')
{{--        <script src="{{ asset('plugins/progressbar/progressbar.js') }}"></script>--}}
{{--        <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>--}}
    {{--    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>--}}

    <script>
        const form = '#mail-frm';
        const dataUrl = $(form).attr('action');

        $(document).on('click', '.file_del', function() {
            let ajaxData = {};
            ajaxData.case = 'file-delete';
            ajaxData.fileType = $(this).data('type');
            ajaxData.filePath = $(this).data('path');

            actionConfirmAlert('삭제 하시겠습니까?', {'ajax': actionAjax(dataUrl, ajaxData)});
        });

        var bar = new ProgressBar.Line(container, {
            strokeWidth: 4,
            easing: 'easeInOut',
            duration: 1400,
            color: '#FFEA82',
            trailColor: '#eee',
            trailWidth: 1,
            svgStyle: {width: '100%', height: '100%'},
            text: {
                style: {
                    // Text color.
                    // Default: same as stroke color (options.color)
                    color: '#999',
                    position: 'absolute',
                    right: '0',
                    top: '30px',
                    padding: 0,
                    margin: 0,
                    transform: null
                },
                autoStyleContainer: false
            },
            from: {color: '#FFEA82'},
            to: {color: '#ED6A5A'},
            step: (state, bar) => {
                bar.setText(Math.round(bar.value() * 100) + ' %');
            }
        });

        bar.animate(1.0);  // Number from 0.0 to 1.0


    </script>
@endsection
