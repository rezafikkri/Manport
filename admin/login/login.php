<?php 
	include '../../init.php'; 
	$dbL = new login;
	if($dbL->cekLoginYes_forHalamanLogin() === true) die;

	$errors = $dbL->get_form_errors();
	$old = $dbL->get_old_value();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login - Management Raport</title>
	<?php include '../../header.php'; ?>
</head>
<body class="bgGreen">
	<!-- progress bar -->
	<div class="progress_bar_back login_progress">
		<div id="mybar" class="progress_bar green default"></div>
	</div>
	<div class="loginAdmin">
		<form action="proses.php" id="form" method="post">
			<div class="login_top">
				<h1 class="marginBottom20px">MANPORT Admin</h1>
				<p class="command marginBottom40px">Silahkan masukkan <span class="green">username</span> dan <span class="green">password</span> kamu!</p>
				<input type="hidden" name="tokenCSRF" value="<?= $dbL->generate_tokenCSRF(); ?>">
				<?= $dbL->pesan_login(); ?>
				<?= $errors['username']??''; ?>
				<input autofocus type="text" name="username" placeholder="Username ..." value="<?= $old['username']??''; ?>">
				<?= $errors['password']??''; ?>
				<input type="password" name="password" placeholder="Password ..." value="<?= $old['password']??''; ?>">

				<button class="button green marginTop20px" type="submit"><span class="fa fa-sign-in"></span> Masuk</button>
			</div><!-- login_top -->
			<p class="copyRight marginTop20px">Copyright &copy; <?= date('Y'); ?> Reza Sariful Fikri. All Rights Reserved</p>
		</form>
	</div>

</body>
</html>