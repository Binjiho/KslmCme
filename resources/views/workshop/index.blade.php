@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-visual">
        <div class="sub-visual-con inner-layer">
            <h2 class="sub-visual-tit">학술자료실</h2>
            <p>
                학술자료실을 확인하실 수 있습니다.
            </p>
            <div class="breadcrumb">
                <a href="/" class="btn btn-home"><span class="hide">HOME</span></a>
                <ul>
                    <li>학술자료실</li>
                </ul>
            </div>
        </div>
    </article>

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">
            <form id="searchF" name="searchF" action="{{ route('workshop') }}" class="sch-form-wrap">
                <input type="hidden" name="sort" value="{{ request()->sort ?? 'desc' }}">
                <fieldset>
                    <legend class="hide">검색</legend>
                    <ul class="write-wrap">
                        <li>
                            <div class="form-tit">유형</div>
                            <div class="form-con">
                                <div class="checkbox-wrap type2">
                                    @foreach($workshopConfig['category'] as $key => $val)
                                        <label for="chk1_{{$key}}" class="checkbox-group"><input type="checkbox" name="category[]" id="chk1_{{$key}}" value="{{ $key }}" {{ in_array($key , request()->category ?? []) ? 'checked' : '' }}>{{ $val }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form-tit">학술대회 구분</div>
                            <div class="form-con">
                                <div class="checkbox-wrap type2">
                                    @foreach($workshopConfig['gubun'] as $key => $val)
                                        <label for="chk2_{{$key}}" class="checkbox-group"><input type="checkbox" name="gubun[]" id="chk2_{{$key}}" value="{{ $key }}" {{ in_array($key , request()->gubun ?? []) ? 'checked' : '' }}>{{ $val }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form-tit">교육분야</div>
                            <div class="form-con">
                                <div class="checkbox-wrap type2">
                                    @foreach($workshopConfig['field'] as $key => $val)
                                        <label for="chk3_{{$key}}" class="checkbox-group"><input type="checkbox" name="field[]" id="chk3_{{$key}}" value="{{ $key }}" {{ in_array($key , request()->field ?? []) ? 'checked' : '' }}>{{ $val }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form-tit">상세검색</div>
                            <div class="form-con">
                                <div class="sch-wrap">
                                    <div class="form-group">
                                        <input type="text" name="search_key" id="search_key" class="form-item sch-key" placeholder="검색어를 입력해주세요. (행사명, 강의명, 초록명, 저자명, 저자 소속 등)" value="{{ request()->search_key ?? '' }}">
                                        <button type="submit" class="btn btn-sch"><span class="hide">검색</span></button>
                                        {{--                                        <button type="reset" class="btn btn-reset"><img src="/assets/image/icon/ic_reset.png" alt="">필터초기화</button>--}}
                                        <a href="{{ route('workshop') }}" class="btn btn-reset"><img src="/assets/image/icon/ic_reset.png" alt="">필터초기화</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        
                    </ul>
                </fieldset>

                {{--include.form--}}
                @if($isRequest)
                    @include('workshop.form.result_form')
                @else
                    @include('workshop.form.search_form')
                @endif
                {{--//incldue.form--}}

            </form>


            {{ $list->links('pagination::custom') }}
        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const dataUrl = '{{ route('workshop.data') }}';
        const form = '#searchF';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.change-heart', function() {
            const ajaxData = {
                'wsid': $(this).data('wsid'),
                'case': 'change-heart',
                'target': $(this).hasClass('on') ? 'Y':'N',
                'type': 'W',
            };

            callAjax(dataUrl, ajaxData);
        });

        $(document).on('click', '.sort', function() {
            $("input[name='sort']").val($(this).data('sort'));
            $(form).submit();
        });
    </script>
@endsection
