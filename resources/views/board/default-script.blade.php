<script>
    const dataUrl = '{{ route('board.data', ['code' => $code]) }}';
    const boardCode = '{{ $code }}';
    const boardConfig = @json($boardConfig);
    const popupMinWidth = 600;
    const popupMinHeight = 500;
    const boardForm = '#board-frm';
    const replyForm = '#reply-frm';

    const getPK = (_this) => {
        return $(_this).closest('li').data('sid');
    }
</script>
<script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('plugins/plupload/2.3.6/plupload.full.min.js') }}"></script>
<script src="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/jquery.plupload.queue.min.js') }}"></script>
<script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>
