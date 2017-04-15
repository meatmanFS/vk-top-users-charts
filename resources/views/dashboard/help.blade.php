@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Help</div>
				<style type="text/css">
					.help-list img, #scPlayer {
						max-width: 100%;
					}
				</style>
                <div class="panel-body">
					<ol class="help-list list-group">
						<li class="list-group-item">
							<ul class="list-group">
								<li class="list-group-item">Create application in <a href="https://vk.com/apps?act=manage">VK</a></li>
								<li class="list-group-item">Set site address (where the application is located)</li>
								<li class="list-group-item"><img src="{{url('/')}}/assets/img/help/step1.png" alt="step one" /></li>
							</ul>							 
						</li>
						<li class="list-group-item">
							<ul class="list-group">
								<li class="list-group-item">Fill the settings</li>
								<li class="list-group-item"><img src="{{url('/')}}/assets/img/help/step2-1.png" alt="step two - one" /></li>
								<li class="list-group-item"><img src="{{url('/')}}/assets/img/help/step2-2.png" alt="step two - two" /></li>
							</ul>	
						</li>
						<li class="list-group-item">
							<ul class="list-group">
								<li class="list-group-item">Login to the VK after setting save</li>
								<li class="list-group-item"><img src="{{url('/')}}/assets/img/help/step3.png" alt="step three" /></li>
							</ul>	
						</li>
						<li class="list-group-item">Start import at the <a href="{{url('/dashboard')}}">Dashboard</a></li>
					</ol>
					<div class="panel panel-default">
						<div class="panel-heading">Results</div>
						<div class="panel-body">
							<p>Work of the application <a href="https://www.youtube.com/watch?v=wTAZwuqUC6U">Screencast</a></p>
							<p>
								<iframe width="560" height="315" src="https://www.youtube.com/embed/wTAZwuqUC6U" frameborder="0" allowfullscreen></iframe>							
							</p>
							<p>
								About the import. I can get more than 300,000 users records , and the VK is start to return the empty response.
								I am suggest to stop import at this point, and start it after some time.
							</p>
							<p>
								About the frontend. There are showing the most common first and second names. So for the example I had 
								the most common name Саша[6653] , and most common second name Мельник[2071].
							</p>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
