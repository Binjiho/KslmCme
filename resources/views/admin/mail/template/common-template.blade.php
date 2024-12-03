
@if(!empty($files))
    <tr>
        <td style="text-align: left; padding: 20px;">
            @php($preview = $preview ?? false)

            @foreach($files as $key => $row)
                @if($preview)
                    <a href="javascript:alert('미리보기중에는 다운로드 할 수 없습니다.')" style="display: block;border: 0 none;text-decoration: none;color: #000;outline: none;line-height: 20px;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;padding-top: 7px;">
                        <img src="{{ asset('/assets/image/admin/icon/bbs_attach.png') }}" alt="" style="display: inline-block;vertical-align: top;height: 20px;">
                        <span style="display: inline-block;vertical-align: top;color: #000;text-decoration: none;">- {{ $row['filename'] }}</span>
                    </a>
                @else
                    <a href="{{ $row->downloadUrl() }}" style="display: block;border: 0 none;text-decoration: none;color: #000;outline: none;line-height: 20px;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;padding-top: 7px;">
                        <img src="{{ asset('/assets/image/admin/icon/bbs_attach.png') }}" alt="" style="display: inline-block;vertical-align: top;height: 20px;">
                        <span style="display: inline-block;vertical-align: top;color: #000;text-decoration: none;">- {{ $row['filename'] }}</span>
                    </a>
                @endif
            @endforeach
        </td>
    </tr>
@endif

@if(!empty($mail['use_btn']))
    <tr>
        <td style="text-align: center; padding: 20px 225px;">
            @switch($mail['use_btn'])
                @case('1')
                    <a href="{{ $mail['link_url'] }}" style="display: block;border:0 none" target="_blank">
                        <img src="{{ asset('/assets/image/admin/common/mailBtn.png') }}" alt="자세히보기" style="display: block;border:0 none;">
                    </a>
                    @break

                @case('2')
                    <a href="{{ $mail['link_url'] }}" style="display: block;border:0 none;" target="_blank">
                        <img src="{{ asset('/assets/image/admin/common/mailBtn_home.png') }}" alt="홈페이지 바로가기" style="display: block;border:0 none;">
                    </a>
                    @break

                @default
                    @break
            @endswitch
        </td>
    </tr>
@endif
