<?php

$ScanResponse = Array();
$ScanEngines = Array();
$FileCategory = Array();

$ScanResponse[0] = "Clean"; //    No threat detection or the file is empty
$ScanResponse[1] = "Infected/Known"; //	Threat is found
$ScanResponse[2] = "Suspicious"; //	Classified as possible threat but not identified as specific threat.
$ScanResponse[3] = "Failed To Scan"; //	Scanning is not fully performed (For example, invalid file or no read permission)
$ScanResponse[4] = "Cleaned"; //	Threat is found and file is cleaned (repaired or deleted)
$ScanResponse[5] = "Unknown"; //	Unknown scan result
$ScanResponse[6] = "Quarantined"; //	File is quarantined
$ScanResponse[7] = "Skipped Clean"; //	Scan is skipped because this file type is in white-list*
$ScanResponse[8] = "Skipped Dirty"; //	Scan is skipped because this file type is in black-list*
$ScanResponse[9] = "Exceeded Archive Depth"; //	Threat is not found but there are more archive levels which were not extracted.
$ScanResponse[10] = "Not Scanned"; //	Scan is skipped by the engine either due to update or other engine specific reason.
$ScanResponse[11] = "Aborted"; //	All ongoing scans are purged
$ScanResponse[12] = "Encrypted"; //	File/buffer is not scanned because the file type is detected as encrypted (password-protected). If the Internal Archive Library is ON encrypted return type is not going to be returned through Metascan scan progress callbacks since the engines do not perform any scan operations. If the Internal Archive Library is OFF Metascan will pass the encrypted files to the engines directly, bypassing the detection.
$ScanResponse[13] = "Exceeded Archive Size"; //	The extracted archive is too large to scan
$ScanResponse[14] = "Exceeded Archive File Number"; //	There are more files in the archive than configured on the server


$FileCategory['E'] = "xecutable (EXE, DLL)";
$FileCategory['D'] = "Document (MS Office word document, MS Office excel sheet)";
$FileCategory['A'] = "Archive (Zip, Rar, Tar) ";
$FileCategory['G'] = "Graphical format (Jpeg, GIF, TIFF, BMP)";
$FileCategory['T'] = "Text";
$FileCategory['P'] = "PDF format";
$FileCategory['M'] = "Audio or video format";
$FileCategory['Z'] = "Mail messages (MSG)";
$FileCategory['O'] = "Other (anything that is not recognized as one of the above)";


function scanFile($key, $file)
{


// Config.
    $api = 'https://api.metascan-online.com/v1/file';
    $apikey = $key;

// Build headers array.
    $headers = array(
        'apikey: ' . $apikey,
        'filename: ' . basename($file)
    );

// Build options array.
    $options = array(
        CURLOPT_URL => $api,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $wp_filesystem->get_contents($file),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
    );

// Init & execute API call.
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = json_decode(curl_exec($ch), true);
    $data_id = $response['data_id'];

//Config.
    $api = "https://api.metascan-online.com/v1/file/" . $data_id;

//Build headers array.
    $headers = array(
        'apikey: ' . $apikey
    );

//Build options array.
    $options = array(
        CURLOPT_URL => $api,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
    );

//Init & execute API call.
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = json_decode(curl_exec($ch), true);

    $result = $response['scan_results']['scan_all_result_i'];

    return $result;

}


//print_r($response);
/*
$Engines =  $response['scan_results']['scan_details']  ;
foreach($Engines as $key => $one)
{
    //print $key . ' ' . $one . '<br>';
	$ScanEngines[$key] = $response['scan_results']['scan_details'][$key]['scan_result_i']   ;
	if ( $ScanEngines[$key] == 0 ) {
			  echo  $key . " ==> " .$ScanResponse[$ScanEngines[$key]] . "<br>" ; 
		 }
	else {
			   echo $key . "==> " .$ScanResponse[$ScanEngines[$key]] . "\t" ;
			   echo "Threat found "  . " ==> " .$ScanEngines[$key]['threat_found'] . "<br>" ;
		  }
	
}
*/


/*
$Engines =  $response['scan_results']['scan_details']  ;
foreach($Engines as $key => $one)
{
    //print $key . ' ' . $one . '<br>';
	$ScanEngines[$key] = $response['scan_results']['scan_details'][$key]  ;
	   echo $key . "==> " . $ScanResponse[$ScanEngines[$key]['scan_result_i'] ] . "\t scan_time " . $ScanEngines[$key]['scan_time'] . "\t threat_found" . $ScanEngines[$key]['threat_found'] . "<br>"   ;
		
}


echo "data_id " . $response['scan_results']['data_id'] . "<br>";
echo "start_time " . $response['scan_results']['start_time'] . "<br>";
echo "total_time " . $response['scan_results']['total_time']. "<br>";
echo "scan_all_result_i " . $response['scan_results']['scan_all_result_i'] . "<br>";
echo "total_avs " . $response['scan_results']['total_avs'] . "<br>";
echo "scan_all_result_a " . $response['scan_results']['scan_all_result_a'] . "<br>";


echo "file_size " . $response['file_info']['file_size'] . "<br>"; 
echo "md5 " . $response['file_info']['md5'] . "<br>"; 
echo "sha1 " . $response['file_info']['sha1'] . "<br>"; 
echo "sha256 " . $response['file_info']['sha256'] . "<br>"; 
echo "file_type_category " . $FileCategory[$response['file_info']['file_type_category']] . "<br>"; 
echo "display_name " . $response['file_info']['display_name'] . "<br>"; 

*/


?>
