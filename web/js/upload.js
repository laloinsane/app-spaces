$(function(){

	$('[data-toggle="tooltip"]').tooltip(); 

	$('#btn').click(function () {
		$('.myprogress').css('width', '0');
        $('.msg').text('');
        
       	var myfile = $('#myfile').val();
        if (myfile == '') {
            alert('Please enter file name and select file');
            return;
        }

		var file = $('#myfile')[0].files[0];
        var id = $('#myfile').data("taller-id");

        var formData = new FormData();
        formData.append('file', file);
        console.info("file", formData.getAll("file"));
        formData.append('id', id);
        formData.append('name', file.name);
        formData.append('type', file.type);

        $('#btn').attr('disabled', 'disabled');
        $('.msg').text('Uploading in progress...');

 		$.ajax({
            url: '../taller/upload',
            data: formData,
            processData: false,
            contentType: false,
            type: 'POST',

            xhr: function () {
            	var xhr = new window.XMLHttpRequest();

                xhr.upload.onprogress = function(e){
                    var done = e.position || e.loaded, total = e.totalSize || e.total;
                    var present = Math.floor(done/total*100)
                    $('.myprogress').text(present + '%');
                    $('.myprogress').css('width', present + '%');
                }
                return xhr;
            },
            success: function (data) {
                $('.msg').text(data);
                $('#btn').removeAttr('disabled');
                location.reload();
            }
        })
	})

});