<? include $_SERVER["DOCUMENT_ROOT"] . "/common/classes/AdminBase.php" ;?>
<?
/*
 * Web process
 * add by cho
 * */
if(!class_exists("AdminMain")){
    class AdminMain extends  AdminBase {
        function __construct($req)
        {
            parent::__construct($req);
        }

        function makeFileName(){
            srand((double)microtime()*1000000) ;
            $Rnd = rand(1000000,2000000) ;
            $Temp = date("Ymdhis") ;
            return $Temp.$Rnd;

        }

        function login(){
            $account = $_REQUEST["account"];
            $password = md5($_REQUEST["password"]);

            $sql = "
                SELECT * FROM tblAdmin
                WHERE `account` = '{$account}' AND `password` = '{$password}' AND status = 1
                LIMIT 1
            ";
            $res = $this->getRow($sql);
            if($res != ""){
                LoginUtil::doAdminLogin($res);
                return $this->makeResultJson(1, "succ", $res);
            }
            else return $this->makeResultJson(-1, "fail");
        }

        function logout(){
            LoginUtil::doAdminLogout();
            return $this->makeResultJson(1, "succ");
        }

        function getLangList(){
            $sql = "SELECT * FROM tblLang ORDER BY `order` ASC;";
            return $this->getArray($sql);
        }

        function deleteLang(){
            $code = $_REQUEST['code'];
            if($code == "") return;
            else{
                $sql = "DELETE FROM tblLang WHERE `code` = '{$code}'";
                $this->update($sql);
            }
        }

        function upsertLang(){
            $code = $_REQUEST['code'];
            $order = $_REQUEST['order'];
            $desc = $_REQUEST['desc'];
            $sql = "INSERT 
                    INTO tblLang(`code`, `order`, `desc`) 
                    VALUES('{$code}', '{$order}', '{$desc}')
                    ON DUPLICATE KEY UPDATE `order`='{$order}', `desc`='{$desc}'";
            $this->update($sql);
        }

        function _upsertLangJson(){
            $this->upsertLangJson($_REQUEST["code"], $_REQUEST["json"]);
        }

        function upsertLangJson($code, $jsonArray){
            $json = mysql_escape_string($jsonArray);

            $sql = "
            INSERT INTO tblLangJson(`code`, `json`, `regDate`)
            VALUES ('{$code}', '{$json}', NOW())
            ON DUPLICATE KEY UPDATE `json`='{$json}'
            ";
            $this->update($sql);
        }

        function _getLangJson(){
            return json_encode($this->getLangJson($_REQUEST["code"]));
        }

        function getLangJson($code){
            $sql = "SELECT * FROM tblLangJson WHERE `code` = '{$code}'";
            return $this->getRow($sql);
        }

        function getLocale(){
            $sql = "SELECT * FROM tblLang ORDER BY `order` ASC";
            return $this->getArray($sql);
        }

        function shareCategoryList(){
            $code = $_REQUEST["code"];
            if($code == "") $sql = "SELECT *, (SELECT `desc` FROM tblLang WHERE `code` = `lang`) AS langDesc FROM tblBoardType ORDER BY regDate DESC";
            else $sql = "SELECT *, (SELECT `desc` FROM tblLang WHERE `code` = `lang`) AS langDesc FROM tblBoardType WHERE `lang` = '{$_REQUEST["code"]}' ORDER BY regDate DESC";
            return $this->getArray($sql);
        }

        function shareCategoryDetail(){
            $sql = "SELECT * FROM tblBoardType WHERE `id` = '{$_REQUEST["id"]}' LIMIT 1";
            return $this->getRow($sql);
        }

        function getExposures(){
            $sql = "SELECT * FROM tblLayoutExposure ORDER BY `desc` ASC";
            $arr = $this->getArray($sql);
//            $retVal = Array();
//            for($e = 0; $e < sizeof($arr); $e++){
//                $retVal[$arr[$e]["code"]] = $arr[$e]["exposure"];
//            }

            return $arr;
        }

        function saveExposure(){
            $value = $_REQUEST["checked"];
            $code = $_REQUEST["code"];
            $sql = "UPDATE tblLayoutExposure SET `exposure` = '{$value}' WHERE `code`='{$code}'";

            $this->update($sql);
        }

        function upsertCategory(){
            $check = getimagesize($_FILES["imgFile"]["tmp_name"]);

            $id = $_REQUEST["id"];
            $lang = $_REQUEST["lang"];
            $name = $_REQUEST["name"];
            $subTitle = $_REQUEST["subTitle"];
            $writePermission = $_REQUEST["writePermission"];
            $readPermission = $_REQUEST["readPermission"];

            $imgPath = NULL;

            if($check !== false){
                $fName = $this->makeFileName() . "." . pathinfo(basename($_FILES["imgFile"]["name"]),PATHINFO_EXTENSION);
                $targetDir = $this->filePath . $fName;
                if(move_uploaded_file($_FILES["imgFile"]["tmp_name"], $targetDir)) $imgPath = $fName;
                else return $this->makeResultJson(-1, "fail");
            }

            $sql = "
                INSERT INTO tblBoardType(`lang`, `name`, `subTitle`, `writePermission`, `readPermission`, `imgPath`, `regDate`)
                VALUES(
                  '{$lang}',
                  '{$name}',
                  '{$subTitle}',
                  '{$writePermission}',
                  '{$readPermission}',
                  '{$imgPath}',
                  NOW()
                )
                ON DUPLICATE KEY UPDATE
                `lang` = '{$lang}',
                `name` = '{$name}',
                `subTitle` = '{$subTitle}',
                `writePermission` = '{$writePermission}',
                `readPermission` = '{$readPermission}',
                `imgPath` = '{$imgPath}'        
            ";
            $this->update($sql);
            return $this->makeResultJson(1, "succ");
        }

    }


}