<?php
	include "koneksi.php";
	$prodi=$_GET['kelas'];
	$nim=$_GET['nim'];
	
    $sql="SELECT * FROM `absensi` WHERE prodi='$prodi' AND nim='$nim'";
	$res_siswa=mysqli_query($koneksi,$sql);
	$berhasil=true;
	while($data=mysqli_fetch_array($res_siswa)){
		$id_absensi= $data[0];
		$jadwal= $data[1];
		$keterangan= $data[2];
		$id_post='ket'.$id_absensi;
		$ket=$_POST[$id_post];
		if($sql_absen=mysqli_query($koneksi,"UPDATE `absensi` SET `keterangan` = '$ket' WHERE `absensi`.`id_absen` = $id_absensi")){
			
		}else{
			
			$berhasil=false;
			echo 'gagal';
		}
	}
	
	if($berhasil){
		?> <script>alert('Ubah Data Berhasil')</script><?php
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=rekapabsensi.php?kelas='.$prodi.'">';		
	}else{
		?> <script>alert('Ubah Data Gagal');history.go(-1);</script><?php		
	}
	
?>