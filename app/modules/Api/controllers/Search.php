<?php
class SearchController extends NF_ApiController {

    public function boardAction(){
        load('model/board');
        $b = isset($this->params['url']['board'])?$this->params['url']['board']:"";
        $boards = Board::search(trim($b));

        $wrapper = Wrapper::getInstance();

        $data = array();
        $data['board'] = $data['section'] = array();
        foreach($boards as $brd){
            if($brd->isDir())
                $data['section'][] = $brd->NAME;
            else
                $data['board'][] = $wrapper->board($brd, array('status'=>true));
        }
        $this->set('data', $data);
    }

    public function articleAction(){
        load(array('model/board', 'model/article', 'inc/pagination'));
        $day = 7;
        $title1 = $title2 = $title3 = $author = $o = '';

        if(isset($this->params['url']['title1']))
            $title1 = nforum_iconv($this->encoding, 'GBK', rawurldecode(trim($this->params['url']['title1'])));
        if(isset($this->params['url']['title2']))
            $title2 = nforum_iconv($this->encoding, 'GBK', rawurldecode(trim($this->params['url']['title2'])));
        if(isset($this->params['url']['titlen']))
            $title3 = nforum_iconv($this->encoding, 'GBK', rawurldecode(trim($this->params['url']['titlen'])));
        if(isset($this->params['url']['author']))
            $author = trim($this->params['url']['author']);
        if(isset($this->params['url']['day']))
            $day = intval($this->params['url']['day']);
        $m = isset($this->params['url']['m']) && $this->params['url']['m'] == '1';
        $a = isset($this->params['url']['a']) && $this->params['url']['a'] == '1';
        $o = isset($this->params['url']['o']) && $this->params['url']['o'] == '1';
        $res = array();
        if(!isset($this->params['url']['board']))
            $this->error(ECode::$BOARD_UNKNOW);
        $board = $this->params['url']['board'];
        try{
            $brd = Board::getInstance($board);
            $res = array_merge($res, Article::search($brd, $title1, $title2, $title3, $author, $day, $m, $a, $o));
        }catch(BoardNullException $e){
        }

        $count = isset($this->params['url']['count'])?$this->params['url']['count']:c("pagination.threads");
        if(($count = intval($count)) <= 0)
            $count = c("pagination.threads");
        if($count > c('modules.api.page_item_limit'))
            $count = c("pagination.threads");
        $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
        $page = intval($page);
        $pagination = new Pagination(new ArrayPageableAdapter($res), $count);
        $articles = $pagination->getPage($page);
        $wrapper = Wrapper::getInstance();
        $data = array();
        $data['pagination'] = $wrapper->page($pagination);
        foreach($articles as $v){
            $data['article'][] = $wrapper->article($v, array('threads' => false));
        }
        $this->set('data', $data);
    }

    public function threadsAction(){
        load(array('model/board', 'model/threads', 'inc/pagination'));
        $day = 7;
        $title1 = $title2 = $title3 = $author = '';

        if(isset($this->params['url']['title1']))
            $title1 = nforum_iconv($this->encoding, 'GBK', rawurldecode(trim($this->params['url']['title1'])));
        if(isset($this->params['url']['title2']))
            $title2 = nforum_iconv($this->encoding, 'GBK', rawurldecode(trim($this->params['url']['title2'])));
        if(isset($this->params['url']['titlen']))
            $title3 = nforum_iconv($this->encoding, 'GBK', rawurldecode(trim($this->params['url']['titlen'])));
        if(isset($this->params['url']['author']))
            $author = trim($this->params['url']['author']);
        if(isset($this->params['url']['day']))
            $day = intval($this->params['url']['day']);
        $m = isset($this->params['url']['m']) && $this->params['url']['m'] == '1';
        $a = isset($this->params['url']['a']) && $this->params['url']['a'] == '1';
        $return =  c('search.max');
        $res = array();
        if(!isset($this->params['url']['board']))
            $this->error(ECode::$BOARD_UNKNOW);
        $board = $this->params['url']['board'];
        try{
            $brd = Board::getInstance($board);
            $res = array_merge($res, Threads::search($brd, $title1, $title2, $title3, $author, $day, $m, $a, $return));
        }catch(BoardNullException $e){
        }

        $count = isset($this->params['url']['count'])?$this->params['url']['count']:c("pagination.threads");
        if(($count = intval($count)) <= 0)
            $count = c("pagination.threads");
        if($count > c('modules.api.page_item_limit'))
            $count = c("pagination.threads");
        $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
        $page = intval($page);
        $pagination = new Pagination(new ArrayPageableAdapter($res), $count);
        $articles = $pagination->getPage($page);
        $wrapper = Wrapper::getInstance();
        $data = array();
        $data['pagination'] = $wrapper->page($pagination);
        foreach($articles as $v){
            $data['threads'][] = $wrapper->article($v, array('threads' => true));
        }
        $this->set('data', $data);
    }
}
