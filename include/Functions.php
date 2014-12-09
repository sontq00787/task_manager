<?php
/**
 * Class to handle all addional functions
 *
 * @author Newbie
 * 
 */
class Functions {
	public function uploadFile() {
		$allowedExts = array (
				"gif",
				"jpeg",
				"jpg",
				"png",
				"json" 
		);
		$file_name = null;
		$temp = explode ( ".", $_FILES ["jsonfile"] ["name"] );
		$extension = end ( $temp );
		if ((($_FILES ["jsonfile"] ["type"] == "application/json") || ($_FILES ["jsonfile"] ["type"] == "image/gif") || ($_FILES ["jsonfile"] ["type"] == "image/jpeg") || ($_FILES ["jsonfile"] ["type"] == "image/jpg") || ($_FILES ["jsonfile"] ["type"] == "image/pjpeg") || ($_FILES ["jsonfile"] ["type"] == "image/x-png") || ($_FILES ["jsonfile"] ["type"] == "image/png")) && ($_FILES ["jsonfile"] ["size"] < 2048000) && in_array ( $extension, $allowedExts )) {
			if ($_FILES ["jsonfile"] ["error"] > 0) {
				return "Error: " . $_FILES ["jsonfile"] ["error"] . "<br>";
			} else {
				// echo "Upload: " . $_FILES ["map_name"] ["name"] . "<br>";
				// echo "Type: " . $_FILES ["map_name"] ["type"] . "<br>";
				// echo "Size: " . ($_FILES ["map_name"] ["size"] / 1024) . " kB<br>";
				// if (file_exists ( "json/" . $_FILES ["jsonfile"] ["name"] )) {
				// return "Error: " . $_FILES ["jsonfile"] ["name"] . " already exists. ";
				// } else {
				move_uploaded_file ( $_FILES ["jsonfile"] ["tmp_name"], "json/" . $_FILES ["jsonfile"] ["name"] );
				$file_name = $_FILES ["jsonfile"] ["name"];
				// echo "Stored in: " . "upload/" . $_FILES ["map_name"] ["name"];
				// }
			}
		} else {
			return "Error: Invalid file";
		}
		return $file_name;
	}
	
	/**
	 * Fetching status for tasks
	 *
	 * @param String $status_code
	 *        	status_code
	 */
	public function printTaskStatus($status_code) {
		if ($status_code == NOT_START)
			return "<font color='red'><b>Not Start</b></font>";
		elseif ($status_code == IN_PROGESS)
			return "<font color='green'><b>In Progress</b></font>";
		elseif ($status_code == COMPLETED)
			return "<font color='gray'>Completed</font>";
		elseif ($status_code == TASK_IMPORTANT)
			return "Important";
		else
			return "New";
	}
	public function printQAStatus($status_code) {
		if ($status_code == IN_PROGESS)
			return "<font color='red'><b>Open</b></font>";
		elseif ($status_code == COMPLETED)
			return "<font color='gray'><b>Close</b></font>";
		else
			return "New";
	}
	
	/**
	 *
	 * @param unknown $mailto        	
	 * @param unknown $mailfrom        	
	 * @param unknown $subject        	
	 * @param unknown $msg        	
	 * @param string $cc        	
	 * @param string $bcc        	
	 * @return boolean
	 */
	function send($mailto, $mailfrom, $subject, $msg, $cc = "", $bcc = "") {
		
		// UTF8Ç≈
		mb_language ( "uni" );
		mb_internal_encoding ( "UTF-8" );
		
		// ÉÅÅ[ÉãÉwÉbÉ_
		$mailheader = "From: " . $mailfrom . "\n";
		if ($cc != "")
			$mailheader .= "Cc: " . $cc . "\n";
		if ($bcc != "")
			$mailheader .= "Bcc: " . $bcc . "\n";
		$mailheader .= "Errors-To: " . mb_convert_encoding ( $mailfrom, "UTF-8", "sjis-win" ) . "\n";
		$mailheader .= "Return-Path: " . mb_convert_encoding ( $mailfrom, "UTF-8", "sjis-win" ) . "\n";
		
		// ëóêMèàóù
		$rtn = mb_send_mail ( $mailto, mb_convert_encoding ( $subject, "UTF-8", "sjis-win" ), mb_convert_encoding ( $msg, "UTF-8", "sjis-win" ), $mailheader );
		
		if ($rtn)
			return TRUE; // 1
		
		return FALSE;
	}
}
?>