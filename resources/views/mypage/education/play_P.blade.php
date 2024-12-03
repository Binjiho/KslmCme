@extends('layouts.web-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <article class="sub-contents">
        <div class="sub-conbox edu-view">
            <div class="view-wrap inner-layer">
                <h3 class="edu-view-tit">
                    {{--<strong>[교육]</strong>--}} {{ $sac_info->edu->title ?? '' }}
                </h3>
                <div class="btn-wrap text-right">
                    <a href="{{ route('mypage.education.detail',['ssid'=>$sac_info->sid]) }}" class="btn btn-type1 color-type6">강의 목록으로 이동 <img src="/assets/image/sub/ic_btn_arrow.png" alt="" class="arrow"></a>
                </div>
                <div class="view-conbox">

                    <input type="hidden" name="ssid" id="ssid" value="{{ $sac_info->sid ?? '' }}" readonly>
                    <input type="hidden" name="lsid" id="lsid" value="{{ $lecture->sid ?? '' }}" readonly>
                    <input type="hidden" name="esid" id="esid" value="{{ $sac_info->edu->sid ?? '' }}" readonly>
                    <input type="hidden" name="pdf_percent" id="pdf_percent" value="{{ $lecture_view->pdf_percent ?? 0 }}" readonly>
                    <input type="hidden" name="complete_status" id="complete_status" value="{{ $lecture_view->complete_status ?? 'N' }}" readonly>

                    <canvas class="play-wrap" id="pdf-canvas">

                    </canvas>

                    <div class="play-info text-right">
                        이동 버튼을 클릭하여 PDF 페이지를 이동해주세요.
                        <div class="btn-wrap">
                            <strong>Page <span id="page-num"></span>/<span id="page-count"></span></strong>
                            <a href="javascript:;" class="btn btn-arrow btn-prev" id="prev-page">◀<span class="hide">이전</span></a>
                            <a href="javascript:;" class="btn btn-arrow btn-next" id="next-page">▶<span class="hide">다음</span></a>
                        </div>
                    </div>

                    <div class="lecture-tit-wrap">
                        <h4 class="lecture-tit">{{ $lecture->title ?? '' }}</h4>
                        <p class="name">{{ $lecture->name_kr ?? '' }} / {{ $lecture->sosok_kr ?? '' }}</p>
                    </div>
                    <div class="progress-box">
                        <div class="progress">
                            <strong>수강진도율</strong>
                            <div class="bar">
                                <span class="percent" style="width: {{ ($lecture_view->pdf_percent ?? 0) == 100 ? 100 : 0 }}%;"></span>
                            </div>
                            <span class="percent_text">{{ ($lecture_view->pdf_percent ?? 0) == 100 ? 100 : 0 }}%</span>
                        </div>
                        <p>
                            강의 수강을 완료한 경우, 수상 진도율이 100%가 된 후 교육 목록으로 이동해주세요.
                        </p>
                    </div>
                    <div class="btn-wrap text-center">
                        <a href="{{ route('mypage.education.detail',['ssid'=>$sac_info->sid]) }}" class="btn btn-type1 btn-round color-type7"><img src="/assets/image/sub/ic_power.png" alt=""> 강의 종료 (강의 목록으로 이동)</a>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endsection

@section('addScript')
    {{--    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>--}}
    {{--    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.6.172/pdf.min.js"></script>

    <script>
        const dataUrl = '{{ route('mypage.education.data') }}';

        const url = '{{ $lecture->realfile1 ?? '' }}';  // PDF 파일 경로 지정

        let pdfDoc = null;
        let currentPage = 1;
        let totalPages = 0;
        const pdf_percent = $("input[name='pdf_percent']").val();
        const scale = 1.5; // 확대/축소 비율
        const canvas = document.getElementById('pdf-canvas');
        const context = canvas.getContext('2d');

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.6.172/pdf.worker.min.js';

        // PDF 페이지 렌더링 함수
        const renderPage = async (pageNum) => {
            const page = await pdfDoc.getPage(pageNum);
            const viewport = page.getViewport({ scale });
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            await page.render(renderContext).promise;

            // 현재 페이지 번호 업데이트
            document.getElementById('page-num').textContent = pageNum;

            if( $("input[name='complete_status']").val() == 'N'){
                //퍼센트 계산
                const percent = Math.round((pageNum / totalPages) * 100);
                $(".percent").css("width",percent+"%");
                $(".percent_text").html(percent+"%");

                // 마지막 페이지 확인 함수
                const isLastPage = () => {
                    return currentPage === totalPages;
                };

                //수강완료 함수
                if (isLastPage()) {
                    let ajaxData = {
                        'case': 'pdf-finish',
                        'ssid': $("input[name='ssid']").val(),
                        'lsid': $("input[name='lsid']").val(),
                        'esid': $("input[name='esid']").val(),
                    };

                    callbackAjax(dataUrl, ajaxData, function (data, error) {
                        if (data) {
                            if (data.result['res'] == "complete") {
                                location.reload();
                            } else if (data.result['res'] == "error") {
                                alert(data.result['msg']);
                                location.reload();
                            }
                        }
                    }, true);
                }else{ // 페이지 넘길때마다
                    let ajaxData = {
                        'case': 'pdf-play',
                        'pdf_percent': percent,
                        'ssid': $("input[name='ssid']").val(),
                        'lsid': $("input[name='lsid']").val(),
                        'esid': $("input[name='esid']").val(),
                    };

                    callbackAjax(dataUrl, ajaxData, function (data, error) {
                        if (data) {
                            if (data.result['res'] == "complete") {
                                location.reload();
                            } else if (data.result['res'] == "error") {
                                alert(data.result['msg']);
                                location.reload();
                            }
                        }
                    }, true);
                }
            }
        };

        // PDF 파일 로드 함수
        const loadPDF = async () => {
            pdfDoc = await pdfjsLib.getDocument(url).promise;
            totalPages = pdfDoc.numPages;
            if(pdf_percent > 0){
                currentPage = Math.ceil((totalPages * pdf_percent) / 100); //pdf_percent로 현재 페이지 계산
            }
            document.getElementById('page-count').textContent = totalPages;
            renderPage(currentPage);
        };

        // 이전 페이지로 이동
        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage <= 1) return;
            currentPage--;
            renderPage(currentPage);
        });

        // 다음 페이지로 이동
        document.getElementById('next-page').addEventListener('click', () => {
            if (currentPage >= totalPages) return;
            currentPage++;
            renderPage(currentPage);
        });

        loadPDF().catch(console.error);
    </script>
@endsection
