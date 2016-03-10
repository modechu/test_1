<?
########################  class uploadIMG  ############ 2003/03/28 ############
#		class upload image to server
#			
#  ※ 目前只接受 jpeg 檔上傳
#  ※ 可依指定 圖檔最高及最寬 要求 將上傳的檔等比率改大小後上傳
#  ※ JACK YANG
#
########################  class uploadIMG  #####################################

class uploadIMG {

	var $type ="jpg";
	var $saveDIR;
	var $fileNAME;
#############################################################
	function upload($postImage, $maxWidth=1000, $maxHeight=1000) {
		$send = "";
	//	$newImage = object;		// image created object

	if ($postImage == "none") {  //是否沒傳檔??
		$msg = "no image file upload.";
	} elseif ($postImage) {

		$picInfos = getimagesize( $postImage );
		$width = $picInfos[0];
		$height = $picInfos[1];
		
		if( $width > $maxWidth & $height <= $maxHeight ) {
			$ratio = $maxWidth / $width;
		} elseif( $height > $maxHeight & $width <= $maxWidth ) {
			$ratio = $maxHeight / $height;
		} elseif( $width > $maxWidth & $height > $maxHeight ) {
			$ratio1 = $maxWidth / $width;
			$ratio2 = $maxHeight / $height;
			$ratio = ($ratio1 < $ratio2)? $ratio1:$ratio2;
		} else {
			$ratio = 1;
		}

		$nWidth = floor($width*$ratio);
		$nHeight = floor($height*$ratio);

		$origPic = imagecreatefromjpeg( $postImage );

		$newImage = imagecreatetruecolor($nWidth,$nHeight);  // ?? need GD2.0X???  2005/01/05
//		$newImage = ImageCreate($nWidth,$nHeight);

		imagecopyresampled($newImage, $origPic, 0, 0, 0, 0, $nWidth, $nHeight, $width, $height); // ?? need GD2.0X???  2005/01/05	
//		ImageCopyResized($newImage, $origPic, 0, 0, 0, 0, $nWidth, $nHeight, $width, $height);	

			// 上傳之路逕+檔名
		$upfile = $this->saveDIR.$this->fileNAME;

//		copy($newImage, $upfile);
			// 上傳
		if (imagejpeg($newImage, $upfile)){ 
			$send = "1";
		} else {
			$send = "";
		}

	}  // end of if (!$postImage == "none")

	return $send;
	}  //end of func
	
#############################################################
	function setSaveTo($dir,$filename)	{
		$this->saveDIR = $dir;
		$this->fileNAME = $filename;
	}
#############################################################
	function imageType( )	{
		if( eregi( "jpeg", $this -> iOrig[ 'type' ]) ) // JPG
			 return "JPG";
		elseif( eregi( "png", $this -> iOrig[ 'type' ] ) ) // PNG
	 		return "PNG";
	}

	// function saveTo( STRING name [, STRING path ] )
	// save the new image in the specified path, with the specified name
#############################################################
	

}  // end class

?>