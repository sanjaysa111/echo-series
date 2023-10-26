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
			<form method="post" action="#">
				@csrf
				<div class="form-group">
					<label>Tasks</label>
					<input type="text" class="form-control" name="body" id="body" placeholder="Enter task">
				</div>
				<button type="submit" class="btn btn-primary" id="taskSubmit" >Submit</button>
			</form>
			<ul id="taskList">
				@foreach ($project->tasks as $task)
					<li>
						<h5 class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $task->body }}</h5>
					</li>
				@endforeach
			</ul>
		</div>

		<script>
			$(document).ready( function() {
				let project_id = @json($project->id);
				let url = "{{ route('project.task.create', ":project_id" ) }}";
				url = url.replace(':project_id', project_id);

				$("#taskSubmit").on( "click", function(e) {
					e.preventDefault();
					
					let body = $('#body').val();

					$.ajax({
						url:url,
						method:'POST',
						data:{ 'body' : body },
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						success: function(response) {
							$('#body').val("");

							$( "#taskList" ).prepend ( "<li><h5 class='mt-1 text-xl font-semibold text-gray-900'>"+ response +"</h5></li>" );
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(errorThrown);
						}
					});
				} );
			
				window.Echo.private('tasks.'+project_id).listen('TaskCreatedEvent', ({task}) => {
					$( "#taskList" ).prepend ( "<li><h5 class='mt-1 text-xl font-semibold text-gray-900'>"+ task.body +"</h5></li>" );
				})
			});
		</script>
    </body>
</html>
