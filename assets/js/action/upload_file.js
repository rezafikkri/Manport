//upload file
	$("form#upload_logo").submit(function(e){

		$(".progress_bar_back").show();
		$(".progress_bar").css({"width":"90%","transition":"1.5s"});

		$("#pesan_file").empty();
		e.preventDefault();
		$.ajax({
			type:"POST",
			url:"create_account/proses.php?action=edit_logo",
			contentType: false,
	    	cache: false,
			processData:false,
			data: new FormData(this),
			success:function(html)
			{
				$(".progress_bar").css({"width":"100%","transition":"0s"});
				$(".progress_bar_back").fadeOut();
				if(html==4)
				{
					var pesan = "Kamu tidak memasukkan file apapun";
					$("#pesan_file").html(pesan);
				}
				else if(html==1)
				{
					pesan = "foto hanya boleh berformat png dan ukuran foto maximum 2 Mb";
					$("#pesan_file").html(pesan);
				}
				else{
					var r = document.getElementById('img_logo').src = "assets/img/logo school/"+html;
					console.log(r);
				}
				$(".progress_bar").css({"width":"0%"});
			}
		})
		$(this)[0].reset();
	})