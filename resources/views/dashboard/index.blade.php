@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
                <div class="panel-body">
					@if( !$access_token )
					<div class="alert alert-danger" role="alert">
						Please go to the <a href="{{url('/dashboard/settings')}}">Settings</a> and setup application!
					</div>
					@else
					<div class="alert alert-success vk-api-import-success"></div>
					<div class="alert alert-danger vk-api-import-errror"></div>
					<div class="row">
						<div class="col-md-10">
							<div class="progress">
								<div class="progress-bar progress-bar-success progress-bar-striped start-vk-api-import-progress" role="progressbar"
									aria-valuenow="{{ $persent }}" 
									aria-valuemin="0" aria-valuemax="100" 
									style="width:{{ $persent }}%">
									{{ round( $items_сount /( $items_number / 100 ), 2 )}}%
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<button type="button" class="btn btn-primary btn-xs active start-vk-api-import">Start Import</button>
						</div>
					</div>                    
					@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>    
    var dashboardImportData = {!! json_encode($import_data) !!};
</script>

<script type="text/javascript" src="{{url('/assets/js/dashboard.js')}}" ></script>
@endsection
