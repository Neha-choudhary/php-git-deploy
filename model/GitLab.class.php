<?php

require_once './model/cURL.class.php';

class GitLab{

    const URL = 'https://gitlab.com/api/v4/';

    protected $_userId;
    protected $_projectId;
    protected $_projectPath;
    protected $_projectNamespace;
    protected $_authCode;
    protected $_catchCommit;
    protected $_branch;
    protected $_configFile;
    private $_lastUpdate;

    public function __construct($projectNamespace, $projectPath, $authCode, $cathCommit, $branch = 'master', $configFile = 'gitlab.json'){
        $this->_projectNamespace = $projectNamespace;
        $this->_projectPath = $projectPath;
        $this->_authCode = $authCode;
        $this->_catchCommit = $cathCommit;
        $this->_branch = $branch;
        $this->_configFile = $configFile;
    }

    public function init(){
        if($handle = fopen($this->_configFile, "r")){
            echo "true";
         }else{
            //First run
            self::getUserId($this->_projectNamespace);
            self::getProjectId($this->_userId, $this->_projectPath);
            self::getBranchUpdate($this->_projectId);
        }
    }
        
    public function getUserId($namespace){
        $get = new cURL(self::URL.'users?username='.$namespace);
        $get->init();
        $this->_userId = $get->__toObject()->id;
    }

    public function getProjectId($userId,$path){
        $get = new cURL(self::URL.'users/'.$userId.'/projects?simple=true&search='.$path, $this->_authCode);
        $get->init();
        $this->_projectId = $get->__toObject()->id;
    }

    public function getBranchUpdate(){
        $get = new cURL(self::URL.'projects/'.$this->_projectId.'/repository/branches/', $this->_authCode);
        $get->init();
        foreach($get->__toArray() as $arr){
            if($arr->name == $this->_branch){
                if($this->_lastUpdate < $arr->commit->authored_date){
                    self::doPull($arr->commit->id);
                }
            }
        }
    }

    public function doPull($sha){
        $get = new cURL(self::URL.'projects/'.$this->_projectId.'/repository/archive.zip?sha='.$sha, $this->_authCode, 300, 4, true);
        if($get->init()){
            $zip = new ZipArchive();
            if ($zip->open("$sha.zip")) {
                for($i=0; $i<$zip->numFiles; $i++) {
                    $zip->renameIndex($i, str_replace("$this->_projectPath-$sha-$sha", '', $zip->getNameIndex($i)));
                }
                $zip->close();
                $zip->open("$sha.zip");
                $zip->extractTo('./');
                $zip->close();
                unlink("$sha.zip");
            }
    }
}

    public function saveConfigFile(){
        file_put_contents($this->_configFile,json_encode(
            Array(
                "projectId"=> $this->_projectId,
                "projectPath"=> $this->_projectPath,
                "projectNamespace"=> $this->_projectNamespace,
                "branch"=> $this->_branch,
                "lastUpdate"=> $this->_lastUpdate
            )
        ));
    }
}