<!doctype html>
<html lang="en" class="fullscreen-bg">

<head>
    <title>Antika Anggrek | Registrasi</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- favicon -->
    <link rel="icon" href="gambar/logoAntika.png" type="image/png"> 


    <!-- MAIN CSS -->
	<link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.min.css">
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet"> <!--Montserrat-->


    <!-- Koneksi ke database -->
    <?php 
    include 'admin/konfig.php';
    ?>

</head>

<body>
    <!-- WRAPPER -->
    <div id="wrapper">
        <div class="vertical-align-wrap">
            <div class="vertical-align-middle">
                <div class="auth-box registrasi">
                    <div class="content">
                        <div class="header">
                            <div class="logo text-center"><h1>REGISTRASI</h1></div>
                            <p class="lead">Silahkan isi data diri Anda</p>
                        </div>
                        <form class="form-auth-small" method="post" action="proses_tambah_cust.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label id="label-regis">Nama Lengkap</label>
                                <input type="text" id="namaLengkap" class="form-control" name="nama_customer" placeholder="Nama lengkap" required="">
                            </div>
                            <div class="form-group">
                                <label id="label-regis">Email</label>
                                <input type="email" id="email" class="form-control" name="email_customer" placeholder="Email" required="">
                            </div>
                            <div class="form-group">
                                <label id="label-regis">Nomor Telepon</label>
                                <input type="tel" id="nomorTelepon" class="form-control" name="nomor_telepon_customer" placeholder="Nomor telepon" required="">
                            </div>
                            <div class="form-group">
                                <label id="label-regis">Username</label>
                                <input type="text" id="usernameCust" class="form-control" name="username" placeholder="Username" required="">
                            </div>
                            <div class="form-group">
                                <label id="label-regis">Password</label>
                                <input type="password" id="passwordCust" class="form-control" name="password" placeholder="Password" required="">
                            </div>
                            <div class="form-group">
                                <label id="label-regis">Tanggal Lahir</label>
                                <input type="date" id="tanggalLahir" class="form-control" name="ttl_customer" placeholder="Tanggal lahir" required="">
                            </div>

                            <div class="form-group">
                                <label for="nama_kota">Pilih Kota:</label>
                                <select id="nama_kota" name="nama_kota" class="form-control" required="">
                                    <option value="" disabled selected>-Silahkan pilih kota anda-</option>
                                    <option value="Bekasi">Bekasi</option>
                                    <option value="Bogor Kota">Bogor Kota</option>
                                    <option value="Depok">Depok</option>
                                    <option value="Jakarta Barat">Jakarta Barat</option>
                                    <option value="Jakarta Pusat">Jakarta Pusat</option>
                                    <option value="Jakarta Selatan">Jakarta Selatan</option>
                                    <option value="Jakarta Timur">Jakarta Timur</option>
                                    <option value="Jakarta Utara">Jakarta Utara</option>
                                    <option value="Tangerang">Tangerang</option>
                                    <option value="Tangerang Selatan">Tangerang Selatan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label id="label-regis">Alamat Lengkap</label>
                                <input type="text" id="alamatLengkap" class="form-control" name="alamat_customer" placeholder="Alamat Lengkap" required="">
                            </div>
                            
                            <button id="simpan" type="submit" class="btn btn-primary btn-lg btn-block">Daftar</button>
                            <h6>Sudah punya akun?</h6>
                            <div class="bottom">
                                <a href="login.php" class="text-register"><h5>Login</h5></a>
                            </div>
                            <div class=" bottom back-button">
								<a href="index.php" class="text-register"><h5>Kembali</h5></a>
							</div>
                        </form>
                    </div>
                    <div class="clearfix"></div> <!---untuk ngatur floating biar nggak override sama elemen CSS-->
                </div>
            </div>
        </div>
    </div>
    <!-- END WRAPPER -->
</body>

</html>
