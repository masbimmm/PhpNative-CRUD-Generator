<?php
//error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Asia/Jakarta');
$HOST = 'localhost';
$DB_USER = 'root';
$DB_PASSWORD = '';
$DB_NAME = 'putecantik';

define ("HOST",$HOST);
define ("DB_USER",$DB_USER);
define ("DB_PASSWORD",$DB_PASSWORD);
define ("DB_NAME",$DB_NAME);
define ("BASE_URL","");
$db = mysqli_connect($HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
if (!$db) {
	die(mysqli_error($db));
}
//SET GMT Buat Tanggal
date_default_timezone_set('Asia/Jakarta');

//FUNGSI ENCRYPT
function enurl($str){
	$encrypted_string=openssl_encrypt($str,"AES-128-ECB",'encrypt:)');
	return base64_encode($encrypted_string);
}
function deurl($str){
	$decrypted_string=openssl_decrypt(base64_decode($str),"AES-128-ECB",'encrypt:)');
	return $decrypted_string;
}
$tanggal = date("Y-m-d H:i:s");
$url =  "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$param = parse_url($url, PHP_URL_QUERY);

function tanggalanjam($tanggal){
	$tahun = substr($tanggal, 0,4);
	$bulan = substr($tanggal, 5,2);
	if ($bulan==1) {
		$bulan = ' Januari ';
	}elseif ($bulan==2) {
		$bulan = ' Februari ';
	}elseif ($bulan==3) {
		$bulan = ' Maret ';
	}elseif ($bulan==4) {
		$bulan = ' April ';
	}elseif ($bulan==5) {
		$bulan = ' Mei ';
	}elseif ($bulan==6) {
		$bulan = ' Juni ';
	}elseif ($bulan==7) {
		$bulan = ' Juli ';
	}elseif ($bulan==8) {
		$bulan = ' Agustus ';
	}elseif ($bulan==9) {
		$bulan = ' September ';
	}elseif ($bulan==10) {
		$bulan = ' Oktober ';
	}elseif ($bulan==11) {
		$bulan = ' Novembar ';
	}elseif ($bulan==12) {
		$bulan = ' Desember ';
	}else{
		$bulan = ' Unk ';
	}
	$jam = ' '.substr($tanggal, -8, 5).' WIB';
	$tanggal = substr($tanggal, 8,2);
	return $tanggal.$bulan.$tahun.$jam;
}
function tanggalan($tanggalan){
	// 2020-05-19 09:23:38
	$tahun = substr($tanggalan, 0,4);
	$bulan = substr($tanggalan, 5,2);
	if ($bulan==1) {
		$bulan = ' Januari ';
	}elseif ($bulan==2) {
		$bulan = ' Februari ';
	}elseif ($bulan==3) {
		$bulan = ' Maret ';
	}elseif ($bulan==4) {
		$bulan = ' April ';
	}elseif ($bulan==5) {
		$bulan = ' Mei ';
	}elseif ($bulan==6) {
		$bulan = ' Juni ';
	}elseif ($bulan==7) {
		$bulan = ' Juli ';
	}elseif ($bulan==8) {
		$bulan = ' Agustus ';
	}elseif ($bulan==9) {
		$bulan = ' September ';
	}elseif ($bulan==10) {
		$bulan = ' Oktober ';
	}elseif ($bulan==11) {
		$bulan = ' Novembar ';
	}elseif ($bulan==12) {
		$bulan = ' Desember ';
	}else{
		$bulan = ' Unk ';
	}
	$tanggal = substr($tanggalan, 8,2);
	return $tanggal.$bulan.$tahun;
}
function cbulan($bulan){
	// 2020-05-19 09:23:38
	if ($bulan==1) {
		$bulan = ' Januari ';
	}elseif ($bulan==2) {
		$bulan = ' Februari ';
	}elseif ($bulan==3) {
		$bulan = ' Maret ';
	}elseif ($bulan==4) {
		$bulan = ' April ';
	}elseif ($bulan==5) {
		$bulan = ' Mei ';
	}elseif ($bulan==6) {
		$bulan = ' Juni ';
	}elseif ($bulan==7) {
		$bulan = ' Juli ';
	}elseif ($bulan==8) {
		$bulan = ' Agustus ';
	}elseif ($bulan==9) {
		$bulan = ' September ';
	}elseif ($bulan==10) {
		$bulan = ' Oktober ';
	}elseif ($bulan==11) {
		$bulan = ' Novembar ';
	}elseif ($bulan==12) {
		$bulan = ' Desember ';
	}else{
		$bulan = ' Unk ';
	}
	return $bulan;
}
function Connect(){
    $connect = mysqli_connect(HOST, DB_USER, DB_PASSWORD,DB_NAME);
    if($connect){
        return $connect;
    } else {
      return FALSE;
    }
}
?>