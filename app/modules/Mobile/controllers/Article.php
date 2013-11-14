<?php
load('model/article');
class ArticleController extends NF_MobileController {

    private $_board;

    public function init(){
        parent::init();
        if(!isset($this->params['name'])){
            $this->error(ECode::$BOARD_NONE);
        }
        try{
            load('model/board');
            $boardName = $this->params['name'];
            $this->_board = Board::getInstance($boardName);
        }catch(BoardNullException $e){
            $this->error(ECode::$BOARD_UNKNOW);
        }
        if(isset($this->params['mode'])){
            $mode = (int)trim($this->params['mode']);
            $this->_board->setMode($mode);
        }
        if(!$this->_board->hasReadPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPERM);
        }
        $this->_board->setOnBoard();
    }

    public function indexAction(){
        $this->notice = "����-{$this->_board->DESC}({$this->_board->NAME})";

        load(array("inc/pagination", "inc/astro"));
        try{
            load('model/threads');
            $gid = $this->params['gid'];
            $threads = Threads::getInstance($gid, $this->_board);
        }catch(ThreadsNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }

        //article jump
        if(isset($this->params['url']['s'])){
            $article = $threads->getArticleById(intval($this->params['url']['s']));
            if(null !== $article){
                $pos = $article->getPos();
                $page = ceil(($pos + 1) / c("pagination.article"));
                $this->redirect("{$this->_mbase}/article/{$this->_board->NAME}/{$gid}?p={$page}#a{$pos}");
            }
            $this->redirect("{$this->_mbase}/article/{$this->_board->NAME}/{$gid}");
        }

        //filter author
        $auF = $au = false;
        if(isset($this->params['url']['au'])){
            $tmp = $threads->getRecord(1, $threads->getTotalNum());
            $auF = array();$au = trim($this->params['url']['au']);
            foreach($tmp as $v){
                if($v->OWNER == $au)
                    $auF[] = $v;
            }
            $auF = new ArrayPageableAdapter($auF);
        }
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
        $pagination = new Pagination(false !== $au?$auF:$threads, c("pagination.article"));
        $articles = $pagination->getPage($p);

        $u = User::getInstance();
        $bm = $u->isBM($this->_board) || $u->isAdmin();
        $info = array();
        foreach($articles as $v){
            $content = $v->getPlant();
            $content = preg_replace("/&nbsp;/", " ", $content);
            $content = preg_replace("/  /", "&nbsp;&nbsp;", $content);
            preg_match("|�� �޸�:��([\S]+) .*?FROM:[\s]*([0-9a-zA-Z.:*]+)|", $content, $m);
            preg_match("|�� ��Դ:.*FROM:[\s]*([0-9a-zA-Z.:*]+)|", $content, $f);
            $m = empty($m)?"":"<br />�޸�:{$m[1]} FROM {$m[2]}";
            $f = empty($f)?"":"<br />FROM {$f[1]}";
            $s = (($pos = strpos($content, "<br/><br/>")) === false)?0:$pos + 10;
            $e = (($pos = strpos($content, "<br/>--<br/>")) === false)?strlen($content):$pos + 7;
            $content = preg_replace(
                array("'^(<br/>)+'", "|(<br/>)+--$|")
                ,array("", "<br>--")
                ,substr($content, $s, $e - $s)
            ) . $m . $f;
            $content = $v->parseAtt($content, 'middle');
            if(c("ubb.parse")){
                load('inc/ubb');
                $content = XUBB::parse($content);
            }
            bbs_brcaddread($this->_board->NAME, $v->ID);
            $info[] = array(
                "id" => $v->ID,
                "op" => ($v->OWNER == $u->userid || $bm)?1:0,
                "time" => date("Y-m-d H:i:s", $v->POSTTIME),
                "pos" => $v->getPos(),
                "poster" => $v->OWNER,
                "content" => $content,
                "subject" => $v->isSubject()
            );
        }
        $this->set("bName", $this->_board->NAME);
        $this->set("anony", $this->_board->isAnony());
        $this->set("canPost", $this->_board->hasPostPerm($u));
        $this->set("info", $info);
        $this->set("gid", $threads->GROUPID);
        $this->set("title", nforum_html($threads->TITLE));
        $this->set("curPage", $pagination->getCurPage());
        $this->set("totalPage", $pagination->getTotalPage());
        $this->set("au", $au);
        //for the quick reply, raw encode the space
        $this->set("reid", $threads->ID);
        if(!strncmp($threads->TITLE, "Re: ", 4))
            $reTitle = $threads->TITLE;
        else
            $reTitle = "Re: " . $threads->TITLE;
        $this->set("reTitle", rawurlencode($reTitle));
    }

    public function singleAction(){
        $this->notice = "����-{$this->_board->DESC}({$this->_board->NAME})";

        if($this->_board->getMode() == BOARD::$THREAD)
            $this->_board->setMode(BOARD::$NORMAL);
        try{
            $gid = $this->params['gid'];
            $article = Article::getInstance($gid, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }

        $u = User::getInstance();
        $bm = $u->isBM($this->_board) || $u->isAdmin();
        $info = array();
        $content = $article->getPlant();
        $content = preg_replace("/&nbsp;/", " ", $content);
        $content = preg_replace("/  /", "&nbsp;&nbsp;", $content);
        preg_match("|�� �޸�:��([\S]+) .*?FROM:[\s]*([0-9a-zA-Z.:*]+)|", $content, $m);
        preg_match("|�� ��Դ:.*FROM:[\s]*([0-9a-zA-Z.:*]+)|", $content, $f);
        $m = empty($m)?"":"<br />�޸�:{$m[1]} FROM {$m[2]}";
        $f = empty($f)?"":"<br />FROM {$f[1]}";
        $s = (($pos = strpos($content, "<br/><br/>")) === false)?0:$pos + 10;
        $e = (($pos = strpos($content, "<br/>--<br/>")) === false)?strlen($content):$pos + 7;
        $content = preg_replace(
            array("'^(<br/>)+'", "|(<br/>)+--$|")
            ,array("", "<br>--")
            ,substr($content, $s, $e - $s)
        ) . $m . $f;
        $content = $article->parseAtt($content, 'middle');
        if(c("ubb.parse")){
            load('inc/ubb');
            $content = XUBB::parse($content);
        }
        bbs_brcaddread($this->_board->NAME, $article->ID);
        $info = array(
            "aid" => $article->ID,
            "gid" => $article->GROUPID,
            "rid" => $article->REID,
            "op" => ($article->OWNER == $u->userid || $bm)?1:0,
            "time" => date("Y-m-d H:i:s", $article->POSTTIME),
            "poster" => $article->OWNER,
            "content" => $content,
            "subject" => $article->isSubject(),
            "pre" => ($a = $article->pre())?$a->ID:false,
            "next" => ($a = $article->next())?$a->ID:false,
            "tPre" => ($a = $article->tPre())?$a->ID:false,
            "tNext" =>($a = $article->tNext())?$a->ID:false
        );
        $this->set("bName", $this->_board->NAME);
        $this->set("mode", $this->_board->getMode());
        $this->set("canPost", $this->_board->hasPostPerm($u));
        $this->set("title", nforum_html($article->TITLE));
        $this->set($info);
    }

    public function postAction(){
        if($this->_board->isReadOnly()){
            $this->error(ECode::$BOARD_READONLY);
        }
        if(!$this->_board->hasPostPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPOST);
        }
        $article = false;
        if(isset($this->params['gid'])){
            $reID = (int) $this->params['gid'];
            if($this->_board->isNoReply())
                $this->error(ECode::$BOARD_NOREPLY);
            try{
                $article = Article::getInstance($reID, $this->_board);
            }catch(ArticleNullException $e){
                $this->error(ECode::$ARTICLE_NOREID);
            }
            if($article->isNoRe())
                $this->error(ECode::$ARTICLE_NOREPLY);
        }else{
            if($this->_board->isTmplPost())
                $this->error(ECode::$TMPL_ERROR);
            $reID = 0;
        }
        $single = (isset($this->params['url']['s']) || isset($this->params['form']['s']));

        if($this->getRequest()->isPost()){
            if(!isset($this->params['form']['subject']))
                $this->error(ECode::$POST_NOSUB);
            if(!isset($this->params['form']['content']))
                $this->error(ECode::$POST_NOCON);
            $subject = trim($this->params['form']['subject']);
            $content = trim($this->params['form']['content']);
            $subject = nforum_iconv($this->encoding, 'GBK', $subject);
            $content = nforum_iconv($this->encoding, 'GBK', $content);
            $subject = rawurldecode($subject);
            $sig = User::getInstance()->signature;
            $email = 0;$anony = null;$outgo = 0;
            if(isset($this->params['form']['email']))
                $email = 1;
            if(isset($this->params['form']['anony']) && $this->_board->isAnony())
                $anony = 1;
            if(isset($this->params['form']['outgo']) && $this->_board->isOutgo())
                $outgo = 1;
            try{
                if(false === $article)
                    Article::post($this->_board, $subject, $content, $sig, $email, $anony, $outgo);
                else
                    $article->reply($subject, $content, $sig, $email, $anony, $outgo);
            }catch(ArticlePostException $e){
                $this->error($e->getMessage());
            }
            $this->redirect($this->_mbase . "/board/" . $this->_board->NAME . ($single?"/0":"") . "?m=" . ECode::$POST_OK);
        }else{
            $reTitle = $reContent = "";
            if($reID != 0){
                $this->notice = "{$this->_board->DESC}-�ظ�";
                $reContent = "\n".$article->getRef();
                //remove ref ubb tag
                load('inc/ubb');
                $reContent = XUBB::remove($reContent);
                if(!strncmp($article->TITLE, "Re: ", 4))
                    $reTitle = $article->TITLE;
                else
                    $reTitle = "Re: " . $article->TITLE;
            }else{
                $this->notice = "{$this->_board->DESC}-����";
            }
        }
        $this->set("single", $single);
        $this->set("bName", $this->_board->NAME);
        $this->set("email", true);
        $this->set("anony", $this->_board->isAnony());
        $this->set("outgo", $this->_board->isOutgo());
        $this->set("title", (string)$reTitle);
        $this->set("content", (string)$reContent);
        $this->set("reid", $reID);
    }

    public function forwardAction(){
        $this->requestLogin();
        if(!isset($this->params['gid']))
            $this->error(ECode::$ARTICLE_NONE);
        $single = (isset($this->params['url']['s']) || isset($this->params['form']['s']));
        if($this->getRequest()->isPost()){
            if(!isset($this->params['form']['target']))
                $this->error(ECode::$USER_NONE);
            $gid = intval($this->params['gid']);
            $target = trim($this->params['form']['target']);
            $threads = isset($this->params['form']['threads']);
            $noref = isset($this->params['form']['noref']);
            $noatt = isset($this->params['form']['noatt']);
            $noansi = isset($this->params['form']['noansi']);
            $big5 = isset($this->params['form']['big5']);
            try{
                $gid = $this->params['gid'];
                $article = Article::getInstance($gid, $this->_board);
                if($threads){
                    load('model/threads');
                    $t = Threads::getInstance($article->GROUPID, $this->_board);
                    $t->forward($target, $t->ID, $noref, $noatt, $noansi, $big5);
                }else{
                    $article->forward($target, $noatt, $noansi, $big5);
                }
            }catch(ArticleNullException $e){
                $this->error(ECode::$ARTICLE_NONE);
            }catch(ArticleForwardException $e){
                $this->error($e->getMessage());
            }
            $this->redirect($this->_mbase . "/board/" . $this->_board->NAME . ($single?"/0":"") . "?m=" . ECode::$ARTICLE_FORWARDOK);
        }

        $this->notice = "{$this->_board->DESC}-ת��";
        load(array("model/friend"));
        $f = new Friend(User::getInstance());
        $friends = $f->getRecord(1, $f->getTotalNum());
        $ret = array();
        foreach($friends as $v){
            $ret[] = $v->userid;
        }
        $this->set('friends', $ret);
        $this->set('bName', $this->_board->NAME);
        $this->set('gid',$this->params['gid']);
        $this->set("single", $single);
    }

    public function editAction(){
        if($this->_board->isReadOnly()){
            $this->error(ECode::$BOARD_READONLY);
        }
        if(!$this->_board->hasPostPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPOST);
        }
        if(!isset($this->params['gid']))
            $this->error(ECode::$ARTICLE_NONE);
        $id = (int)$this->params['gid'];
        try{
            $article = Article::getInstance($id, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        if(!$article->hasEditPerm(User::getInstance()))
            $this->error(ECode::$ARTICLE_NOEDIT);
        $single = (isset($this->params['url']['s']) || isset($this->params['form']['s']));
        if($this->getRequest()->isPost()){
            $subject = trim($this->params['form']['subject']);
            $content = trim($this->params['form']['content']);
            $subject = nforum_iconv($this->encoding, 'GBK', $subject);
            $content = nforum_iconv($this->encoding, 'GBK', $content);
            $subject = rawurldecode($subject);
            if(!$article->update($subject, $content))
                $this->error(ECode::$ARTICLE_EDITERROR);
            $this->redirect($this->_mbase . "/board/" . $this->_board->NAME . ($single?"/0":"") . "?m=" . ECode::$ARTICLE_EDITOK);
        }else{
            $this->notice = "{$this->_board->DESC}-�༭";
            $title = $article->TITLE;
            $content = $article->getContent();
        }

        $this->set("bName", $this->_board->NAME);
        $this->set("email", false);
        $this->set("anony", false);
        $this->set("outgo", false);
        $this->set("title", $title);
        $this->set("content", $content);
        $this->set("single", $single);
        $this->set("reid", $id);
        $this->set("edit", true);
        $this->render("post");
    }

    public function deleteAction(){
        $u = User::getInstance();
        if(isset($this->params['gid'])){
            try{
                $a = Article::getInstance(intval($this->params['gid']), $this->_board);
                if(!$a->hasEditPerm($u))
                    $this->error(ECode::$ARTICLE_NODEL);
                if(!$a->delete())
                    $this->error(ECode::$ARTICLE_NODEL);
            }catch(ArticleNullException $e){
                $this->error(ECode::$ARTICLE_NONE);
            }
        }
        $single = (isset($this->params['url']['s']) || isset($this->params['form']['s']));
        $this->redirect($this->_mbase . "/board/" . $this->_board->NAME . ($single?"/0":"") . "?m=" . ECode::$ARTICLE_DELOK);
    }
}