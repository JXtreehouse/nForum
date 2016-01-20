<?php
/**
 * user controller for nforum
 *
 * @author xw
 */
class UserController extends NF_Controller {

    public function ajax_sessionAction(){
        $this->cache(false);
        $static = c('site.static');
        $base = c('site.base');
        $user = User::getInstance();

        load('inc/wrapper');
        $wrapper = Wrapper::getInstance();
        $ret = $wrapper->user($user);
        $ret['is_login'] = ($user->userid != 'guest');
        $ret['forum_total_count'] = Forum::getOnlineNum();
        $ret['forum_user_count'] = Forum::getOnlineUserNum();
        $ret['forum_guest_count'] = Forum::getOnlineGuestNum();

        load("model/mail");
        $info = MailBox::getInfo($user);
        $ret['new_mail'] = $info['newmail'];
        $ret['full_mail'] = $info['full'];

        if(c('refer.enable')){
            load('model/refer');
            $ret['new_at'] = $ret['new_reply'] = false;
            try{
                if($user->getCustom('userdefine1', 2)){
                    $refer = new Refer($user, Refer::$AT);
                    $ret['new_at'] = $refer->getNewNum();
                }
                if($user->getCustom('userdefine1', 3)){
                    $refer = new Refer($user, Refer::$REPLY);
                    $ret['new_reply'] = $refer->getNewNum();
                }
            }catch(ReferNullException $e){}
        }

        $this->set('no_html_data', $ret);
    }

    public function ajax_loginAction(){
        $this->cache(false);
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['form']['id']))
            $this->error(ECode::$LOGIN_NOID);
        if(!isset($this->params['form']['passwd']))
            $this->error(ECode::$LOGIN_ERROR);
        $id = trim($this->params['form']['id']);
        $pwd = $this->params['form']['passwd'];
        if($id == '')
            $this->error(ECode::$LOGIN_NOID);

        $cookieDate = 0;
        if(isset($this->params['form']['CookieDate']))
            $cookieDate = (int) $this->params['form']['CookieDate'];
        switch ($cookieDate) {
            case 1;
                $time = 86400; //24*60*60 sec
                break;
            case 2;
                $time = 2592000; //30*24*60*60 sec
                break;
            case 3:
                $time = 31536000; //365*24*60*60 sec
                break;
            default:
                $time = null;
        }
        try{
            NF_Session::getInstance()->login($id, $pwd, false, $time);
        }catch(LoginException $e){
            $this->error($e->getMessage());
        }
        $this->ajax_sessionAction();
    }

    public function ajax_logoutAction(){
        $this->cache(false);
        NF_Session::getInstance()->logout();
    }

    /**
     * check id status
     * 0 -- unused
     * 1 -- baned
     * 2 -- small
     * 3 -- baned
     * 4 -- exist
     * 5 -- long
     * @param String $id
     */
    public function ajax_valid_idAction(){
        if(!isset($this->params['url']['id']))
            $this->error();
        $ret = bbs_is_invalid_id(trim($this->params['url']['id']));
        $this->set('no_html_data', array('status'=>$ret));
    }

    public function ajax_queryAction(){
        $id = trim($this->params['id']);
        try{
            $u = User::getInstance($id);
        }catch(UserNullException $e){
            $this->error(ECode::$USER_NOID);
        }
        load("inc/wrapper");
        $wrapper = Wrapper::getInstance();
        $ret = $wrapper->user($u);
        $ret['status'] = $u->getStatus();
        $this->set('no_html_data', $ret);
    }

    public function infoAction(){
        $this->requestLogin();
        $this->notice[] = array("url"=>"/user/info", "text"=>"���������޸�");
        $this->css[] = "control.css";
        $this->js[] = "forum.control.js";

        Forum::setUserMode(BBS_MODE_EDITUFILE);

        $u = User::getInstance();
        $ret = array(
            "gender" => ($u->gender == 77)?1:2,
            "year" => intval($u->birthyear) + 1900,
            "month" => $u->birthmonth,
            "day" => $u->birthday,
            "myface" => nforum_html($u->getFace()),
            "myface_url" => nforum_html($u->userface_url),
            "myface_w" => $u->userface_width,
            "myface_h" => $u->userface_height,
            "qq" => nforum_html($u->OICQ),
            "msn" => nforum_html($u->MSN),
            "homepage" => nforum_html($u->homepage),
            "email" => nforum_html($u->reg_email),
            "sig" => nforum_html($u->getSignature())
        );
        $this->set($ret);

    }

    public function ajax_infoAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        extract($this->params['form']);
        try{
            $u->setInfo(intval($gender), intval($year), intval($month),intval($day),$email, $qq, $msn,  $homepage, 0, $furl, intval($fwidth), intval($fheight));
        }catch(UserSaveException $e){
            $this->error($e->getMessage());
        }
        $signature = nforum_iconv('utf-8', $this->encoding, $signature);
        $u->setSignature($signature);
        $ret['ajax_code'] = ECode::$USER_SAVEOK;
        $this->set('no_html_data', $ret);
    }

    /**
     * action for modify pwd and nickname
     */
    public function passwdAction(){
        $this->requestLogin();
        $this->notice[] = array("url"=>"/user/info", "text"=>"�ǳ������޸�");
        $this->css[] = "control.css";
        $this->js[] = "forum.control.js";

        $u = User::getInstance();
        $this->set("name", $u->username);
    }

    public function ajax_passwdAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        if(isset($this->params['form']['name'])){
            $name = $this->params['form']['name'];
            $name = nforum_iconv('UTF-8', $this->encoding, $name);
            //0 means modify forever
            if($u->setName($name)){
                $ret['ajax_code'] = ECode::$USER_NAMEOK;
                $this->set('no_html_data', $ret);
            }else{
                $this->error(ECode::$USER_NAMEERROR);
            }
        }else if(isset($this->params['form']['pold'])
            && isset($this->params['form']['pnew1'])
            && isset($this->params['form']['pnew2'])){
                $old = $this->params['form']['pold'];
                $new1 = $this->params['form']['pnew1'];
                $new2 = $this->params['form']['pnew2'];
                if($new1 !== $new2){
                    $this->error(ECode::$USER_PWDERROR);
                }
                if(!Forum::checkPwd($u->userid, $old, false, false)){
                    $this->error(ECode::$USER_OLDPWDERROR);
                }
                if(!$u->setPwd($new1))
                    $this->error(ECode::$USER_PWDERROR);
                $ret['ajax_code'] = ECode::$USER_PWDOK;
                $this->set('no_html_data', $ret);
        }
    }

    public function customAction(){
        $this->requestLogin();
        $this->css[] = "control.css";
        $this->js[] = "forum.control.js";
        $this->notice[] = array("url"=>"/user/custom", "text"=>"�û��Զ������");

        Forum::setUserMode(BBS_MODE_USERDEF);

        $u = User::getInstance();
        $list = c("user.custom");
        $ret = array();
        foreach($list as $k => $item){
            foreach($item as $v)
                $ret[] = array("name" => $v[1], "desc" => $v[2], "yes" => $v[3], "no" => $v[4], "val" => $u->getCustom($k, $v[0]), "id" => "{$k}_{$v[0]}");
        }
        $this->set("custom", $ret);
    }

    public function ajax_customAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        $list = c("user.custom");
        $val = array();
        foreach($list as $k => $item){
            $arr = array();
            foreach($item as $v){
                if(isset($this->params['form']["{$k}_{$v[0]}"])){
                    $arr[] = array("pos"=>$v[0], "val"=>intval($this->params['form']["{$k}_{$v[0]}"]));
                    if($k == "userdefine1" && $v[0] == 31 && intval($this->params['form']["{$k}_{$v[0]}"]) === 0){
                        load('model/widget');
                        Widget::w3to2($u);
                    }
                }
            }
            $val[$k] = $arr;
        }
        $u->setCustom($val);
        $ret['ajax_code'] = ECode::$USER_SAVEOK;
        $this->set('no_html_data', $ret);
    }

    /**
     * page for upload face in iframe
     * override the js array
     */
    public function ajax_faceAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        $face = c("user.face");

        //init upload file
        if(isset($this->params['url']['name'])){
            //html5 mode
            $tmp_name = tempnam(CACHE, "upload_");
            file_put_contents($tmp_name, file_get_contents('php://input'));
            $file = array(
                'tmp_name' => $tmp_name,
                'name' => nforum_iconv('utf-8', $this->encoding, $this->params['url']['name']),
                'size' => filesize($tmp_name),
                'error' => 0
            );
        }else if(isset($this->params['form']['file'])
            && is_array($this->params['form']['file'])){
            //flash mode
            $file = $this->params['form']['file'];
            $file['name'] = nforum_iconv('utf-8', $this->encoding, $file['name']);
        }else{
            $this->error(ECode::$ATT_NONE);
        }

        $errno = isset($file['error'])?$file['error']:UPLOAD_ERR_NO_FILE;
        switch($errno){
            case UPLOAD_ERR_OK:
                $tmpFile = $file['tmp_name'];
                $tmpName = $file['name'];
                if (!isset($tmp_name) && !is_uploaded_file($tmpFile)) {
                    $msg = "�ϴ�����";
                    break;
                }
                $ext = strrchr($tmpName, '.');
                if(!in_array(strtolower($ext), $face['ext'])){
                    $msg = "�ϴ��ļ���չ������";
                    break;
                }
                if(filesize($tmpFile) > $face['size']){
                    $msg = "�ļ���С��������" . $face['size']/1024 . "K";
                    break;
                }
                mt_srand();
                $faceDir = $face['dir'] . DS . strtoupper(substr($u->userid,0,1));
                $facePath = $faceDir . DS . $u->userid . "." . mt_rand(0, 10000) . $ext;
                $faceFullDir = WWW . DS . $faceDir;
                $faceFullPath = WWW . DS . $facePath;
                if(!is_dir($faceFullDir)){
                    @mkdir($faceFullDir);
                }
                if(is_file($faceFullPath)){
                    $msg = "�Ҿ���������������Ʊ��";
                    break;
                }
                if (isset($tmp_name)) {
                    if(!rename($tmp_name, $faceFullPath)){
                        $msg = "�ϴ�����";
                        break;
                    }
                }else if (!move_uploaded_file($tmpFile, $faceFullPath)) {
                    $msg = "�ϴ�����";
                    break;
                }

                load("inc/image");
                try{
                    $img = new Image($faceFullPath);
                    $format = $img->getFormat();
                    if(!in_array($format, range(1, 3))){
                        $msg = "�ϴ����ļ�ò�Ʋ���ͼ���ļ�";
                        break;
                    }
                    //gif do not thumbnail
                    if($format != 1){
                        $facePath = preg_replace("/\.[^.]+$/", '.jpg', $facePath);
                        $faceFullPath = WWW . DS . $facePath;
                        $img->thumbnail($faceFullPath, 120, 120);
                    }
                }catch(ImageNullException $e){
                    $msg = "�ϴ����ļ�ò�Ʋ���ͼ���ļ�";
                    break;
                }

                $this->set("no_html_data", array(
                    "img" => $facePath
                    ,"width" => $img->getWidth()
                    ,"height" => $img->getHeight()
                    ,"ajax_st" => 1
                    ,"ajax_code" =>ECode::$SYS_AJAXOK
                    ,"ajax_msg" => "�ļ��ϴ��ɹ�"
                ));
                return;
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $msg = "�ļ���С����ϵͳ����";
                break;
            case UPLOAD_ERR_PARTIAL:
                $msg = "�ļ��������";
                break;
            case UPLOAD_ERR_NO_FILE:
                $msg = "û���ļ��ϴ���";
                break;
            default:
                $msg = "δ֪����";
        }
        if(isset($tmp_name))
            @unlink($tmp_name);
        $this->set("no_html_data", array(
            "ajax_st" => 0
            ,"ajax_code" =>ECode::$SYS_AJAXERROR
            ,"ajax_msg" => $msg
        ));
    }
}
