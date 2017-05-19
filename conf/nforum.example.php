<?php
/********* app config ******************/
$export['application']['encoding'] = 'GBK';
$export['application']['debug'] = true;

/********* site config ******************/
$export['site']['name'] = 'NFORUM������̳';
#����: String
#����: վ������

$export['site']['desc'] = 'NFORUM������̳';
#����: String
#����: վ����⣬��վĬ�ϱ���Ϊname-desc

$export['site']['keywords'] = 'nforum kbs bbs';
#����: String
#����: վ��ؼ��֣�������html��head��

$export['site']['description'] = 'NFORUM������̳';
#����: String
#����: վ��������������html��head��

$export['site']['static'] = '';
#����: String
#����: ��Դ�ļ����������ڲ���Ҫcookie��֤����Դ�ļ��������ô��������ʡ����ֵΪ�����뵱ǰ������ͬ��

$export['site']['base'] = '';
#����: String
#����: ���nForum�ڷ�web��Ŀ¼�£����ڴ�����web����Ŀ¼����'/bbs'

$export['site']['home'] = '/default';

$export['site']['preIndex'] = true;
#����: Boolean
#����: �Ƿ����ý�վҳ�棬��guest�û����ʸ�����ʱ���Զ���ת����վҳ��/index

$export['site']['notice']['text'] = '����:&nbsp;������������һ������';
$export['site']['notice']['url'] = '';
#����: String
#����: nForum��ҳ��������&����

/********* module config ******************/
$export['modules']['install'] = array('index', 'vote', 'mobile', 'api');

$export['modules']['vote']['base'] = '/vote';

$export['modules']['mobile']['base'] = '/m';
$export['modules']['mobile']['domain'] = '';
#����: String
#����: Mobileģ��Ķ�������������http://�����ú�ʹ�ø�����ֱ�ӷ���Mobileģ�飬ע�⣺��������web��Ŀ¼��ָ��nForumĿ¼

$export['modules']['api']['base'] = '/api';
$export['modules']['api']['domain'] = '';
#����: String
#����: Apiģ��Ķ�������������http://�����ú�ʹ�ø�����ֱ�ӷ���Apiģ�飬ע�⣺��������web��Ŀ¼��ָ��nForumĿ¼

$export['modules']['api']['page_item_limit'] = 50;

/********* plugin config ******************/
$export['plugins']['install'] = array('uaacl', 'ipacl');

/********* view config ******************/
$export['view']['smarty']['compile_check'] = true;
#����: Boolean
#����: �Ƿ���ģ���ޡ����Ϊtrue���޸���ͼ�ļ����Զ����±���ģ��

$export['view']['smarty']['force_compile'] = false;
#����: Boolean
#����: �Ƿ�ǿ�Ʊ���ģ��

$export['view']['pack']['html'] = true;
#����: Boolean
#����: �Ƿ�ѹ��html
#
$export['view']['pack']['js'] = true;
#����: Boolean
#����: �Ƿ�ѹ��javascript

$export['view']['pack']['css'] = true;
#����: Boolean
#����: �Ƿ�ѹ��css

/********* adv config ******************/
$export['adv']['path'] = '/files/imgupload';

#id which can access advertisment managment page
$export['adv']['id'] = array('SYSOP');
#����: Array
#����: �ɷ��ʹ�����ϵͳ��id

/********* thumbnail config ******************/
$export['thumbnail']['small'] = array(120, null);
$export['thumbnail']['middle'] = array(240, null);
#����: Array
#����: ����ͼ����,keyΪ����ͼ���,valueΪ��Ⱥ͸߶�,��������null����

/********* ubb config ******************/
$export['ubb']['parse'] = true;
#����: Boolean
#����: �Ƿ����ubb����

$export['ubb']['syntax'] = '';
#����: String
#����: �﷨������SyntaxHighlighter3.x��Ŀ¼���ƣ����SyntaxHighlighter3.x����wwwĿ¼�£�Ϊ�ռ��������﷨����

/********* search config ******************/
$export['search']['site'] = false;
#����: Boolean
#����: �Ƿ���ȫվ����,�������,ֻ�й���Ա��ʹ��

$export['search']['max'] = 999;
#����: Int
#����: ����Ĭ���������ص����������

$export['search']['day'] = 7;
#����: Int
#����: ����Ĭ���������µ����������

/********* other config ******************/
$export['ajax']['check'] = true;
#����: Boolean
#����: �Ƿ���ajax�����header

$export['redirect']['time'] = 3;
#����: Int
#����: ajax�Ի�����ʧ��ִ��Ĭ�϶�����ʱ����

$export['cache']['second'] = 300;
#����: Int
#����: HTTP EXPIRES

$export['proxy']['X_FORWARDED_FOR'] = false;
#����: Boolean
#����: web������ǰ�˴��ڴ���ʱ��Ϊtrue

$export['elite']['root'] = '0Announce';

$export['refer']['enable'] = defined('BBS_ENABLE_REFER');

$export['rss']['num'] = 20;
#����: Int
#����: RSS�����������Ŀ��

$export['exif'] = array('Photo');
#����: Array
#����: ����ͼƬexif��Ϣ�İ���

/********* article config ******************/
$export['article']['ref_line'] = BBS_QUOTED_LINES;
$export['article']['quote_level'] = BBS_QUOTE_LEV;
$export['article']['att_num'] = BBS_MAXATTACHMENTCOUNT;
$export['article']['att_size'] = BBS_MAXATTACHMENTSIZE;
$export['article']['att_check'] = false;

/********* user config ******************/
$export['user']['face']['size'] = 1024 * 256;
#����: Int
#����: �û��Զ���ͷ���С���ƣ���λ�ֽ�

$export['user']['face']['dir'] = 'uploadFace';
#����: String
#����: �û��Զ���ͷ���ϴ�Ŀ¼��Ϊ����wForum����Ŀ¼����ΪwForum�����Ŀ¼

$export['user']['face']['ext'] = array('.jpg', '.jpeg', '.gif', '.png');
#����: Array
#����: �û��Զ���ͷ�������ʽ

$export['user']['custom']['userdefine0'] = array(
        array(29,'��ʾ��ϸ�û���Ϣ', '�Ƿ��������˿��������Ա����յ�����', '����', '������'));
//user_define2 is none after 1
$export['user']['custom']['userdefine1'] = array(
        array(0,'���� IP', '�Ƿ��ĺͱ���ѯ��ʱ�������Լ��� IP ��Ϣ','����','������'),
        array(2,'���� @ ����', '���������ᵽ(@)����ʱ���Ƿ���������Ϣ','����','������'),
        array(3,'���ûظ�����', '�����˻ظ����������ʱ���Ƿ���������Ϣ','����','������'),
        //bit-31 for column num of web 2 or 3
        array(31,'��ҳ����', '��ҳ��ʾ������','3&nbsp;&nbsp;','2'));
//mailbox_prop is none after 2
$export['user']['custom']['mailbox_prop'] = array(
        array(0,'����ʱ�����ż���������', '�Ƿ���ʱ�Զ�ѡ�񱣴浽������','����','������'),
        array(1,'ɾ���ż�ʱ�����浽������', '�Ƿ�ɾ���ż�ʱ�����浽������','������','����'),
        array(3,'�Զ�������������ʼ�', '�Ƿ��Զ�������������ʼ�','��','��'),
);

/********* pagination config ******************/
$export['pagination']['threads'] = 30;
#����: Int
#����: ����ÿҳ��ʾ��������

$export['pagination']['article'] = 10;
#����: Int
#����: ����ÿҳ��ʾ��������

$export['pagination']['mail'] = 20;
#����: Int
#����: ����ÿҳ��ʾ���ʼ���

$export['pagination']['friend'] = 20;
#����: Int
#����: ÿҳ��ʾ�ĺ�������

$export['pagination']['search'] = 80;
#����: Int
#����: �������ÿҳ��ʾ������

/********* cookie config ******************/
$export['cookie']['prefix'] = 'nforum';
#����: String
#����: cookieǰ׺������Ϊ��

#if cookie domain is empty, it will be the same as domain
$export['cookie']['domain'] = '';
#����: String
#����: cookie��Ĭ��Ϊ�վͺ�

$export['cookie']['path'] = '/';
#����: String
#����: cookie·��

$export['cookie']['encryption'] = false;
#����: Boolean
#����: �Ƿ��cookie��ip���ܣ����Ϊtrue������Ч�ķ�ֹxss���������ip�����仯cookieʧЧ

/********* section list ******************/
for($i = 0;$i <= BBS_SECNUM - 1;$i++){
    $export['section'][constant("BBS_SECCODE{$i}")] = array(constant("BBS_SECNAME{$i}_0"),constant("BBS_SECNAME{$i}_1"));
}

/********* widget config ******************/
$export['widget']['persistent'] = true;
#����: Boolean
#����: ��ҳwidget�Ƿ��ڷ������־û������Ϊtrue�������Զ�����widget��ֵ�����Ϊfalse��widget��ʹ��ajax��ʽ��ȡ���ݡ�

$export['widget']['core'] = array('board', 'section', 'favor');

/**
 * the extension widget config is
 * $export['widget']['ext'][category-index] = array(category-name, array(widget-list)[, array(default-file)])
 * it will be include default-file first and check whether have widget
 * if not it will include widget-name.php
 */
$export['widget']['ext']['0'] = array('��̳���', array('topten', 'recommend', 'bless'), array('classic'));
$export['widget']['ext']['1'] = array('����', array('weather', 'vote'));

//�ο��Լ���¼�û��ĳ�ʼ��ģ��
//example array('col', 'row', 'title'(array), color)
$export['widget']['default']['recommend'] = array('col'=>1, 'row'=>1);
$export['widget']['default']['section-0'] = array('col'=>1, 'row'=>2);
$export['widget']['default']['section-2'] = array('col'=>1, 'row'=>3);
$export['widget']['default']['section-4'] = array('col'=>1, 'row'=>4);
$export['widget']['default']['topten'] = array('col'=>2, 'row'=>1);
$export['widget']['default']['section-1'] = array('col'=>2, 'row'=>2);
$export['widget']['default']['section-3'] = array('col'=>2, 'row'=>3);
$export['widget']['default']['section-5'] = array('col'=>2, 'row'=>4);
$export['widget']['default']['weather'] = array('col'=>3, 'row'=>1);
$export['widget']['default']['vote'] = array('col'=>3, 'row'=>2);
$export['widget']['default']['bless'] = array('col'=>3, 'row'=>3);
$export['widget']['default']['section-6'] = array('col'=>3, 'row'=>4);
$export['widget']['color'] = array(
        0 => array('default', 'Ĭ��'),
        1 => array('red', '��'),
        2 => array('orange', '��'),
        3 => array('yellow', '��'),
        4 => array('green', '��'),
        5 => array('blue', '��'),
        6 => array('white', '��')
        );

/********* db config ******************/
$export['db']['dbms'] = 'mysql';
$export['db']['host'] = 'localhost';
$export['db']['port'] = '3306';

$export['db']['user'] = '';
$export['db']['pwd'] = '';
$export['db']['db'] = '';
#����: String
#����: ���ݿ�����

$export['db']['dsn'] = "{$export['db']['dbms']}:host={$export['db']['host']};port={$export['db']['port']};dbname={$export['db']['db']}";
$export['db']['charset'] = 'gbk';

/********* jsr config ******************/
$export['jsr']['mWidth'] = 1000;
#page in iframe
$export['jsr']['iframe'] = false;
#if jsr.iframe false,the allow page pattern(javascript)
$export['jsr']['allowFrame'] = '';
$export['jsr']['session']['timeout'] = 60;
$export['jsr']['redirect'] = $export['redirect']['time'];
$export['jsr']['keyboard'] = true;
$export['jsr']['GAPIKEY'] = '';
