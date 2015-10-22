<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Uploading...</title>
</head>
<body>
<h1>Uploading file...</h1>
<?php

/**
 * 压缩图像尺寸函数 imageresize
* @perem $src_name 原始图像名称（包含路径名）
* @perem $percent  压缩比例（如0.5为新图像宽高为原图一半）
* @return $new_name 返回压缩后图像名称（包含路径名）
* @caution：调用函数前请做好类型检查，尽限于gif、jpeg、jpg和png格式图像
*/
function imageresize($src_name,$percent){

	list($src_width, $src_height) = getimagesize($src_name);  // 原图大小
	//echo "原始图像宽度为 : ".$width.'    高度为：'.$height."<br>";

	$new_width = $src_width * $percent;   // 缩略图大小
	$new_height = $src_height * $percent;

	$new_image = imagecreatetruecolor($new_width, $new_height);   // 创建中缩图图像流

	$suff = strrchr($src_name, '.'); // 获取后缀名
    
	switch ($suff){
		case ".jpg":
		case ".jpeg":
			$src_image = imagecreatefromjpeg($src_name); // 装载原始图像流
			// 压缩图像
			imagecopyresized($new_image, $src_image, 0, 0, 0, 0,
			$new_width, $new_height, $src_width, $src_height);
			// 获取新图的文件名
			$new_name = substr($src_name,0,strpos($src_name,'.')).'_'.$percent.$suff;
			// 存储图像
			imagejpeg($new_image,$new_name,100);
			break;
		case "png":
			$src_image = imagecreatefrompng($src_name); 
			imagecopyresized($new_image, $src_image, 0, 0, 0, 0,
			$new_width, $new_height, $src_width, $src_height);
			$new_name = substr($src_name,0,strpos($src_name,'.')).'_'.$percent.$suff;
			imagepng($new_image,$new_name);
			break;
		case "gif":
			$src_image = imagecreatefromgif($src_name); 
			imagecopyresized($new_image, $src_image, 0, 0, 0, 0,
			$new_width, $new_height, $src_width, $src_height);
			$new_name = substr($src_name,0,strpos($src_name,'.')).'_'.$percent.$suff;
			imagegif($new_image,$new_name);
			break;
		default:
			//echo alert("文件格式错误，请选择正确格式重新上传！");
			break;
	}
	return $new_name;
}


if($_FILES['userfile']['error']>0)
{
	echo 'Problem:error';

	exit;
}
// put the file where we'd like it
$upfile='uploads/'.$_FILES['userfile']['name'];
if (is_uploaded_file($_FILES['userfile']['tmp_name']))
{	
	if(!move_uploaded_file($_FILES['userfile']['tmp_name'],$upfile))
	{
		echo 'Problem: Could not move file to destination directory';
		exit;
	}

	$middle_percent = 0.5; // 中等压缩比例
	$small_percent = 0.25;  // 小图压缩比例

	$mid_name = imageresize($upfile,$middle_percent);
	$small_name = imageresize($upfile,$small_percent);
	
	echo $mid_name.'<br>';
	echo $small_name."<br>";
}
else
{
	echo 'Problem: Possible file upload attack. Filename:';
	echo $_FILES['userfile']['name'];
	exit;
}
echo $_FILES['userfile']['name'].'File uploaded successfully<br><br>';
?>

</body>
</html>