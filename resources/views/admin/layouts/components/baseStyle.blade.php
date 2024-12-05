{{-- base css --}}
<link rel="icon" href="/favicon.ico">
<link rel="stylesheet" href="/assets/css/admin/slick.css">
<link rel="stylesheet" href="/assets/css/admin/admin.css">
<link rel="stylesheet" href="{{ asset('plugins/flatpickr/css/flatpickr.min.css') }}">

<style>
    select {border: 1px solid #ccc;}
    form select {height: 30px;}
</style>

{{-- addCss --}}
@yield('addStyle')
