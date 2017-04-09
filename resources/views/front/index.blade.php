@extends('layouts.layout')
@section('content')
    <div id="vk-top-users-chart"><i class="fa fa-spinner fa-6 fa-spin" aria-hidden="true"></i></div>
    <div id="vk-top-users-table"><i class="fa fa-spinner fa-6 fa-spin" aria-hidden="true"></i></div>
@endsection

@section( 'scripts' )
<script
  src="https://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous">
</script>
<script>    
    var appData = {!! json_encode($app_data) !!};
</script>
<script type="text/javascript" src="/assets/js/app.js"></script>
@endsection