<?php
set_time_limit(10000);
//获取当前系统时间
$timeb=date('y-m-d H:i:s',time());

//找出学生的学院专业

$numnum[0]=20130010001;
$numnum[1]=20130121001;
$numnum[2]=20130205001;
$numnum[3]=20130209001;
$numnum[4]=20130212001;
$numnum[5]=20130221001;
$numnum[6]=20130222001;
$numnum[7]=20130311001;
$numnum[8]=20130412001;
$numnum[9]=20130421001;
$numnum[10]=20130519001;

//起始数据
$num=$numb=$numnum[0];

//取得数组长度，将数组修改一下
$length=count($numnum);

//结束数据
$nume=$numnum[$length-1];



//数组的切换变量
$i=0;


//图片网页的前缀地址
$imgurl="";

//图片后缀
$imgend=".jpg";
//写入文件时的回车
$huiche="\n";
//判断有效数据
$a=0;
//跳转到下一个数组的判断条件
$judge=0;

while(1)
{
	//捕捉图片
	@$img = file_get_contents($imgurl.$num.$imgend);
	if($img!=false){
		$judge=0;
		$file=fopen("stufind.txt","a");
		fwrite($file,$num);
		fwrite($file,$huiche);
		fclose($file);
		$a++;
	}
	else{
		$judge++;
	}
	echo $num.'<br/>';
	$num=$num+1;
	if($judge>=20){
		$i++;
		//***************************************************************************************
		$num=$numnum[$i];
		//***************************************************************************************
	}
	if($num>=$nume)
		break;
}
$numall=($nume-$numb)/1000;
//获取当前系统时间
$timee=date('y-m-d H:i:s',time());
echo "爬虫进行前的时间为：".$timeb.'<br/>';
echo "爬虫完毕后的时间为：".$timee.'<br/>';
echo "本次共爬了".$a."个有效数据".'<br/>';
echo "系统已经把数据存入stufind.txt文件中".'<br/>';
?>