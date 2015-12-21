<?php
set_time_limit(10000);
//获取当前系统时间
$timeb=date('y-m-d H:i:s',time());

//图片网页的前缀地址

$imgurl="";
//起始数据
$num=$numb=*******;
//结束数据
$nume=************;
//图片后缀
$imgend=".jpg";
//写入文件时的回车
$huiche="\n";
//判断有效数据
$a=0;
while(1)
{
	//捕捉图片
	@$img = file_get_contents($imgurl.$num.$imgend);
	if($img!=false){
		$file=fopen("stunum.txt","a");
		fwrite($file,$num);
		fwrite($file,$huiche);
		fclose($file);
		$a++;
	}
	echo $num.'<br/>';
	$num=$num+1000;
	if($num>=$nume)
		break;
}
$numall=($nume-$numb)/1000;
//获取当前系统时间
$timee=date('y-m-d H:i:s',time());
echo "爬虫进行前的时间为：".$timeb.'<br/>';
echo "爬虫完毕后的时间为：".$timee.'<br/>';
echo "本次共爬了".$numall."个数据".'<br/>';
echo "本次共爬了".$a."个有效数据".'<br/>';
echo "系统已经把数据存入stunum.txt文件中".'<br/>';
?>