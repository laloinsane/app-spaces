$(function(){
 
	    /*Cargar el archivo y eviar al controlador*/
		$('#subir-archivo').change(function(evt) {
			$('#nose').html('cargando ...');
			var img = $('#subir-archivo')[0].files[0];
			var id = $(this).data("taller-id");
			console.log(img);
				var filename = this.value.match(/[^\\\/]+$/, '')[0];
				var fd = new FormData();
				fd.append('id', id); //
				fd.append('type', img.type); //
				fd.append('myfile', $('#subir-archivo')[0].files[0]); //archivo de imagen dado por el usuario
				fd.append('filename', filename); //
				//  console.log($('#myfile')[0].files[0]);
				$.ajax({
					url:'../taller/upload',
					data: fd,
					processData: false,
					contentType: false,
					headers: {
						'Content-Type': undefined
					},
					type: 'POST',
					success: function(data) {
						console.log(data)
						$('#nose').html('');
						location.reload();
					},
				}).fail(function(response) {
					alert('Error: ' + response.responseText);
					location.reload();
				})
			//https://stackoverflow.com/questions/15410265/file-upload-progress-bar-with-jquery
		});



});