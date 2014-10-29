<?php
/***********************************************************
	Filename: {phpok}/admin/ueditor_control.php
	Note	: Ueditor 编辑器中涉及到上传的操作
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2014年7月7日
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class ueditor_control extends phpok_control
{
	function __construct()
	{
		parent::control();
	}

	function load_config()
	{
		$config = $this->lib('file')->cat($this->dir_root.'data/config.json');
		$config = preg_replace("/\/\*[\s\S]+?\*\//","",$config);
		$config = $this->lib('json')->decode($config);
		$config['imageCompressEnable'] = false;
		//获取默认存储路径
		$cate_rs = $this->model('res')->cate_one();
		if(!$cate_rs || $cate_rs['root'] == '/' || !$cate_rs['root'])
		{
			$cate_rs["id"] = 0;
			$cate_rs["root"] = "res/";
			$cate_rs["folder"] = "/";
		}
		$folder = $cate_rs["root"];
		if($cate_rs["folder"] && $cate_rs["folder"] != "/")
		{
			$folder .= date($cate_rs["folder"],$this->time);
		}
		if(!file_exists($folder))
		{
			$this->lib('file')->make($folder);
		}
		//如果还是没有检测到文件夹，则回到默认目录
		if(!file_exists($folder))
		{
			$folder = $cate_rs['root'];
		}
		if(substr($folder,-1) != "/") $folder .= "/";
		if(substr($folder,0,1) == "/") $folder = substr($folder,1);
		if($folder)
		{
			$folder = str_replace("//","/",$folder);
		}
		$rooturl = $this->root_url();
		$config['imagePathFormat'] = $folder;
		$config['imageUrlPrefix'] = $rooturl;
		$config['scrawlPathFormat'] = $folder;
		$config['scrawUrlPrefix'] = $rooturl;
		$config['snapscreenPathFormat'] = $folder;
		$config['snapscreenUrlPrefix'] = $rooturl;
		$tmp = array('localhost','127.0.0.1','img.baidu.com',$_SERVER['SERVER_NAME']);
		$config['catcherLocalDomain'] = array_unique($tmp);
		$config['catcherPathFormat'] = $folder;
		$config['catcherUrlPrefix'] = $rooturl;
		$config['videoPathFormat'] = $folder;
		$config['videoUrlPrefix'] = $rooturl;
		$config['filePathFormat'] = $folder;
		$config['fileUrlPrefix'] = $rooturl;
		$config['imageManagerUrlPrefix'] = $rooturl;
		$config['videoManagerUrlPrefix'] = $rooturl;
		$config['fileManagerUrlPrefix'] = $rooturl;
		$config['cateid'] = $cate_rs['id'];
		foreach($config as $key=>$value)
		{
			if(substr($key,0,5) == 'scraw')
			{
				unset($config[$key]);
			}
		}
		return $config;
	}

	//停止运行
	private function _stop($info,$data='')
	{
		if(!$data)
		{
			$data = array();
		}
		$data['state'] = ($info && !is_bool($info)) ? $info : 'SUCCESS';
		exit($this->lib('json')->encode($data));
	}

	public function index_f()
	{
		$action = $this->get('action');
		if(!$action)
		{
			$this->_stop('未指定请求方式');
		}
		$action_array = array('config','uploadimage','uploadvideo','uploadfile','listimage','listfile','listvideo','catchimage');
		if(!in_array($action,$action_array))
		{
			$this->_stop('请求参数不正确');
		}
		$action_name = 'u_'.$action;
		$this->$action_name();
	}

	//图片本地化
	private function u_catchimage()
	{
		$config = $this->load_config();
		$folder = $config['catcherPathFormat'];
		$imgUrls = $this->get($config['catcherFieldName']);
		if(!$imgUrls)
		{
			$this->_stop('没有图片信息');
		}
		set_time_limit(0);
		$tmpNames = array();
		$arraylist = array("jpg","gif","png","jpeg");
		$rslist = array();
		$oldlist = array();
		foreach($imgUrls AS $key=>$imgUrl)
		{
			$imgUrl = str_replace( "&amp;" , "&" , $imgUrl);
			//如果获取的图片信息是data类型，则自动存储为png格式
			if(strtolower(substr($imgUrl,0,10)) == 'data:image')
			{
				$tmp = explode(",",$imgUrl);
				$content = base64_decode(substr($imgUrl,strlen($tmp[0])));
				$tmp_title = $this->time."_".$key;
				$new_filename = $tmp_title;
				$ext = 'png';
			}
			else
			{
				if(strpos($imgUrl,"http")!==0)
				{
					array_push($rslist,array('state'=>'附件获取失败'));
					continue;
				}
				$content = $this->lib('html')->get_content($imgUrl);
				$tmp_title = basename($imgUrl);
				$new_filename = substr(md5($imgUrl),9,16)."_".rand(0,99)."_".$key;
				$fileType = strtolower( strrchr( $imgUrl , '.' ));
				$ext = substr($fileType,1);
				if(!$ext) $ext = "png";
			}
            if(!$content)
            {
	            array_push($rslist,array('state'=>'附件获取失败'));
                continue;
            }
            $save_folder = $this->dir_root.$folder;
			$newfile = $save_folder.$new_filename.".".$ext;
			$this->lib('file')->save_pic($content,$newfile);
			if(!is_file($newfile))
			{
				array_push($rslist,array('state'=>'附件写入失败'));
				continue;
			}
			//迁移附件到数据库中
			$array = array();
			$array["cate_id"] = $config['cateid'];
			$array["folder"] = $folder;
			$array["name"] = $new_filename.'.'.$ext;
			$array["ext"] = $ext;
			$array["filename"] = $folder.$new_filename.".".$ext;
			$array["addtime"] = $this->time;
			if($tmp_title && !$this->is_utf8($tmp_title))
			{
				$tmp_title = $this->charset($tmp_title,"GBK","UTF-8");
			}
			$array["title"] = $tmp_title ? str_replace(".".$ext,"",$tmp_title) : str_replace(".".$ext,"",$new_filename);
			if(in_array($ext,$arraylist))
			{
				$img_ext = getimagesize($newfile);
				$my_ext = array("width"=>$img_ext[0],"height"=>$img_ext[1]);
				$array["attr"] = serialize($my_ext);
			}
			$array["session_id"] = $this->session->sessid();
			//存储图片信息
			$id = $this->model('res')->save($array);
			if(!$id)
			{
				$this->lib('file')->rm($this->dir_root.$array['filename']);
				array_push($rslist,array('state'=>'附件存储失败'));
                continue;
			}
			$this->model('res')->gd_update($id);
			$oldlist[$id] = $imgUrl;
			array_push( $rslist , array('id'=>$id) );
		}
		$idlist = array();
		foreach($rslist as $key=>$value)
		{
			if($value['id'])
			{
				$idlist[] = $value['id'];
			}
		}
		if(!$idlist || count($idlist)<1)
		{
			$this->_stop('没有可用的附件');
		}
		$condition = "res.id IN(".implode(",",$idlist).")";
		$is_gd = false;
		$gd_rs = $this->model('gd')->get_editor_default();
		if($gd_rs)
		{
			$condition .= " AND e.gd_id='".$gd_rs["id"]."' ";
			$is_gd = true;
		}
		$piclist = $this->model('res')->edit_pic_list($condition,0,999,$is_gd);
		if(!$piclist)
		{
			$this->_stop('没有可用的附件');
		}
		$plist = array();
		foreach($piclist as $key=>$value)
		{
			$plist[$value['id']] = $value;
		}
		foreach($rslist as $key=>$value)
		{
			if($value['id'] && $plist[$value['id']])
			{
				$tmp = array();
				$tmp['title'] = $plist[$value['id']]['title'];
				$tmp['original'] = $plist[$value['id']]['title'];
				$tmp['state'] = 'SUCCESS';
				$tmp['source'] = $oldlist[$value['id']];
				$tmp['url'] = $plist[$value['id']]['filename'];
				$rslist[$key] = $tmp;
			}
		}
		$this->_stop(true,array('list'=>$rslist));
	}

	//读取视频列表
	private function u_listvideo()
	{
		$config = $this->load_config();
		$offset = $this->get('start','int');
		$psize = $this->get('size','int');
		$type = $config['videoManagerAllowFiles'];
		$type = implode("|",$type);
		$type = str_replace(".","",$type);
		$condition = "res.ext IN('".str_replace('|',"','",$type)."')";
		$rslist = $this->model('res')->edit_pic_list($condition,$offset,$psize,false);
		if(!$rslist)
		{
			$this->_stop('视频内容为空');
		}
		$piclist = array();
		foreach($rslist as $key=>$value)
		{
			$tmp = array('url'=>$value['filename'],'ico'=>$value['ico'],'mtime'=>$value['addtime'],'title'=>$value['title']);
			$piclist[] = $tmp;
		}
		$data = array('list'=>$piclist,'start'=>$offset,'size'=>$psize);
		$this->_stop(true,$data);
	}

	//文件管理工具
	private function u_listfile()
	{
		$offset = $this->get('start','int');
		$psize = $this->get('size','int');
		$rslist = $this->model('res')->edit_pic_list('',$offset,$psize,false);
		if(!$rslist)
		{
			$this->_stop('附件内容为空');
		}
		$piclist = array();
		foreach($rslist as $key=>$value)
		{
			$tmp = array('url'=>$value['filename'],'ico'=>$value['ico'],'mtime'=>$value['addtime'],'title'=>$value['title']);
			$piclist[] = $tmp;
		}
		$data = array('list'=>$piclist,'start'=>$offset,'size'=>$psize);
		$this->_stop(true,$data);
	}
	//图片管理工具
	private function u_listimage()
	{
		$offset = $this->get('start','int');
		$psize = $this->get('size','int');
		$condition = "res.ext IN ('gif','jpg','png','jpeg') ";
		$is_gd = false;
		$gd_rs = $this->model('gd')->get_editor_default();
		if($gd_rs)
		{
			$condition .= " AND e.gd_id='".$gd_rs["id"]."' ";
			$is_gd = true;
		}
		$rslist = $this->model('res')->edit_pic_list($condition,$offset,$psize,$is_gd);
		if(!$rslist)
		{
			$this->_stop('图片数据内容为空');
		}
		$piclist = array();
		foreach($rslist as $key=>$value)
		{
			$tmp = array('url'=>$value['filename'],'ico'=>$value['ico'],'mtime'=>$value['addtime']);
			$piclist[] = $tmp;
		}
		$data = array('list'=>$piclist,'start'=>$offset,'size'=>$psize);
		$this->_stop(true,$data);
	}

	//附件上传
	private function u_uploadfile()
	{
		$config = $this->load_config();
		$folder = $config['filePathFormat'];
		$input_name = $config['fileFieldName'];
		$rs = $this->upload_base($input_name,$folder,$config['cateid']);
		if(!$rs || $rs['status'] != 'ok')
		{
			$this->_stop('文件上传失败');
		}
		$data = array('title'=>$rs['title'],'url'=>$rs['filename'],'original'=>$rs['title']);
		$this->_stop(true,$data);
	}
	//视频上传
	private function u_uploadvideo()
	{
		$config = $this->load_config();
		$folder = $config['videoPathFormat'];
		$input_name = $config['videoFieldName'];
		$rs = $this->upload_base($input_name,$folder,$config['cateid']);
		if(!$rs || $rs['status'] != 'ok')
		{
			$this->_stop('视频上传失败');
		}
		$data = array('title'=>$rs['title'],'url'=>$rs['filename'],'original'=>$rs['title']);
		$this->_stop(true,$data);
	}

	//图片上传
	private function u_uploadimage()
	{
		$config = $this->load_config();
		$rs = $this->upload_base($config['imageFieldName'],$config['imagePathFormat'],$config['cateid']);
		if(!$rs || $rs['status'] != 'ok')
		{
			$this->_stop('上传失败，'.$rs['content']);
		}
		$gd_rs = $this->model('gd')->get_editor_default();
		if($gd_rs)
		{
			$ext_rs = $this->model('res')->get_gd_pic($rs['id']);
			$filename = ($ext_rs && $ext_rs[$gd_rs['identifier']]) ? $ext_rs[$gd_rs['identifier']]['filename'] : $rs['filename'];
		}
		else
		{
			$filename = $rs['filename'];
		}
		$data = array('title'=>$rs['title'],'url'=>$filename,'original'=>$rs['title']);
		$this->_stop(true,$data);
	}

	//读取配置信息
	private function u_config()
	{
		$config = $this->load_config();
		$this->_stop(true,$config);
	}

	//写入主题列表
	public function info_f()
	{
		$pageurl = $this->url("uedit","info");
		$pageid = $this->get($this->config["pageid"],"int");
		if(!$pageid) $pageid = 1;
		$psize = 28;
		$offset = ($pageid - 1) * $psize;
		//读取所有项目
		$projectlist = $this->model('project')->get_all_project($_SESSION['admin_site_id']);
		$this->assign("projectlist",$projectlist);
		//读取全部列表
		$condition = "l.site_id=".$_SESSION['admin_site_id'];
		$project_id = $this->get('project_id','int');
		if($project_id)
		{
			$p_rs = $this->model('project')->get_one($project_id);
			if($p_rs)
			{
				$condition .= " AND l.project_id=".$project_id;
				$pageurl .= "&project_id=".$project_id;
				$cate_id = $this->get('cate_id','int');
				if($cate_id && $p_rs['cate'])
				{
					$cate_rs = $this->model('cate')->get_one($cate_id);
					$catelist = array($cate_rs);
					$this->model('cate')->get_sublist($catelist,$cate_id);
					$cate_id_list = array();
					foreach($catelist AS $key=>$value)
					{
						$cate_id_list[] = $value["id"];
					}
					$cate_idstring = implode(",",$cate_id_list);
					$condition .= " AND l.cate_id IN(".$cate_idstring.")";
					$pageurl .= "&cate_id=".$cate_id;
					$this->assign("cate_id",$cate_id);
				}
				$this->assign("project_id",$project_id);
			}
		}
		$keywords = $this->get("keywords");
		if($keywords)
		{
			$condition .= " AND (l.title LIKE '%".$keywords."%' OR l.tag LIKE '%".$keywords."%' OR l.seo_keywords LIKE '%".$keywords."%' OR l.seo_desc LIKE '%".$keywords."%' OR l.seo_title LIKE '%".$keywords."%') ";
			$pageurl .= "&keywords=".rawurlencode($keywords);
			$this->assign("keywords",$keywords);
		}
		$total = $this->model('list')->get_all_total($condition);
		if($total>0)
		{
			$rslist = $this->model('list')->get_all($condition,$offset,$psize);
			$this->assign("rslist",$rslist);
			$this->assign("total",$total);
			if($total>$psize)
			{
				$pagelist = phpok_page($pageurl,$total,$pageid,$psize,"home=首页&prev=上一页&next=下一页&last=尾页&half=4&add=(total)/(psize)&always=1");
				$this->assign("pagelist",$pagelist);
			}
		}
		$this->view("edit_title");
	}
	
	//基础上传
	function upload_base($input_name='upfile',$folder='res/',$cateid=0)
	{
		//上传类型
		$typelist = $this->model('res')->type_list();
		if($typelist)
		{
			$ext = array();
			foreach($typelist as $key=>$value)
			{
				$ext[] = $value['ext'];
			}
			$ext = implode(",",$ext);
			$this->lib('upload')->set_type($ext);
		}
		$rs = $this->lib('upload')->upload($input_name);
		if($rs["status"] != "ok")
		{
			return $rs;
		}
		//存储目录
		$basename = basename($rs["filename"]);
		$save_folder = $this->dir_root.$folder;
		if($save_folder.$basename != $rs["filename"])
		{
			$this->lib('file')->mv($rs["filename"],$save_folder.$basename);
		}
		if(!file_exists($save_folder.$basename))
		{
			$this->lib('file')->rm($rs["filename"]);
			$rs = array();
			$rs["status"] = "error";
			$rs["error"] = "图片迁移失败";
			return $rs;
		}
		$array = array();
		$array["cate_id"] = $cateid;
		$array["folder"] = $folder;
		$array["name"] = $basename;
		$array["ext"] = $rs["ext"];
		$array["filename"] = $folder.$basename;
		$array["addtime"] = $this->time;
		if(!$this->is_utf8($rs["title"]))
		{
			$rs["title"] = $this->charset($rs["title"],"GBK","UTF-8");
		}
		$array["title"] = str_replace(".".$rs["ext"],"",$rs["title"]);
		$arraylist = array("jpg","gif","png","jpeg");
		if(in_array($rs["ext"],$arraylist))
		{
			$img_ext = getimagesize($save_folder.$basename);
			$my_ext = array("width"=>$img_ext[0],"height"=>$img_ext[1]);
			$array["attr"] = serialize($my_ext);
		}
		$array["session_id"] = $this->session->sessid();
		//存储图片信息
		$id = $this->model('res')->save($array);
		if(!$id)
		{
			$this->lib('file')->rm($save_folder.$basename);
			$rs = array();
			$rs["status"] = "error";
			$rs["error"] = "图片存储失败";
			return $rs;
		}
		$this->model('res')->gd_update($id);
		$rs = $this->model('res')->get_one($id);
		$rs["status"] = "ok";
		return $rs;
	}
}
?>