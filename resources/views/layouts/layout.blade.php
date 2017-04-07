<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>VK Top Users Charts</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- Le styles -->
		<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
		<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
		</style>
		<link href="/assets/css/bootstrap-responsive.min.css" rel="stylesheet">
		<link rel="shortcut icon" href="/assets/ico/favicon.ico">
	</head>

	<body>

		@include('layouts.nav')

		<div class="container">
			@yield('content')			

			<hr>

			@include('layouts.footer')

		</div> <!-- /container -->
		@yield('scripts')
	</body>
</html>
