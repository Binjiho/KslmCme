@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />

    <style>
        input:disabled {
            background-color: #f0f0f0; /* 회색 배경 */
            color: #ccc; /* 회색 텍스트 */
            border: 1px solid #ddd; /* 연한 회색 테두리 */
        }
    </style>
@endsection

@section('contents')
<div style="padding:25px;">
    <div class="sub-tit-wrap">
        <h3 class="sub-tit">교육 {{ empty($education->sid) ? '등록' : '수정' }}</h3>
    </div>
	
		<form id="mail-frm" method="post" action="{{ route('education.data') }}" data-sid="{{ $education->sid ?? 0 }}" data-case="education-{{ empty($education->sid) ? 'create' : 'update' }}" data-send="N">
			<div class="write-wrap">
				<dl>
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 노출여부</dt>
					<dd>
						<div class="radio-wrap">
							@foreach($educationConfig['hide'] as $key => $val)
								<div class="radio-group">
									<input type="radio" name="hide" id="hide_{{ $key }}" value="{{ $key }}" {{ ($education->hide ?? '') == $key ? 'checked' : '' }}>
									<label for="hide_{{ $key }}">{{ $val }}</label>
								</div>
							@endforeach
						</div>
					</dd>
				</dl>
				<dl>
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 교육 구분</dt>
					<dd>
						<div class="radio-wrap">
							@foreach($educationConfig['category'] as $key => $val)
								<div class="radio-group">
									<input type="radio" name="category" id="category_{{ $key }}" value="{{ $key }}" {{ ($education->category ?? '') == $key ? 'checked' : '' }}>
									<label for="category_{{ $key }}">{{ $val }}</label>
								</div>
							@endforeach
						</div>
					</dd>
				</dl>
				<dl class="gubun_dl" style="{{ ($education->category ?? '') == 'A' ? '' : 'display:none;' }}">
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 학술대회 구분</dt>
					<dd>
						<div class="radio-wrap">
							@foreach($educationConfig['gubun'] as $key => $val)
								<div class="radio-group">
									<input type="radio" name="gubun" id="gubun_{{ $key }}" value="{{ $key }}" {{ ($education->gubun ?? '') == $key ? 'checked' : '' }}>
									<label for="gubun_{{ $key }}">{{ $val }}</label>
								</div>
							@endforeach
						</div>
					</dd>
				</dl>
				<dl>
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 교육명</dt>
					<dd>
						<input type="text" name="title" id="title" value="{{ $education->title ?? '' }}" class="form-item">
					</dd>
				</dl>
				<dl>
					<dt style="text-align: center;">교육소개</dt>
					<dd>
						<textarea name="contents" id="contents" class="tinymce">{{ $education->contents ?? '' }}</textarea>
					</dd>
				</dl>

				<dl>
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 수강조건</dt>
					<dd>
						<div class="radio-wrap">
							@foreach($educationConfig['condition_yn'] as $key => $val)
								<div class="radio-group">
									<input type="radio" name="condition_yn" id="condition_yn_{{ $key }}" value="{{ $key }}" {{ ($education->condition_yn ?? '') == $key ? 'checked' : '' }}>
									<label for="condition_yn_{{ $key }}">{{ $val }}</label>
								</div>
							@endforeach

							<select name="pre_esid" style="width: 33%;" {{ ($education->condition_yn ?? '') == 'N' ? 'disabled' : ''}}>
								<option value="">선행 교육 선택</option>
								@foreach($pre_edu_list as $val)
									<option value="{{ $val->sid }}" {{ ($education->pre_esid ?? '') == $val->sid ? 'selected' : '' }}>{{ $val->title }}</option>
								@endforeach
							</select>
						</div>
					</dd>
				</dl>
				<dl>
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 퀴즈</dt>
					<dd>
						<div class="radio-wrap">
							@foreach($educationConfig['quiz_yn'] as $key => $val)
								<div class="radio-group">
									<input type="radio" name="quiz_yn" id="quiz_yn_{{ $key }}" value="{{ $key }}" {{ ($education->quiz_yn ?? '') == $key ? 'checked' : '' }}>
									<label for="quiz_yn_{{ $key }}">{{ $val }}</label>
								</div>
							@endforeach

							퀴즈 사용 개수
							<select name="quiz_cnt" style="width: 20%;" {{ ($education->quiz_yn ?? '') == 'N' ? 'disabled' : ''}}>
								<option value="">선택</option>
								@for($i=1; $i<=$educationConfig['quiz_cnt']; $i++)
									<option value="{{ $i }}" {{ ($education->quiz_cnt ?? '') == $i ? 'selected' : '' }}>{{ $i }}개</option>
								@endfor
							</select>

							/ 합격 개수
							<select name="pass_cnt" style="width: 20%;" {{ ($education->quiz_yn ?? '') == 'N' ? 'disabled' : ''}}>
								<option value="">선택</option>
								@for($i=1; $i<=$educationConfig['pass_cnt']; $i++)
									<option value="{{ $i }}" {{ ($education->pass_cnt ?? '') == $i ? 'selected' : '' }}>{{ $i }}개</option>
								@endfor
							</select>
						</div>
					</dd>
				</dl>
				<dl>
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 설문</dt>
					<dd>
						<div class="radio-wrap">
							@foreach($educationConfig['survey_yn'] as $key => $val)
								<div class="radio-group">
									<input type="radio" name="survey_yn" id="survey_yn_{{ $key }}" value="{{ $key }}" {{ ($education->survey_yn ?? '') == $key ? 'checked' : '' }}>
									<label for="survey_yn_{{ $key }}">{{ $val }}</label>
								</div>
							@endforeach
						</div>
					</dd>
				</dl>

				<dl>
					<dt style="text-align: center;"> 교육자료</dt>
					<dd>
						<div class="filebox">
							<input class="upload-name form-item" id="thumbnail_text" placeholder="파일 업로드" readonly="readonly">
							<label for="thumbnail">파일 업로드</label>
							<input type="file" id="thumbnail" name="thumbnail" class="file-upload" accept="image/jpg, image/jpeg, image/png" data-accept="jpeg|jpg|png" onchange="fileCheck(this,$('#thumbnail_text'))">

							@if(!empty($education->sid) && $education->realfile)
								<a href="{{ $education->downloadUrl() }}">{{ $education->filename }} (다운)</a>

								<a href="javascript:void(0);" class="file_del" data-type="thumb" data-path="{{ $education->realfile }}"><img src="{{ asset('assets/image/admin/ic_del.png') }}" alt="삭제"></a>
							@endif
						</div>
					</dd>
				</dl>

				<dl >
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 이수증 사용여부</dt>
					<dd>
						<div class="radio-wrap">
							@foreach($educationConfig['certi_yn'] as $key => $val)
								<div class="radio-group">
									<input type="radio" name="certi_yn" id="certi_yn_{{ $key }}" value="{{ $key }}" {{ ($education->certi_yn ?? '') == $key ? 'checked' : '' }}>
									<label for="certi_yn_{{ $key }}">{{ $val }}</label>
								</div>
							@endforeach
						</div>
					</dd>
				</dl>
				<dl class="certi_code_dl" style="{{ ($education->certi_yn ?? '') == 'Y' ? '' : 'display:none;' }}">
					<dt style="text-align: center;"> 이수증 코드값</dt>
					<dd>
						KSLM - <input type="text" name="certi_code" id="certi_code" value="{{ $education->certi_code ?? '' }}" class="form-item" style="width: 50%" noneKo><b style="color: #e95d5d;">영문3 + 숫자3로 입력하세요</b>
					</dd>
				</dl>
				<dl>
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 신청기간</dt>
					<dd style="display: flex;">
						<input type="text" name="regist_sdate" id="regist_sdate" value="{{ !empty($education->regist_sdate) ? (isValidTimestamp($education->regist_sdate) ? $education->regist_sdate : '') : '' }}" class="form-item" datepicker readonly> -
						<input type="text" name="regist_edate" id="regist_edate" value="{{ !empty($education->regist_edate) ? (isValidTimestamp($education->regist_edate) ? $education->regist_edate : '') : '' }}" class="form-item" datepicker readonly {{ ( $education->regist_limit_yn ?? '') == 'N' ? 'disabled':'' }}>
						<div class="checkbox-wrap">
							<div class="checkbox-group">
								<input type="checkbox" name="regist_limit_yn" id="regist_limit_yn" value="N" {{ ( $education->regist_limit_yn ?? '') == 'N' ? 'checked':'' }} >
								<label for="regist_limit_yn">기한없음</label>
							<div>
						</div>
					</dd>
				</dl>
				<dl>
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 수강기간</dt>
					<dd style="display: flex;">
						<input type="text" name="edu_sdate" id="edu_sdate" value="{{ !empty($education->edu_sdate) ? (isValidTimestamp($education->edu_sdate) ? $education->edu_sdate : '') : '' }}" class="form-item datetime" datepicker readonly> -
						<input type="text" name="edu_edate" id="edu_edate" value="{{ !empty($education->edu_edate) ? (isValidTimestamp($education->edu_edate) ? $education->edu_edate : '') : '' }}" class="form-item datetime" datepicker readonly {{ ( $education->edu_limit_yn ?? '') == 'N' ? 'disabled':'' }}>
						<div class="checkbox-wrap">
							<div class="checkbox-group">
								<input type="checkbox" name="edu_limit_yn" id="edu_limit_yn" value="N" {{ ( $education->edu_limit_yn ?? '') == 'N' ? 'checked':'' }} >
								<label for="edu_limit_yn">기한없음</label>
							<div>
						</div>
					</dd>
				</dl>
				<dl>
					<dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 금액</dt>
					<dd style="display: flex;">
						<div class="radio-wrap">
							@foreach($educationConfig['free_yn'] as $key => $val)
								<div class="radio-group">
									<input type="radio" name="free_yn" id="free_yn_{{ $key }}" value="{{ $key }}" {{ ($education->free_yn ?? '') == $key ? 'checked' : '' }}>
									<label for="free_yn_{{ $key }}">{{ $val }}</label>
								</div>
							@endforeach
								<input type="text" name="cost" id="cost" value="{{ $education->cost ?? '' }}" class="form-item" style="width: 50%" onlyNumber>
						</div>
					</dd>
				</dl>

				<dl class="method_dl" style="{{ ($education->free_yn ?? '') == 'Y' ? 'display:none;' : '' }}">
					<dt style="text-align: center;"> 결제방법</dt>
					<dd style="display: flex;">
						<div class="radio-wrap">
							@foreach($educationConfig['pay_method'] as $key => $val)
								<div class="radio-group">
									<input type="radio" name="pay_method" id="pay_method_{{ $key }}" value="{{ $key }}" {{ ($education->pay_method ?? '') == $key ? 'checked' : '' }}>
									<label for="pay_method_{{ $key }}">{{ $val }}</label>
								</div>
							@endforeach
						</div>
					</dd>
				</dl>
				<dl class="bank_dl" style="display: flex; {{ ($education->free_yn ?? '') == 'N' && ($education->pay_method ?? '') == 'B' ? '' : 'display:none;' }}">
					<dt style="text-align: center;"> 은행정보</dt>
					<dd>
						<input type="text" style="width: 30%;" name="bank_name" id="bank_name" value="{{ $education->bank_name ?? '' }}" class="form-item" placeholder="은행명" >
						<input type="text" style="width: 30%;" name="account_name" id="account_name" value="{{ $education->account_name ?? '' }}" class="form-item" placeholder="예금주명" >
						<input type="text" style="width: 30%;" name="account_num" id="account_num" value="{{ $education->account_num ?? '' }}" class="form-item" placeholder="계좌번호" >
					</dd>
				</dl>
				<dl class="info_dl" style="{{ ($education->free_yn ?? '') == 'Y' ? 'display:none;' : '' }}">
					<dt style="text-align: center;"> 결제정보</dt>
					<dd>
						<input type="text" name="pay_info" id="pay_info" value="{{ $education->pay_info ?? '' }}" class="form-item" placeholder="결제 관련 마감일 정보 등을 입력해주세요." >
					</dd>
				</dl>
			</div>

			<div class="btn-wrap text-center">
				<button type="submit" class="btn btn-type1 color-type20" id="submit">{{ empty($education->sid) ? '등록' : '수정' }}</button>
				<a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
			</div>
		</form>
</div>
@endsection

@section('addScript')
    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>
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

        $(document).on('click', 'input[name=category]', function() {
            if ( $(this).val() == 'A' ){
                $(".gubun_dl").show();
            }else{
                $(".gubun_dl").hide();
                $("input[name='gubun']").prop('checked',false);
            }
        });

        $(document).on('click', 'input[name=certi_yn]', function() {
            if ( $(this).val() == 'Y' ){
                $(".certi_code_dl").show();
            }else{
                $(".certi_code_dl").hide();
                $("input[name='certi_code']").val('');
            }
        });

        $(document).on('click', 'input[name=free_yn]', function() {
            if ( $(this).val() == 'Y' ){
                $(".method_dl").hide();
                $(".bank_dl").hide();
                $(".info_dl").hide();
                $("input[name='pay_method']").prop('checked',false);
                $("input[name='bank_name']").val('');
                $("input[name='account_name']").val('');
                $("input[name='account_num']").val('');
                $("input[name='pay_info']").val('');
            }else{
                $(".method_dl").show();
                // $(".bank_dl").show();
                $(".info_dl").show();
            }
        });

        $(document).on('click', 'input[name=pay_method]', function() {
            if ( $(this).val() == 'B' ){
                $(".bank_dl").show();
            }else{
                $(".bank_dl").hide();
            }
        });

        $(document).on('click', '#regist_limit_yn', function() {
            if ( $("input[name='regist_limit_yn']").is(":checked") ){
                $("input[name='regist_edate']").val('');
                $("input[name='regist_edate']").prop("disabled",true);
            }else{
                $("input[name='regist_edate']").prop("disabled",false);
            }
        });
        $(document).on('click', '#edu_limit_yn', function() {
            if ( $("input[name='edu_limit_yn']").is(":checked") ){
                $("input[name='edu_edate']").val('');
                $("input[name='edu_edate']").prop("disabled",true);
            }else{
                $("input[name='edu_edate']").prop("disabled",false);
            }
        });
        $(document).on('click', 'input[name=free_yn]', function() {
            if ( $("input[name='free_yn']:checked").val() == 'Y' ){
                $("input[name='cost']").val('');
                $("input[name='cost']").prop("disabled",true);
            }else{
                $("input[name='cost']").prop("disabled",false);
            }
        });
        $(document).on('click', 'input[name=condition_yn]', function() {
            if ( $(this).val() == 'N' ){
                $("select[name='pre_esid']").val('');
                $("select[name='pre_esid']").prop("disabled",true);
            }else{
                $("select[name='pre_esid']").prop("disabled",false);
            }
        });
        $(document).on('click', 'input[name=quiz_yn]', function() {
            if ( $(this).val() == 'N' ){
                $("select[name='quiz_cnt']").val('');
                $("select[name='quiz_cnt']").prop("disabled",true);
                $("select[name='pass_cnt']").val('');
                $("select[name='pass_cnt']").prop("disabled",true);
            }else{
                $("select[name='quiz_cnt']").prop("disabled",false);
                $("select[name='pass_cnt']").prop("disabled",false);
            }
        });

        defaultVaildation();

        // 게시판 폼 체크
        $(form).validate({
            ignore: ['content', 'popup_content'],
            rules: {
                hide: {
                    checkEmpty: true,
                },
                category: {
                    checkEmpty: true,
                },
                gubun: {
                    checkEmpty: {
                        depends: function (element) {
                            return $("input[name='category']:checked").val() === 'A';
                        }
                    },
                },
                title: {
                    isEmpty: true,
                },
                condition_yn: {
                    checkEmpty: true,
                },
                pre_esid: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='condition_yn']:checked").val() === 'Y';
                        }
                    }
                },
                quiz_yn: {
                    checkEmpty: true,
                },
                quiz_cnt: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='quiz_yn']:checked").val() === 'Y';
                        }
                    }
                },
                pass_cnt: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='quiz_yn']:checked").val() === 'Y';
                        }
                    }
                },
                survey_yn: {
                    checkEmpty: true,
                },
                certi_yn: {
                    checkEmpty: true,
                },
                certi_code: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='certi_yn']:checked").val() === true;
                        }
                    },
                },
                regist_sdate: {
                    isEmpty: true,
                },
                regist_edate: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='regist_limit_yn']").is(":checked") === false;
                        }
                    },
                },
                edu_sdate: {
                    isEmpty: true,
                },
                edu_edate: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='edu_limit_yn']").is(":checked") === false;
                        }
                    },
                },
                free_yn: {
                    checkEmpty: true,
                },
                cost: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='free_yn']:checked").val() === 'N';
                        }
                    },
                },
                pay_method: {
                    checkEmpty: {
                        depends: function (element) {
                            return $("input[name='free_yn']:checked").val() === 'N';
                        }
                    },
                },
                bank_name: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='pay_method']:checked").val() === 'B';
                        }
                    },
                },
                account_name: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='pay_method']:checked").val() === 'B';
                        }
                    },
                },
                account_name: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='pay_method']:checked").val() === 'B';
                        }
                    },
                },
            },
            messages: {
                hide: {
                    checkEmpty: '노출여부를 선택해주세요.',
                },
                category: {
                    checkEmpty: '교육구분을 선택해주세요.',
                },
                gubun: {
                    checkEmpty: '학술대회 구분을 선택해주세요.',
                },
                title: {
                    isEmpty: `교육명을 입력해주세요.`,
                },
                condition_yn: {
                    checkEmpty: '수강조건을 선택해주세요.',
                },
                pre_esid: {
                    isEmpty: '선행교육을 선택해주세요.',
                },
                quiz_yn: {
                    checkEmpty: '퀴즈 사용유무를 선택해주세요.',
                },
                quiz_cnt: {
                    isEmpty: '퀴즈사용개수를 선택해주세요.',
                },
                pass_cnt: {
                    isEmpty: '퀴즈합격개수를 선택해주세요.',
                },
                survey_yn: {
                    checkEmpty: '설문 사용유무를 선택해주세요.',
                },
                certi_yn: {
                    checkEmpty: '이수증 사용유무를 선택해주세요.',
                },
                certi_code: {
                    isEmpty: '이수증 코드값을 입력해주세요.',
                },
                regist_sdate: {
                    isEmpty: '신청기간 시작일을 입력해주세요.',
                },
                regist_edate: {
                    isEmpty: '신청기간 마감일을 입력해주세요.',
                },
                edu_sdate: {
                    isEmpty: '수강기간 시작일을 입력해주세요.',
                },
                edu_edate: {
                    isEmpty: '수강기간 시작일을 입력해주세요.',
                },
                free_yn: {
                    checkEmpty: '금액 유료여부를 선택해주세요.',
                },
                cost: {
                    isEmpty: '금액을 입력해주세요.',
                },
                pay_method: {
                    checkEmpty: '결제방법을 선택해주세요.',
                },
                bank_name: {
                    isEmpty: '은행명을 입력해주세요.',
                },
                account_name: {
                    isEmpty: '예금주명을 입력해주세요.',
                },
                account_name: {
                    isEmpty: '계좌번호를 입력해주세요.',
                },

            },
            submitHandler: function() {
                boardSubmit();
            }
        });

        const boardSubmit = () => {
            let ajaxData = newFormData(form);
            ajaxData.append('contents', tinymce.get('contents').getContent());

            callMultiAjax(dataUrl, ajaxData);
        }


    </script>
@endsection
