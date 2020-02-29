$(function(){
	// tampil data select kelas where jurusan
	$("select[name='jurusan']").change(function(){
		const jurusan_id = this.value;
		const statusAjax = document.querySelector("statusAjax");
		const url = this.getAttribute("url_tampil_kelas");
		if(statusAjax.getAttribute("value") == "yes") {
			$.ajax({
				type:"POST",
				url:url,
				data:{jurusan_id:jurusan_id},
				beforeSend:function() {
					$(".progress_bar_back").show();
					$(".progress_bar").css({"width":"90%","transition":"3s"});
					statusAjax.setAttribute("value","ajax");
				},
				success:function(respon) {
					statusAjax.setAttribute("value","yes");
					$(".progress_bar").css({"width":"100%","transition":"1s"});
					$(".progress_bar_back").fadeOut();

					let data;
					try {
						data = JSON.parse(respon);
					} catch(e){}

					if(data != undefined) {
						let hasil = '<option disabled="" selected="">...</option>';
						data.forEach(function(e){
							hasil+= '<option value="'+e.kelas_id+'">'+e.kelas+'</option>';
						});
						$("select[name='kelas']").html(hasil);
					} else {
						$("select[name='kelas']").html('<option disabled="" selected="">...</option>');
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	});
});