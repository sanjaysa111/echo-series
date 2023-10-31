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
		@vite(['resources/js/app.js'])
		<script>
			var user = @json(auth()->user());
		</script>
    </head>
    <body class="container">
		<div class="row">
			<div class="col-md-8">
				<form method="post" action="#">
					@csrf
					<div class="form-group">
						<label>Tasks</label>
						<input type="text" class="form-control" name="body" id="body" placeholder="Enter task">
						<span id="activePeer"></span>
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
			<div class="col-md-4 mt-4">
				<h5>
					Active Participants
				</h5>
				<ul class="mt-3" id="activeParticipants"></ul>
			</div>
		</div>

		<script>
			$(document).ready( function() {
				let typingTimmer = false;
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
						success: function(body) {
							$('#body').val("");

							$( "#taskList" ).prepend ( "<li><h5 class='mt-1 text-xl font-semibold text-gray-900'>"+ body +"</h5></li>" );
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(errorThrown);
						}
					});
				} );

				window.Echo.join('tasks.'+project_id)
				.here(users => {
					users.forEach(user => {
						$('#activeParticipants').prepend("<li id="+user.id+'_'+user.name+"><h5>"+ user.name +"</h5 ></li>")
					});
				})
				.joining(user => {
					$('#activeParticipants').append("<li id="+user.id+'_'+user.name+"><h5>"+ user.name +"</h5 ></li>")
				})
				.leaving(user => {
					$('#'+user.id+'_'+user.name).remove();
				})
				.listen('TaskCreatedEvent', ({task}) => {
					$( "#taskList" ).prepend ( "<li><h5 class='mt-1 text-xl font-semibold text-gray-900'>"+ task.body +"</h5></li>" );
				})
				.listenForWhisper("typing", (e) => {
					$("#activePeer").html(e.name + " is typing...");

					if(typingTimmer) clearTimeout(typingTimmer);
					
					typingTimmer = setTimeout(() => {
						$('#activePeer').empty();	
					}, 3000);

				});

				$('#body').keydown(function() {
					window.Echo.join('tasks.'+project_id)
					.whisper("typing", {
						name:user.name
					});
				})
			});
		</script>
    </body>
</html>
