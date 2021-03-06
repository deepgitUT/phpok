<?php
/**
 * 演示插件<插件安装>
 * @package phpok\plugins
 * @作者 phpok.com
 * @版本 4.8.000
 * @授权 http://www.phpok.com/lgpl.html PHPOK开源授权协议：GNU Lesser General Public License
 * @时间 2017年11月18日 10时44分
**/
class install_demo extends phpok_plugin
{
	public $me;
	public function __construct()
	{
		parent::plugin();
		$this->me = $this->_info();
	}
	
	/**
	 * 插件安装时，增加的扩展表单输出项，如果不使用，请删除这个方法
	**/
	public function index()
	{
		$condition = "module>0";
		$project_list = $this->model('project')->project_all($this->session->val('admin_site_id'),'id',$condition);
		$this->assign('project_list',$project_list);
		return $this->_tpl('setting.html');
	}
	
	/**
	 * 插件安装时，保存扩展参数，如果不使用，请删除这个方法
	**/
	public function save()
	{
		$id = $this->_id();
		$ext = array();
		$ext['demo'] = $this->get('demo1');
		$ext['mypid'] = $this->get('mypid');
		//$ext['扩展参数字段名'] = $this->get('表单字段名');
		$this->_save($ext,$id);
	}
	
	
}