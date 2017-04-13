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
							<p>Work of the application <a href="https://www.screencast.com/t/5FBJgIv5">Screencast</a></p>
							<p>
								<object id="scPlayer" class="embeddedObject" width="1920" height="1040" type="application/x-shockwave-flash" data="https://content.screencast.com/users/meatmanFS/folders/Jing/media/2a1b8ac7-b68a-4bcc-b8d9-4c8d75b545ec/jingswfplayer.swf" style="width:1238.769230769231px; height:671px;">
									<param name="movie" value="https://content.screencast.com/users/meatmanFS/folders/Jing/media/2a1b8ac7-b68a-4bcc-b8d9-4c8d75b545ec/jingswfplayer.swf">
									<param name="quality" value="high">
									<param name="bgcolor" value="#FFFFFF">
									<param name="flashVars" value="containerwidth=1920&amp;containerheight=1040&amp;thumb=https://content.screencast.com/users/meatmanFS/folders/Jing/media/2a1b8ac7-b68a-4bcc-b8d9-4c8d75b545ec/FirstFrame.jpg&amp;content=https://content.screencast.com/users/meatmanFS/folders/Jing/media/2a1b8ac7-b68a-4bcc-b8d9-4c8d75b545ec/2017-04-11_1448.swf&amp;blurover=false">
									<param name="allowFullScreen" value="true">
									<param name="scale" value="exactfit">
									<param name="allowScriptAccess" value="always">
									<param name="wmode" value="opaque">
									<param name="base" value="https://content.screencast.com/users/meatmanFS/folders/Jing/media/2a1b8ac7-b68a-4bcc-b8d9-4c8d75b545ec/">
								</object>								
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
