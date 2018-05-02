<?php
namespace Home\Controller;

use Think\Controller;

class MsgController extends Controller
{
    public function index()
    {
        // 1.创建对象
        $msgTable = D('project');
        // 2.获取url参数
        $msg = $msgTable->searchProject_3();
        // 4.为视图赋值
        $this->assign('msglist', $msg);
        $this->display();
    }

    public function join()
    {
        $msgTable = D('project');
        $msgid = I('get.pid');
        $msg = $msgTable->getMsgById($msgid);
        $msgTable->updateRead($msgid);//add once read
        $this->assign('msg', $msg);
        $this->assign('pid',$msgid);
        $this->display();
    }

    public function vote()
    {
        if (IS_POST) {
            $post = $_POST;
            $member = D('member');
            $r = $member->insertM($post['pid'],$post['name'],$post['gender'],$post['age'],$post['province'],$post['city'],$post['number'],$post['detail'],$post['tel'],$post['email']);
            if($r) $this->success('报名成功','/Home/Msg/msglist');
            else $this->error($member->getError(),"javascript:window.location.href=document.referrer;",1);
        } else {
            $msgTable = D('project');
            $msgid = I('get.pid');
            $msg = $msgTable->getMsgById($msgid);
            $colloge = D('colloge')->where(array(
                'school_id' => 1
            ))->select();
            $msgTable->updateRead($msgid);//add once read
            $this->assign('province', $colloge);
            $this->assign('msgid',$msgid);
            $this->assign('msg', $msg);
            $this->display();
        }
    }
    public function msglist()
    {
        $msgTable = D('project');
        $member = D('member');
        // 2.获取url参数
        $msg = $msgTable->searchAllProject();
        $nowdate = date('Y-m-d H:i:s');
        //$c_registered = $member->where('mprojectid='.$msg['pid'])->count();
        // 4.为视图赋值
        $this->assign('nowdate', $nowdate);
        $this->assign('msglist', $msg);
        $this->display();
    }
    public function about() {
        $this->display();
    }
}