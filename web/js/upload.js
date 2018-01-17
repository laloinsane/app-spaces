$(function(){
	$('#subir-archivo').click(function(){
		$('#subir-archivo').prop('disabled',true);
		var id = $(this).data("taller-id");
		console.log ($(this).data("taller-id"));

		$.ajax({
			url:'../taller/upload?id='+id,
			type:'post',
			dataType:'text',
			success: function(data){
				console.log (data);
			},
		}).fail(function(response){
			alert ("error")
			console.log (response);
		});

		$('#subir-archivo').prop('disabled',false);
	});
});