<?php
class cURL {
     protected $_url;
     protected $_timeout;
     protected $_status;
     protected $_webContent;
     protected $_binaryTransfer;
     protected $_authCode;

     public function __construct($url, $authCode = false, $timeOut = 300,$maxRedirecs = 4,$binaryTransfer = false)
     {
         $this->_url = $url;
         $this->_timeout = $timeOut;
         $this->_maxRedirects = $maxRedirecs;
         $this->_binaryTransfer = $binaryTransfer;
         if($authCode){
         $this->_authCode = $authCode;
         }
     }

     public function init($url = null)
     {
        if($url != null){
          $this->_url = $url;
        }

         $ch = curl_init();

         curl_setopt($ch,CURLOPT_URL,$this->_url);
         curl_setopt($ch,CURLOPT_TIMEOUT,$this->_timeout);
         curl_setopt($ch,CURLOPT_MAXREDIRS,$this->_maxRedirects);
         curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         if($this->_authCode){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('PRIVATE-TOKEN: '.$this->_authCode));
         }
         if($this->_binaryTransfer)
         {
            $fp = fopen (substr($this->_url, strpos($this->_url, 'sha=')+4, strlen($this->_url)-1).'.zip', 'w+');
            curl_setopt($ch, CURLOPT_FILE, $fp); 
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $success = curl_exec($ch); 
            curl_close($ch);
            fclose($fp);
            return $success;
         }
         $this->_webContent = curl_exec($ch);
         $this->_status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
         curl_close($ch);

     }

   public function getHttpStatus()
   {
       return $this->_status;
   }

   public function __tostring(){
      return $this->_webContent;
   }

   public function __toArray(){
       return json_decode($this->_webContent);
   }
   
   public function __toObject(){
       $obj = json_decode($this->_webContent);
       return is_array($obj) ?  $obj[0] :  $obj;
   }
}