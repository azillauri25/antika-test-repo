<!doctype html>
<html lang="en" class="fullscreen-bg">

<head>
    <title>Antika Anggrek | Login</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<!-- favicon -->
	<link rel="icon" href="gambar/logoAntika.png" type="image/png"> 

	<!-- MAIN CSS -->
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



	<!-- GOOGLE FONTS -->
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet"> <!-- font Montserrat-->

	<!-- Koneksi database db_antika-->
	<?php
	include 'admin/konfig.php';
	?>
</head>

<body>
	<!-- cek login -->
	<?php
	if (isset($_GET['pesan'])) {
		if ($_GET['pesan'] == "Gagal") {
			echo "<div style='margin-bottom:-55px' class='alert alert-danger' role='alert'>
			<i class='fa fa-exclamation-triangle'></i> Login Gagal !! Username dan Password Salah !!</div>";
		}
	}
	?>
	<!-- WRAPPER/ card untuk login -->
	<div id="wrapper">
		<div class="vertical-align-wrap">
			<div class="vertical-align-middle">
				<div class="auth-box login">
					<div class="content">
						<div class="header">
							<div class="logo text-center"><h1>LOGIN</h1></div>
							<p class="lead-form">Silahkan masukkan username dan password</p>
						</div>
						<form class="form-auth-small" action="cek_login.php" method="post">
							<div class="form-group">
								<label for="signin-email" class="control-label sr-only">Username</label>
								<input type="text" class="form-control" name="username" placeholder="Username">
							</div>
							<div class="form-group">
								<label for="signin-password" class="control-label sr-only">Password</label>
								<input type="password" class="form-control" name="password" placeholder="Password">
							</div>
							<button type="submit" class="btn btn-primary btn-lg btn-block">LOGIN</button>
							<h6>Belum punya akun?</h6>
							<div class="bottom">
								<a href="registrasi.php" class="text-register"><h5>Registrasi</h5></a>
							</div>
							<div class="bottom back-button">
								<a href="https://api.whatsapp.com/send?phone=6281545863325&text=Halo,%20min%20mau%20tanya%20tentang%20produk%20anggrek%20dong!" class="text-register">
									<h5>Lupa Password?</h5>
								</a>
							</div>
							<div class="bottom back-button">
								<a href="index.php" class="text-register"><h5>Kembali</h5></a>
							</div>
						</form>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END WRAPPER -->

</body>

</html>
