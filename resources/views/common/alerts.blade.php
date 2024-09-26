@if (session()->has('message'))
    <script>
        toastr.options = {
            "progressBar": true,
            "showMethod": 'slideDown',
            "hideMethod": 'slideUp',
            "closeMethod": 'slideUp'
        }
        toastr.info("{{ @session('message') }}", "Info!!");
    </script>
@endif
@if (session()->has('msg-error'))
    <script>
        toastr.options = {
            "progressBar": true,
            "showMethod": 'slideDown',
            "hideMethod": 'slideUp',
            "closeMethod": 'slideUp'
        }
        toastr.error("{{ @session('msg-error') }}", "Error!!");
    </script>
@endif
@if (session()->has('msg-ok'))
    <script>
        toastr.options = {
            "progressBar": true, 
            "showMethod": 'slideDown',
            "hideMethod": 'slideUp',
            "closeMethod": 'slideUp'
        }
        toastr.success("{{ @session('msg-ok') }}", "Ok!!");
    </script>
@endif
@if (session()->has('msg-ok2'))
    <script>
        toastr.options = {
            "progressBar": true, 
            "showMethod": 'slideDown',
            "hideMethod": 'slideUp',
            "closeMethod": 'slideUp'
        }
        toastr.success("{{ @session('msg-ok2') }}", "Ok!!");
    </script>
@endif
@if (session()->has('msg-ops'))
    <script>
        toastr.options = {
            "progressBar": true,
            "showMethod": 'slideDown',
            "hideMethod": 'slideUp',
            "closeMethod": 'slideUp'
        }
        toastr.error("{{ @session('msg-ops') }}", "Ups!!");
    </script>
@endif
@if (session()->has('info'))
    <script>
        toastr.options = {
            "progressBar": true,
            "positionClass": 'toast-top-right'
        }
        toastr.info("{{ @session('info') }}", "Atención!!");
    </script>
@endif
