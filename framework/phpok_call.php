<?php
/***********************************************************
	Filename: {phpok}/phpok_call.php
	Note	: PHPOK����������
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2013-04-20 17:42
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class phpok_call extends phpok_control
{
	private $mlist;
	function __construct()
	{
		parent::control();
		$this->mlist = get_class_methods($this);
	}

	//ִ�����ݵ���
	function phpok($id,$rs="")
	{
		if(!$id) return false;
		$cacheId = '';
		$content = '';
		if($rs && is_string($rs)) parse_str($rs,$rs);
		//�ж������ò������ǵ����������ĵ�����
		if(substr($id,0,1) != '_')
		{
			$call_rs = $GLOBALS['app']->model('call')->get_rs($id,$this->site['id']);
			if(!$call_rs) return false;
			if($call_rs['ext'])
			{
				$call_rs_ext = unserialize($call_rs['ext']);
				unset($call_rs['ext'],$call_rs['id']);
				if($call_rs_ext) $call_rs = array_merge($call_rs_ext,$call_rs);
			}
			if($rs && is_array($rs)) $call_rs = array_merge($call_rs,$rs);
		}
		else
		{
			if(!$rs || !is_array($rs)) return false;
			//arclist�������б�
			//arc����ƪ������Ϣ
			//cate��������Ϣ
			//catelist��������
			//project����Ŀ��Ϣ
			//sublist������Ŀ��Ϣ
			//parent��������Ŀ��Ϣ
			//plist��ͬ����Ŀ��Ϣ
			//fields���ֶα�
			//user����Ա
			//userlist����Ա�б�
			//total����������
			//cate_id����ǰ������Ϣ��������Ŀ�����������ӣ�
			//subcate���ӷ�����Ϣ������ǰ�����µ��ӷ���
			$list = array('arclist','arc','cate','catelist','project','sublist','parent','plist','fields','user','userlist','total','cate_id','subcate');
			$id = substr($id,1);
			//�����arclist����δ����is_list���ԣ���Ĭ�����ô�����
			if($id == "arclist")
			{
				$rs["is_list"] = $rs["is_list"] == 'false' ? 0 : 1;
			}
			if(!$id || !in_array($id,$list)) return false;
			$call_rs = array_merge($rs,array('type_id'=>$id));
		}
		return $this->load_call($call_rs);
	}

	function load_call($rs)
	{
		$content = "";
		$tmp = '_'.$rs['type_id'];
		if(in_array($tmp,$this->mlist))
		{
			$content = $this->$tmp($rs);
		}
		return $content;
	}

	//��ȡ�����б�
	function _arclist($rs)
	{
		return $GLOBALS['app']->model('data')->arclist($rs);
	}

	function _total($rs)
	{
		return $GLOBALS['app']->model('data')->total($rs);
	}

	//��ȡ��ƪ����
	function _arc($rs)
	{
		return $GLOBALS['app']->model('data')->arc($rs);
	}

	//ȡ����Ŀ��Ϣ
	function _project($rs)
	{
		return $GLOBALS['app']->model('data')->project($rs);
	}

	//��ȡ������
	function _catelist($rs)
	{
		return $GLOBALS['app']->model('data')->catelist($rs);
	}

	//��ȡ��ǰ������Ϣ
	function _cate($rs)
	{
		return $GLOBALS['app']->model('data')->cate($rs);
	}

	function _cate_id($rs)
	{
		return $GLOBALS['app']->model('data')->cate_id($rs);
	}

	//ȡ����Ŀ��չ�ֶ�
	function _fields($rs)
	{
		return $GLOBALS['app']->model('data')->fields($rs);
	}

	//ȡ����һ����Ŀ
	function _parent($rs)
	{
		return $GLOBALS['app']->model('data')->_project_parent($rs);
	}

	//��ȡ��ǰ��Ŀ�µ�����Ŀ��֧�ֶ༶
	function _sublist($rs)
	{
		return $GLOBALS['app']->model('data')->sublist($rs);
	}

	//��ȡ��ǰ�����µ��ӷ���
	function _subcate($rs)
	{
		return $GLOBALS['app']->model('data')->subcate($rs);
	}
}
?>