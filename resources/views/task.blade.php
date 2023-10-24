<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.1.1.min.js">
		@vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="container">
		<div>
			<form method="post" action="{{ route('task.create') }}">
				@csrf
				<div class="form-group">
					<label>Tasks</label>
					<input type="text" class="form-control" name="body" id="body" placeholder="Enter task">
				</div>
				<button type="submit" class="btn btn-primary" id="taskSubmit" >Submit</button>
			</form>
			<ul id="taskList">
				@foreach ($tasks as $task)
					<li>
						<h5 class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $task }}</h5>
					</li>
				@endforeach
			</ul>
		</div>
    </body>
	
	<script>
		$(document).ready( function() {
			$("#taskSubmit").on( "click", function(e) {
				e.preventDefault();
				
				let body = $('#body').val();

				$.ajax({
					url:"{{ route('task.create') }}",
					method:'POST',
					data:{ 'body' : body },
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(response) {
						$('#body').val("");

						$( "#taskList" ).prepend ( "<li><h5 class='mt-1 text-xl font-semibold text-gray-900 dark:text-white'>"+ response +"</h5></li>" );
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					}
				});
			} );
		
			window.Echo.channel('task-created').listen('TaskCreatedEvent', e => {
				$( "#taskList" ).prepend ( "<li><h5 class='mt-1 text-xl font-semibold text-gray-900 dark:text-white'>"+ e.task +"</h5></li>" );
			})
		});
	</script>
</html>
