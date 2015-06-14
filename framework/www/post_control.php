<?php
/***********************************************************
	Filename: {phpok}/model/post_control.php
	Note	: 表单系统
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2013年6月5日
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class post_control extends phpok_control
{
	function __construct()
	{
		parent::control();
		$this->model('popedom')->siteid($this->site['id']);
		$groupid = $this->model('usergroup')->group_id($_SESSION['user_id']);
		if(!$groupid)
		{
			error(P_Lang('无法获取前端用户组信息，请检查'),'','error');
		}
		$this->user_groupid = $groupid;
	}

	//内容发布页
	function index_f()
	{
		$id = $this->get("id");
		$pid = $this->get('pid','int');
		if(!$id && !$pid){
			error(P_Lang('未指定项目'),'','error');
		}
		$project_rs = $this->call->phpok('_project',array("phpok"=>$id,'pid'=>$pid));
		if(!$project_rs || !$project_rs['module']){
			error(P_Lang("项目不符合要求"),'','error');
		}
		if(!$project_rs['post_status']){
			error(P_Lang('项目未启用发布功能，联系管理员启用此功能'),'','error');
		}
		$project_rs['url'] = $this->url('post',$project_rs['identifier']);
		$this->assign("page_rs",$project_rs);
		if(!$this->model('popedom')->check($project_rs['id'],$this->user_groupid,'post')){
			error(P_Lang('您没有权限执行此操作'),'','error');
		}
		//绑定分类信息
		if($project_rs['cate']){
			$catelist = array();
			$cate_all = $this->model("cate")->cate_all($project_rs['site_id']);
			$this->model("cate")->sublist($catelist,$project_rs['cate'],$cate_all);
			$this->assign("catelist",$catelist);
		}
		$cateid = $this->get("cateid","int");
		if($cateid){
			$cate_rs = $this->model('data')->cate(array('pid'=>$project_rs['id'],'cateid'=>$cateid,'cate_ext'=>true));
			$this->assign("cate_rs",$cate_rs);
		}else{
			$cate = $this->get('cate');
			if($cate){
				$cate_rs = $this->model('data')->cate(array('pid'=>$project_rs['id'],'cate'=>$cate,'cate_ext'=>true));
				$this->assign("cate_rs",$cate_rs);
			}
		}
		
		//扩展字段
		$ext_list = $this->model('module')->fields_all($project_rs["module"],"identifier");
		$extlist = array();
		foreach(($ext_list ? $ext_list : array()) AS $key=>$value){
			if(!$value['is_front']){
				continue;
			}
			if($value["ext"]){
				$ext = unserialize($value["ext"]);
				foreach($ext AS $k=>$v){
					$value[$k] = $v;
				}
			}
			$extlist[] = $this->lib('form')->format($value);
		}
		$this->assign("extlist",$extlist);
		$tpl = $project_rs['post_tpl'] ? $project_rs['post_tpl'] : $project_rs['identifier'].'_post';
		if(!$this->tpl->check_exists($tpl)){
			error('未配置发布模板，联系管理员进行配置');
		}
		//返回上一级网址
		$_back = $this->get("_back");
		if(!$_back) $_back = $_SERVER["HTTP_REFERER"];
		if(!$_back){
			$_back = $this->url($project_rs['identifier'],$cate_rs['identifier']);
		}
		$this->assign('_back',$_back);
		$this->view($tpl);
	}

	//编辑个人信息
	function edit_f()
	{
		if(!$_SESSION['user_id'])
		{
			error('非会员不能操作此信息',$this->url,'error',10);
		}
		$_back = $this->get("_back");
		if(!$_back)
		{
			$_back = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $this->url;
		}
		$id = $this->get('id','int');
		if(!$id)
		{
			error('未指定ID',$_back,'error');
		}
		$this->assign('id',$id);
		$rs = $this->model('data')->arc(array('id'=>$id));
		if(!$rs)
		{
			error('内容信息不存在',$_back,'error');
		}
		if($rs['user_id'] != $_SESSION['user_id'])
		{
			error('您没有修改此内容权限',$_back,'error');
		}
		

		//获取项目信息
		$project_rs = $this->model('data')->project(array('pid'=>$rs['project_id']));
		//$project_rs = $this->call->phpok('_project',array("pid"=>$rs['project_id']));
		if(!$project_rs || !$project_rs['module'])
		{
			error("项目不符合要求！",$_back,'error',10);
		}
		$project_rs['url'] = $this->url('post','edit','id='.$id);
		$this->assign("page_rs",$project_rs);

		//绑定分类信息
		if($project_rs['cate'])
		{
			$catelist = array();
			$cate_all = $this->model("cate")->cate_all($project_rs['site_id']);
			$this->model("cate")->sublist($catelist,$project_rs['cate'],$cate_all);
			$this->assign("catelist",$catelist);
		}
		
		$cate = $this->get("cate");
		$cateid = $this->get("cateid","int");
		if($cate)
		{
			$cate_rs = $this->model("cate")->get_one($cate,"identifier",$project_rs['site_id']);
		}
		if(!$cate_rs && $cateid)
		{
			$cate_rs = $this->model("cate")->get_one($cateid,"id",$project_rs['site_id']);
		}
		$this->assign("cate_rs",$cate_rs);
		//扩展字段
		$ext_list = $this->model('module')->fields_all($project_rs["module"],"identifier");
		$extlist = array();
		foreach(($ext_list ? $ext_list : array()) AS $key=>$value)
		{
			if($value["ext"])
			{
				$ext = unserialize($value["ext"]);
				foreach($ext AS $k=>$v)
				{
					$value[$k] = $v;
				}
			}
			//绑定内容
			$value['content'] = $rs[$value['identifier']];
			//
			$extlist[] = $this->lib('form')->format($value);
		}
		$this->assign("extlist",$extlist);
		$this->assign('rs',$rs);
		$this->view($project_rs["identifier"]."_post");
	}
	
	function iframe_f()
	{
		$id = $this->get("id");
		$p_rs = $this->check($id,true);
		$this->assign("id",$id);
		$this->assign("page_rs",$p_rs);
		$this->view($p_rs["identifier"]."_post_iframe");
		//$this->view($p_rs["tpl_post_iframe"]);
	}

	function save_f()
	{
		$id = $this->get("id");
		$chk_rs = $this->check($id);
		if($chk_rs['status'] != "ok")
		{
			error($chk_rs['inof'],"","error");
		}
		$_back = $this->get("_back");
		if(!$_back) $_back = $_SERVER["HTTP_REFERER"];
		if(!$_back)
		{
			$_back = $this->url("post")."&id=".$id;
		}
		$m_rs = $this->model('module')->get_one($p_rs["module"]);
		
		$title = $this->get("title");
		if(!$title)
		{
			$note = $p_rs["alias_title"] ? $p_rs["alias_title"] : "主题";
			error($note."不能为空",$_back,"error");
		}
		$array = array();
		$array["title"] = $title;
		$array["dateline"] = $this->system_time;
		$array["status"] = 0;
		$array["hidden"] = 0;
		$array["module_id"] = $p_rs["module"];
		$array["project_id"] = $p_rs["id"];
		$array["site_id"] = $p_rs["site_id"];
		$array["cate_id"] = $this->get("cate_id","int");
		$insert_id = $this->model('list')->save($array);
		if(!$insert_id)
		{
			error("数据存储失败，请联系管理",$_back,"error");
		}
 		$ext_list = $this->model('module')->fields_all($p_rs["module"]);
 		$tmplist = array();
 		$tmplist["id"] = $insert_id;
 		$tmplist["site_id"] = $p_rs["site_id"];
 		$tmplist["project_id"] = $p_rs["id"];
 		$tmplist["cate_id"] = $array["cate_id"];
		if($ext_list)
 		{
			foreach($ext_list AS $key=>$value)
			{
				$val = ext_value($value);
				if($value["ext"])
				{
					$ext = unserialize($value["ext"]);
					foreach($ext AS $k=>$v)
					{
						$value[$k] = $v;
					}
				}
				if($value["form_type"] == "password")
				{
					$content = $rs[$value["identifier"]] ? $rs[$value["identifier"]] : $value["content"];
					$val = ext_password_format($val,$content,$value["password_type"]);
				}
				$tmplist[$value["identifier"]] = $val;
			}
 		}
		$this->model('list')->save_ext($tmplist,$p_rs["module"]);
		//存储扩展字段
		$identifier = "content-".$insert_id;
 		$i_array = array();
 		$i_array["id"] = $insert_id;
 		$i_array["site_id"] = $p_rs["site_id"];
 		$i_array["phpok"] = $identifier;
 		$i_array["type_id"] = "content";
 		$this->model("id");
 		$this->model('id')->save($i_array);
		error("数据存储成功，请等待管理员审核！",$_back,"ok");
	}

	function ajax_save_f()
	{
		$id = $this->get("id");
		$chk_rs = $this->check($id);
		if($chk_rs["status"] != "ok")
		{
			json_exit($chk_rs["info"]);
		}
		$p_rs = $chk_rs["info"];
		$m_rs = $this->model('module')->get_one($p_rs["module"]);
		$title = $this->get("title");
		if(!$title)
		{
			$note = $p_rs["alias_title"] ? $p_rs["alias_title"] : "主题";
			json_exit($note."不能为空");
		}
		//唯一性验证
		$_chk = $this->get("_chk");
		if($_chk)
		{
			if($_chk == 'title')
			{
				$sql = "SELECT id FROM ".$this->db->prefix."list WHERE project_id='".$p_rs['id']."' AND site_id='".$p_rs['site_id']."'";
				$sql.= " AND title='".$title."' AND module_id='".$p_rs['module']."' LIMIT 1";
			}
			else
			{
				$tmp = $this->get($_chk);
				if(!$tmp) json_exit("验证不通过，必填项目不能为空");
				$sql = "SELECT id FROM ".$this->db->prefix."list_".$p_rs["module"]." WHERE project_id='".$p_rs['id']."' ";
				$sql.= "AND site_id='".$p_rs['site_id']."' AND ".$_chk."='".$tmp."' LIMIT 1";
			}
			$chk = $this->db->get_one($sql);
			if($chk) json_exit("验证不通过，信息已存在");
		}
		$array = array();
		$array["title"] = $title;
		$array["dateline"] = $this->system_time;
		$array["status"] = 0;
		$array["hidden"] = 0;
		$array["module_id"] = $p_rs["module"];
		$array["project_id"] = $p_rs["id"];
		$array["site_id"] = $p_rs["site_id"];
		$array["cate_id"] = $this->get("cate_id","int");
		$insert_id = $this->model('list')->save($array);
		if(!$insert_id)
		{
			json_exit("数据存储失败，请联系管理");
		}
		$ext_list = $this->model('module')->fields_all($p_rs["module"]);
		$tmplist = array();
		$tmplist["id"] = $insert_id;
		$tmplist["site_id"] = $p_rs["site_id"];
		$tmplist["project_id"] = $p_rs["id"];
		$tmplist["cate_id"] = $array["cate_id"];
		if($ext_list)
		{
			foreach($ext_list AS $key=>$value)
			{
				$val = ext_value($value);
				if($value["ext"])
				{
					$ext = unserialize($value["ext"]);
					foreach($ext AS $k=>$v)
					{
						$value[$k] = $v;
					}
				}
				if($value["form_type"] == "password")
				{
					$content = $rs[$value["identifier"]] ? $rs[$value["identifier"]] : $value["content"];
					$val = ext_password_format($val,$content,$value["password_type"]);
				}
				$tmplist[$value["identifier"]] = $val;
			}
		}
		$this->model('list')->save_ext($tmplist,$p_rs["module"]);
		//存储扩展字段
		$identifier = "content-".$insert_id;
		$i_array = array();
		$i_array["id"] = $insert_id;
		$i_array["site_id"] = $p_rs["site_id"];
		$i_array["phpok"] = $identifier;
		$i_array["type_id"] = "content";
		$this->model('id')->save($i_array);
		json_exit("添加成功",true);
	}
	
	function check($id,$frame=false,$check_tpl=true)
	{
		if(!$id) return array("status"=>"error","info"=>'未指定ID信息');
		$pid = array_search($id,$this->cache_data['id_list']);
		if(!$pid) return array("status"=>"error","info"=>'项目信息不存在');
		$rs = $this->call->project_me(array("param"=>$pid));
		if(!$rs) return array("status"=>"error","info"=>'项目信息不存在或未启用');
		if(!$rs['module']) return array("status"=>"error","info"=>'此项目没有表单功能');
		return array("status"=>"ok","info"=>$rs);
	}
}
?>