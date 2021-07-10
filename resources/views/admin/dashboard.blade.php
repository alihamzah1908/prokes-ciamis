@extends('master')
@section('content')
  <!-- Toggle button -->
<h4 class="card-title">Welcome</h4>
<p class="card-text">Please continue to navigation on the left.</p>
@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
  // Sidebar toggle behavior
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar, #content').toggleClass('active');
        });
    })
</script>
@endpush
<!-- End demo content -->
</body>

</html>
