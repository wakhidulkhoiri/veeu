<?php
date_default_timezone_set('Asia/Jakarta');
require_once("sdata-modules.php");
/**
 * @Author: Wakhidul Khoiri, S.Kom
 * @Date:   2018-08-27 09:33:26
 * @Last Modified by:   Wakhidul Khoiri, S.Kom
 * @Last Modified time: 2018-08-27 09:33:26
*/
##############################################################################################################
$config['deviceCode'] 		= '357202073259369';
$config['tk'] 			      = 'ACCcJuIKxBpKvIFnQVvPuIgYtIU7vYLpgvdxdHRodw';
$config['token'] 		      = '7ed6-3gC4Tw1POLYPEDXXqqAuIavffH7SpsJV3AFUU38mCK31qQ1PfKp3wwJAhCNi1CxpxTdPYs4S-U';
$config['uuid'] 		      = '5617f34a008346e1b8a403127782dec7';
$config['sign'] 		      = '4445ed471315aa3d8586358fa92111d4';
$config['android_id'] 		= 'a9b4e64a664415b';
##############################################################################################################
for ($x=0; $x <1; $x++) { 
	$url 	= array(); 
	for ($cid=0; $cid <30; $cid++) { 
		for ($page=0; $page <20; $page++) { 
			$url[] = array(
				'url' 	=> 'http://api.beritaqu.net/content/getList?cid='.$cid.'&page='.$page,
				'note' 	=> 'optional', 
			);
		}
		$ambilBerita = $sdata->sdata($url); unset($url);unset($header);
		foreach ($ambilBerita as $key => $value) {
			$jdata = json_decode($value[respons],true);
			foreach ($jdata[data][data] as $key => $dataArtikel) {
				$artikel[] = $dataArtikel[id];
			}
		}
		$artikel = array_unique($artikel);
		echo "[+] Mengambil data artikel (CID : ".$cid.") ==> ".count(array_unique($artikel))."\r\n";
	}
	while (TRUE) {
		$timeIn30Minutes = time() + 60*120;
		$rnd 	= array_rand($artikel); 
		$id 	= $artikel[$rnd];
		$url[] = array(
			'url' 	=> 'http://api.beritaqu.net/timing/read',
			'note' 	=> $rnd, 
		);
		$header[] = array(
			'post' => 'OSVersion=8.0.0&android_channel=google&android_id='.$config['android_id'].'&content_id='.$id.'&content_type=1&deviceCode='.$config['deviceCode'].'&device_brand=samsung&device_ip=114.124.239.'.rand(0,255).'&device_version=SM-A730F&dtu=001&lat=&lon=&network=wifi&pack_channel=google&time='.$timeIn30Minutes.'&tk='.$config['tk'].'&token='.$config['token'].'&uuid='.$config['uuid'].'&version=10047&versionName=1.4.7&sign='.$config['sign'], 
		);
		$respons = $sdata->sdata($url , $header); 
		unset($url);unset($header);
		foreach ($respons as $key => $value) {
			$rjson = json_decode($value[respons],true);
			echo "[+][".$id." (Live : ".count($artikel).")] Message : ".$rjson['message']." | Poin : ".$rjson['data']['amount']." | Read Second : ".$rjson['data']['current_read_second']."\r\n";
			if($rjson[code] == '-20003' || $rjson['data']['current_read_second'] == '330' || $rjson['data']['amount'] == 0){
				unset($artikel[$value[data][note]]);
			}
		}
		if(count($artikel) == 0){
			sleep(120);
			break;
		}
		sleep(5);
	}
	$x++;
}
