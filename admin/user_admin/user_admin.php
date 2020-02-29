<?php  if(!class_exists("config")) { die; }

	$db = new user_admin;
	if($db->cekLoginNo_halamanAdmin() === true) die;
	
	$r = $db->tampil_user_admin();
	$errors = $db->get_form_errors();
?>
<div class="col-5 offset-left-3 offset-right-4">
	<div class="home default">
		<h1 class="judul marginBottom20px">Admin</h1>
		<form action="user_admin/proses.php?action=edit_user_admin" method="post" id="form">
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<?= $db->pesan_edit_user_admin(); ?>
			<label class="label">Username</label>
			<?= $errors['username']??''; ?>
			<input type="text" name="username" placeholder="..." value="<?= $r['username']??''; ?>">
			<label class="label">Password</label>
			<?= $errors['password']??''; ?>
			<div class="inputCon">
				<a id="see_not_see_password"><span class="fa fa-lock"></span></a>
				<input type="password" id="password" name="password" placeholder="...">
			</div>
			<label class="label">Password Sekarang</label>
			<?= $errors['passwordNow']??''; ?>
			<div class="inputCon">
				<a id="see_not_see_passwordNow"><span class="fa fa-lock"></span></a>
				<input type="password" id="passwordNow" name="passwordNow" placeholder="...">
			</div>
			<div class="inputCheckbox display_inline_block marginRight10px">
				<input type="checkbox" id="showSimpan">
				<label for="showSimpan"></label>
			</div>
			<label class="label inline">Ceklist untuk menyimpan!</label><br>

			<button type="submit" id="simpan" class="button green display_none marginTop20px"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<script type="text/javascript">
$(function(){
	const checkbox = document.querySelector("input#showSimpan");
	checkbox.addEventListener('click', function(){
		if(this.checked == true) {
			$("button#simpan").removeClass("display_none");
		} else {
			$("button#simpan").addClass("display_none");
		}
	})

	// show hide 
	function showHidePassword(idInput, idA){
		const input = document.querySelector(`input#${idInput}`);
		const type = input.getAttribute('type');
		const span_see_not_see_password = document.querySelector(`a#${idA} span`);
		if(type == "password") {
			input.setAttribute("type","text");
			span_see_not_see_password.classList.replace("fa-lock","fa-unlock-alt");
		} else {
			input.setAttribute("type","password");
			span_see_not_see_password.classList.replace("fa-unlock-alt","fa-lock");
		}
	}

	$("a#see_not_see_password").click(function(){
		showHidePassword('password','see_not_see_password');
	})

	$("a#see_not_see_passwordNow").click(function(){
		showHidePassword('passwordNow','see_not_see_passwordNow');
	})
})
</script>