<?php

 //证书文件.请勿修改任何内容


 error_reporting(0);
  
 if( isset($_POST['code'] )){
   $code=$_POST['code'];
 }else {  
   $code="";
 }

  
 if( isset($_POST['sign'] )){
  $sign=$_POST['sign'];
   }else {  
  $sign="";
 }

 if( isset($_POST['sign2'] )){
  $sign2=$_POST['sign2'];
   }else {  
  $sign2="";
 }   
   
 if( isset($_POST['timestamp'] )){
  $timestamp=$_POST['timestamp'];
   }else {  
  $timestamp="";
 } 

   
   
 
 if( isset($_POST['data01'] )){
  $data01= base64_decode($_POST['data01']); 
 
 }else {  
   $data01="";
 }
 
 if( isset($_POST['data02'] )){
  $data02= base64_decode($_POST['data02']); 
 }else {  
   $data02="";
 }
 
 
 if( isset($_POST['data03'] )){
  $data03= base64_decode($_POST['data03']); 
 }else {  
   $data03="";
 }
 
 if( isset($_POST['data04'] )){
  $data04= base64_decode($_POST['data04']); 
 }else {  
   $data04="";
 }
 
 if( isset($_POST['data05'] )){
  $data05= base64_decode($_POST['data05']); 
 }else {  
   $data05="";
 }
 
 if( isset($_POST['data06'] )){
  $data06= base64_decode($_POST['data06']); 
 }else {  
   $data06="";
 }

 
 $state=0; 
 $serverKey="PwNWsETDLQRhPDInhUyK0gLZOeeKtQwLZpnhTlTJ8Jwcd0vkQgopL+Fde6h/5F4Z";
 
 
if($code=="GetInfo" and $data01=="Connection" )
{
  if($data03!="V0003")
  {  
 $state=2000;
 echo base64_encode('CertificateVersionErro');
  }
}
  
if($state==0 and strlen($sign2)>=10)
{
  $signTemp= md5(md5($code.$data01.$data02.$timestamp.$serverKey).$timestamp);
  if($sign==$signTemp)
  {
   $state=1;  
  }  
  else
  {
   $state=2000;
   echo base64_encode('SignErro');
  }  
} 
  
 if ($state==1)
 {
   if($code=="GetInfo")
   {   
   if($data01=="IsInPublic")
   {  
   $str="false";
   if( file_exists('core.php') ) // if In Public
   {
    $str="true";
   }  
   echo base64_encode($str);
   $state=2000;
   }
   else if($data01=="GetOssInfo")
 {
           $oss['IsOpen']="false"; //是否开启OSS.值:true;false
           $oss['OssType']="AliYun"; //OSS类型.阿里云OSS:AliYun;七牛云OSS:QiNiu;腾讯云OSS:Tencent;
           $oss['AccessKeyId']="l1herGr6"; // OSS的KeyId
           $oss['AccessKeySecret']="EBe4xNzwertrAIPtaXIi"; // OSS密码
           $oss['BucketName']="shopfw-oss"; // OSS 名称
           $oss['Endpoint']="http://oss.shopfw.com/"; //OSS地址如 http://oss.shopfw.com/ 必须以http开头  /结尾   
           $oss['Region']="ap-shanghai"; //腾讯OSS专用.存储桶的地域.参考文档:https://cloud.tencent.com/document/product/436/6224
           $str="[". json_encode($oss) ."]"; 
           $str= json_encode($oss);
           $state=2000;
           echo base64_encode($str); 
 } 
  }      

 }  

 if($state==1)
 {  

 
    $configFile='';
    if( file_exists('config/database.php') ) 
    {
     $configFile='config/database.php';
    }  
    else if ( file_exists('../config/database.php') ) 
    {
     $configFile='../config/database.php';
    } 
   
  if(strlen($configFile)>=1)
  {
    $config=include($configFile);  
     
    if(isset($config['connections']['mysql'])==true) 
    {
        $config=$config['connections']['mysql'];
    }
 
    $DB_USER = $config['username'] ;  
    $DB_PASSWORD = $config['password'] ; 
    $DB_NAME =$config['database'] ;   
    $DB_HOST = $config['hostname'] ;   
    $PORT =$config['hostport'];  
    $PREFIX = $config['prefix'];  
 
 
 $data01=str_replace("sxo_",$PREFIX,$data01);
 $data02=str_replace("sxo_",$PREFIX,$data02);
 $data03=str_replace("sxo_",$PREFIX,$data03);
 $data04=str_replace("sxo_",$PREFIX,$data04);
 $data05=str_replace("sxo_",$PREFIX,$data05);
   
  }
  else
  {
   $state=2000;
   echo base64_encode("NoConfig");  
  }
  
  
  
  if ( $state==1)
  {
   
   $DNS="mysql:host=$DB_HOST;port=$PORT;dbname=$DB_NAME";    

   try
   {
    $con  = new PDO($DNS, $DB_USER, $DB_PASSWORD); 
    $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $con->query ("set names utf8mb4");   

    if($code=="GetInfo" and $data01=="Connection")
    {
     $state=2000;
     echo base64_encode("MysqlIsOk"); 
    }
    else
    {
     $state=50;
    }
   }
   catch(PDOException $e)
   {
    $state=2000;
    $str="MysqlIsErro:".$e->getMessage();
    echo base64_encode($str);
   }
  }
  
 }

 
   
 if ($state==50)
 {
  if ($code=="GetText"){
   
   $str="";
   try {
    $rs = $con->query($data01); 
    if($rs) {      
     $rs->setFetchMode(PDO::FETCH_OBJ);    
     $str= $rs->fetchColumn(0);   
    }
     } catch (PDOException $e) {
     $str= "ErroInfo:". $e->getMessage()."\n".$data01 ;
     }
   echo base64_encode($str);  
  }
  else if ( stripos(",Update,Delete,Insert,Modify,",$code)!==false ){

   $str="";
   try {
    $str=$con->exec($data01);   
   } catch (PDOException $e) {
    $str= "ErroInfo:". $e->getMessage()."\n".$data01 ;
   }
     echo base64_encode($str);  
  }  
  else if ($code=="GetRows"){
   $str="";
   try {   
    $rs = $con->query($data01); 
    
    if($rs){ 
     $rs->setFetchMode(PDO::FETCH_OBJ);    
     while ($row = $rs->fetch()) {
      $strTemp="";
      foreach($row as $key=>$value)
      {
       $value=str_replace('"','\"',$value);
    $value=str_replace('&quot;','\"',$value);
       $strTemp=$strTemp."\"$value\" ,";
      }
      if($strTemp<>""){
       $strTemp = substr( $strTemp,0,strlen($strTemp)-1 );      
       $str=$str."(".$strTemp.")  ,";
      }  
     } 
     if($str<>""){
      $str = substr( $str,0,strlen($str)-1 );
     }
    }
   } catch (PDOException $e) {
    $str= "ErroInfo:". $e->getMessage()."\n".$data01 ;
   }

   echo base64_encode($str);

  
  }
  else if ($code=="GetJson"){  
   $str="";
   $results=array();   
   try {    
    $rs = $con->prepare($data01);   
    $rs->execute();     
    if($rs){
     while($row = $rs->fetch(PDO::FETCH_ASSOC)) {
      $results[] = $row;
     }
     if(count($results) >=1)
     {
      $str= json_encode($results);   
     }
    }
   } catch (PDOException $e) {
    $str= "ErroInfo:". $e->getMessage()."\n".$data01 ;
   }   

   echo base64_encode($str);
  }  
  else if( $code=="InsertAndSelect" )
  {    
   $str="";
   try {
    $rs = $con->query($data02); 
    if($rs) {      
     $rs->setFetchMode(PDO::FETCH_OBJ);    
     $str= $rs->fetchColumn(0);   
     
     if($str=="")
     {
      $con->exec($data01);
      $rs = $con->query($data02);      
      $rs->setFetchMode(PDO::FETCH_OBJ);    
      $str= $rs->fetchColumn(0);
     }
  if($str=="")
  {
   $str= "ErroInfo:InsertAndSelect Erro\n InsertSql:$data01\nSelectSql:$data02" ;
  }  
    }
   } catch (PDOException $e) {
    $str= "ErroInfo:". $e->getMessage()."\n".$data01."\n\n".$data02 ;
   } 
   echo base64_encode($str);  
   
  }
  else if( $code=="UploadImages")
  {
   $str="true";
   if(strpos($data01, '/')!==0 )
   {   
    $data01="/".$data01;
   }
   
   if(strpos($data01, '.')==false)
   {
    $str="false";
   }
   
   if($str=="true")
   {    
    $data01 = realpath(dirname(__FILE__)).$data01;
    $data01=str_replace("\\","/",$data01); 
    $path =  substr($data01,0, strrpos($data01, '/')+1); 
    createFolders($path);   
    
    $data03=base64_decode($data03); 
    $str =  createImage($data01,$data03 );
   }  
   echo base64_encode($str); 
  } 

 }
 else if($state==0)
 {
  $serverKey="d8834ad88f444a5 *****************" ;
  echo 'Server is ok'."  ID:"."<a href='https://www.shopfw.com' target='_blank'>$serverKey</a>";
 }

 function createFolders($dir)
 { 
     return is_dir($dir) or (createFolders(dirname($dir)) and mkdir($dir)  and chmod($dir,0777)); 
 }
 
 function createImage(  $filePath ,  $fileStream )
 {
  $str="False";
  if( strrpos($filePath, '.')>=0 ) 
  {
   $extensionNameAll=".jpg.jpeg.gif.png.bmp.tiff";
   $fileExtension = substr($filePath, strrpos($filePath, '.'));
   
   if( strrpos($extensionNameAll, $fileExtension)>=0 ) 
   {
    set_time_limit(0);  
    $data = $fileStream;
     $fpDst = fopen($filePath, "wb");
     fwrite($fpDst, $data);
     fclose($fpDst);
     $str="true";
   }
  } 

  return $str;
 } 


 
 


 
 
?>


