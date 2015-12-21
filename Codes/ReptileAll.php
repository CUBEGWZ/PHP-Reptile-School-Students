<?php

//程序运行初始时间
$timeb=date('y-m-d H:i:s',time());

//超时时间设置
set_time_limit(1000000);

//用于取消mysql的错误提示
error_reporting(E_ALL ^ E_DEPRECATED);


//数据存储的数据库配置
$DB_HOST="localhost";
$DB_USER="root";
$DB_PWD="";
$DB_NAME="stuinfo";
$DB_SHEET="test";





//学生数组数据
//************************************************************************************









//************************************************************************************

//初始数组的下标
$beginnum=0;

//遇到安全狗的间隔时间（以秒为单位）
$dogtime=180;

//取得数组长度，将数组修改一下
$length=count($stunum);
for($i=$beginnum;$i<$length;$i++)
	$stunum[$i]=$stunum[$i]+200000000000;

//数组的数据切换变量
$c=$beginnum;
//网页的前缀地址(学号信息的查询地址)
$url="";
//爬虫的初始学号
$numb=$num=$stunum[$beginnum];
//爬虫结束的学号
$nume=$stunum[$length-1]+1000;

//有效数据的个数（初始为0）
$numtrue=0;
//空数据跳转到下一个数组的判断变量
$numzeronum=0;
//空数据跳转前默认数据（当$numzeronum大于等于$numzero时数据切换到下一个数组）
$numzero=20;
//定义总共爬的数据量
$allnum=1;
//捕捉链接地址并转储到$html
$html=file_get_contents($url.$num);
$html=str_replace(array("/r","/n","/t","/s"), '', $html);
//捕捉学生信息存储到$match数组，并创建判断该学号 是否存在学生数据的条件$judge，如果$judge==0,说明该学号没有学生数据
$judge=preg_match_all('/<div[^>]*>(.*?)<\/div>/si',$html,$match);

//连接数据库
$con = mysql_connect($DB_HOST,$DB_USER,$DB_PWD);

while(1)
{
	//超出结束的学号时退出
	if($num>=$nume)
		break;
	if($judge==0)
		$numzeronum++;
	else
	{
		$numzeronum=0;
		//$JudgeWriteDB判断是否将数据写入数据库，根据本代码位置可知，此处可能出现的网页中存在div标签，所以此处检测安全狗页面的@符号
		$JudgeWriteDB=preg_match_all('/\@/',$html,$WriteDB);
		while($JudgeWriteDB==0)
		{
			//如果遇到安全狗，三分钟后再尝试
			$date_b= date('y-m-d H:i:s',time());
			$date_e= date('y-m-d H:i:s',time());
			$difference=strtotime($date_e)-strtotime($date_b);
			while($difference<$dogtime)
			{
			  $date_e= date('y-m-d H:i:s',time());
			  $difference=strtotime($date_e)-strtotime($date_b);
			}
			//捕捉链接地址并转储到$html
			$html=file_get_contents($url.$num);
			$html=str_replace(array("/r","/n","/t","/s"), '', $html);
			$JudgeWriteDB=preg_match_all('/\@/',$html,$WriteDB);
			//防止安全狗后第一个检测的学号没有学生数据信息
			$judge=preg_match_all('/<div[^>]*>(.*?)<\/div>/si',$html,$match);
			if($judge==0)
			{
				$numzeronum++;
				break;
			}	
		}
		if($numzeronum==0)
		{
			//捕捉学生信息存储到$match数组，并创建判断该学号 是否存在学生数据的条件$judge
			$judge=preg_match_all('/<div[^>]*>(.*?)<\/div>/si',$html,$match);
			//获取头像图片链接地址
			/*
			preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',$html,$link);
			$imgurl=$img.$link[1][0];
			echo $imgurl;
			*/
			//自定义图片地址
			$imgurl="$num.jpg";
			//echo $imgurl;
			//获取身份证号
			$ju=preg_match('/\d{18}|\d{17}[0-9Xx]/',$html,$idcard);
			//echo $idcard[0];
			$info[0]=$match[1][3];//学号
			$info[1]=$match[1][5];//姓名
			$info[2]=$match[1][7];//性别
			$info[3]=$match[1][9];//学院
			$info[4]=$match[1][11];//部门
			//身份证号
			if($ju==0)
				$idcard[0]="999999999999999999";
			$info[5]=$idcard[0];
			//头像链接真实地址（提取用作图片链接时需要修改）
			$info[6]=$imgurl;
			//转换字符串编码
			$info[0]=iconv('GB18030','UTF-8', $info[0]);
			$info[1]=iconv('GB18030','UTF-8', $info[1]);
			$info[2]=iconv('GB18030','UTF-8', $info[2]);
			$info[3]=iconv('GB18030','UTF-8', $info[3]);
			$info[4]=iconv('GB18030','UTF-8', $info[4]);
			$info[5]=iconv('GB18030','UTF-8', $info[5]);
			$info[6]=iconv('GB18030','UTF-8', $info[6]);
			//print_r($info);
			//有效数据自加1
			$numtrue++;
			if (!$con){
				die('无法连接数据库: ' . mysql_error());
			}
			else
			{
				mysql_query("set names 'utf8'");	//设置要写入数据的格式
				//将数据写入$DB_SHEET数据表
				$sql = "insert into $DB_SHEET(num,name,sex,college,class,idcard,photo) values ('$info[0]','$info[1]','$info[2]','$info[3]','$info[4]','$info[5]','$info[6]')";
				mysql_query($sql);
			}
			
			
		}
		
	}
	$num++;
	$allnum++;
	if($numzeronum>=$numzero)
	{
		$c++;
		if($c>($length-1))
			$stunum[$c]=999999999999;
		$num=$stunum[$c];
	}
	if($num==999999999999)
		break;
	else
	{
		//捕捉链接地址并转储到$html
		$html=file_get_contents($url.$num);
		$html=str_replace(array("/r","/n","/t","/s"), '', $html);
		//捕捉学生信息存储到$match数组，并创建判断该学号 是否存在学生数据的条件$judge
		$judge=preg_match_all('/<div[^>]*>(.*?)<\/div>/si',$html,$match);
	}
}

mysql_close($con);	//关闭连接

$numnum=$nume-$numb;
//获取当前系统时间
$timee=date('y-m-d H:i:s',time());
echo "爬虫进行前的时间为：".$timeb.'<br/>';
echo "爬虫完毕后的时间为：".$timee.'<br/>';
echo "本次共爬了".$allnum."个数据。".'<br/>';
echo "一共爬到了：".$numtrue."个有用的数据。".'<br/>';
echo "系统已经把数据存入".$DB_NAME."数据库中的".$DB_SHEET."数据表内！".'<br/>';




























//连接数据库
$con = mysql_connect($DB_HOST,$DB_USER,$DB_PWD);
if (!$con){
	die('无法连接数据库: ' . mysql_error());
}
else{

	mysql_select_db($DB_NAME, $con);
	//捕捉学生信息存储到$match数组，并创建判断该学号 是否存在学生数据的条件$judge
	$judge=preg_match_all('/<div[^>]*>(.*?)<\/div>/si',$html,$match);
	while(1)
	{
		//超出结束的学号时退出
		if($num>=$nume)
			break;
		if($judge==0)
			$numzeronum++;
		else
		{
			$numzeronum=0;
			$JudgeWriteDB=preg_match_all('/\@/',$html,$WriteDB);
			while($JudgeWriteDB==0)
			{
				//如果遇到安全狗，三分钟后再尝试
				$date_b= date('y-m-d H:i:s',time());
				$date_e= date('y-m-d H:i:s',time());
				$difference=strtotime($date_e)-strtotime($date_b);
				while($difference<180)
				{
				  $date_e= date('y-m-d H:i:s',time());
				  $difference=strtotime($date_e)-strtotime($date_b);
				}
				//捕捉链接地址并转储到$html
				$html=file_get_contents($url.$num);
				$html=str_replace(array("/r","/n","/t","/s"), '', $html);
				$JudgeWriteDB=preg_match_all('/\@/',$html,$WriteDB);
				$judge=preg_match_all('/<div[^>]*>(.*?)<\/div>/si',$html,$match);
				if($judge==0)
					break;
			}
			//捕捉学生信息存储到$match数组，并创建判断该学号 是否存在学生数据的条件$judge
			$judge=preg_match_all('/<div[^>]*>(.*?)<\/div>/si',$html,$match);
			//获取头像图片链接地址
			/*
			preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',$html,$link);
			$imgurl=$img.$link[1][0];
			echo $imgurl;
			*/
			//自定义图片地址
			$imgurl="$num.jpg";
			//echo $imgurl;
			//获取身份证号
			$ju=preg_match('/\d{18}|\d{17}[0-9Xx]/',$html,$idcard);
			//echo $idcard[0];
			$info[0]=$match[1][3];//学号
			$info[1]=$match[1][5];//姓名
			$info[2]=$match[1][7];//性别
			$info[3]=$match[1][9];//学院
			$info[4]=$match[1][11];//部门
			//身份证号
			if($ju==0)
				$idcard[0]="999999999999999999";
			$info[5]=$idcard[0];
			//头像链接真实地址（提取用作图片链接时需要修改）
			$info[6]=$imgurl;
			//转换字符串编码
			$info[0]=iconv('GB18030','UTF-8', $info[0]);
			$info[1]=iconv('GB18030','UTF-8', $info[1]);
			$info[2]=iconv('GB18030','UTF-8', $info[2]);
			$info[3]=iconv('GB18030','UTF-8', $info[3]);
			$info[4]=iconv('GB18030','UTF-8', $info[4]);
			$info[5]=iconv('GB18030','UTF-8', $info[5]);
			$info[6]=iconv('GB18030','UTF-8', $info[6]);
			//print_r($info);
			//有效数据自加1
			$numtrue++;
			mysql_query("set names 'utf8'");	//设置要写入数据的格式
			//将数据写入$DB_SHEET数据表
			$sql = "insert into $DB_SHEET(num,name,sex,college,class,idcard,photo) values ('$info[0]','$info[1]','$info[2]','$info[3]','$info[4]','$info[5]','$info[6]')";
			mysql_query($sql);
		}
		$num++;
		$allnum++;
		if($numzeronum>=$numzero)
		{
			$c++;
			if($c>($length-1))
				$stunum[$c]=999999999999;
			$num=$stunum[$c];
		}
		if($num==999999999999)
			break;
		else
		{
			//捕捉链接地址并转储到$html
			$html=file_get_contents($url.$num);
			$html=str_replace(array("/r","/n","/t","/s"), '', $html);
			//捕捉学生信息存储到$match数组，并创建判断该学号 是否存在学生数据的条件$judge
			$judge=preg_match_all('/<div[^>]*>(.*?)<\/div>/si',$html,$match);
		}
	}
}

mysql_close($con);	//关闭连接

$numnum=$nume-$numb;
//获取当前系统时间
$timee=date('y-m-d H:i:s',time());
echo "爬虫进行前的时间为：".$timeb.'<br/>';
echo "爬虫完毕后的时间为：".$timee.'<br/>';
echo "本次共爬了".$allnum."个数据。".'<br/>';
echo "一共爬到了：".$numtrue."个有用的数据。".'<br/>';
echo "系统已经把数据存入".$DB_NAME."数据库中的".$DB_SHEET."数据表内！".'<br/>';
?>
