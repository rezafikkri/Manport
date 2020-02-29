$(function(){

	const container = document.querySelector(".container-big");

	container.addEventListener('click', function(e){
		e.stopPropagation();

		let href = e.target.getAttribute('href');
		let targetMenu = e.target.nextElementSibling;
		// jika href kosong, maka cari parentnya
		if(href == null) href = e.target.parentElement.getAttribute('href');
		// jika target menu kosong maka cari diparentnya
		if(targetMenu == null) targetMenu = e.target.parentElement.nextElementSibling;

		// prevent default
		if(href == 'dropdownAuto' || href == 'dropdownManual') {
			e.preventDefault();
		}

		if( href == 'dropdownAuto' && targetMenu.getAttribute('target-menu') == 'auto') {
			targetMenu.classList.toggle('muncul');

		} else if(href == 'dropdownManual') {
			let classMenu = e.target.getAttribute('target-menu');
			if(classMenu == null) {
				classMenu = e.target.parentElement.getAttribute('target-menu');
			}
			document.querySelector("."+classMenu).classList.toggle('muncul');
		}
	})

	$("body").click(function(e){
		let cek = e.target.classList.contains('sweet-alert');
		if(cek == false) {
			if(e.target.offsetParent != null) {
				cek = e.target.offsetParent.classList.contains("sweet-alert");
				if(cek == false) {
					if(e.target.offsetParent.offsetParent != null) {
						cek = e.target.offsetParent.offsetParent.classList.contains("sweet-alert");
					} else {
						cek = false;
					}
				}
			} else {
				cek = false;
			}
		}
		
		if(cek != true) {
			$(".menuheader ul").removeClass("muncul");
			$(".menu_siswa").removeClass("muncul");
		}
	})
})