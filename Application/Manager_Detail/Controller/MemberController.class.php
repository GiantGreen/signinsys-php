<?php
namespace Manager_Detail\Controller;
use Think\Controller;
class MemberController extends Controller {
	public function submember(){
        if (session('?logineduser')){
            if(!empty(I('get.sid')))
            {
                $sourceid = I('get.sid');
            }
            else{
                $sourceid = 1;
            }
            $msg = D('project')->where(array('pid' => $sourceid))->select();
            $datasource = D('project')->select();
            $memberlist = D('vmember')->where(array(
                'mprojectid' => $sourceid
            ))->select();
            $this->assign('msg',$msg);
            $this->assign('data',$datasource);
            $this->assign('sac','open active');
            $this->assign('sac1','active');
            $this->assign('memberlist',$memberlist);
            $this->display();
        }else{
            $this->error('只有管理员才可以进入,请先登录', '/Manager_Detail/User/login');
        }
    }
    public function csvdown($id){
        $memberlist = D('vmember')->where(array(
                'mprojectid' => $id
            ))->field('mname,mnumber')->select();
        $str = "";
        foreach ($memberlist as $key => $value) {
            $str .= $value['mname'].",".$value['mnumber']."\n";
        }
        $fileName = 'es-'.date('Ymd').'.csv';
        $this->export_csv($fileName,$str);
        exit;
    }
    public function smscsvdown($id){
        $memberlist = D('vmember')->where(array(
                'mprojectid' => $id
            ))->field('mname,mtel')->select();
        $str = "";
        foreach ($memberlist as $key => $value) {
            $str .= $value['mname'].",".$value['mtel']."\n";
        }
        $fileName = 'sms-'.date('Ymd').'.csv';
        $this->export_csv($fileName,$str);
        exit;
    }
    public function export_csv($filename, $data){
        header("Content-type:text/csv");   
        header("Content-Disposition:attachment;filename=".$filename);   
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
        header('Expires:0');   
        header('Pragma:public');
        echo $data;
        
    }
    public function editmember(){
        if (session('?logineduser')){
            $this->assign('sac','open active');
            $this->assign('sac2','active');
            $this->display();
        }else{
            $this->error('只有管理员才可以进入,请先登录', '/Manager_Detail/User/login');
        }

    }
    public function download(){
        $pid = I('get.pid');
        $type = I('get.type');
        //"Excel5";
        $msg = D('project')->where(array('pid' => $pid))->select();
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $objSheet = $objPHPExcel->getActiveSheet();
        $objSheet->setTitle($msg[0]['pname']);
        $data = D('vmember')->where(array(
                'mprojectid' => $pid
            ))->select();
        $objSheet->mergeCells('A1:J1');
        $objSheet->mergeCells('A2:J2');
        //设置表头行高
        $objSheet->getRowDimension(1)->setRowHeight(35);
        $objSheet->getRowDimension(2)->setRowHeight(22);
        //设置字体样式
        $objSheet->getStyle('A1')->getFont()->setName('黑体');
        $objSheet->getStyle('A1')->getFont()->setSize(20);
        $objSheet->getStyle('A1')->getFont()->setBold(true);
        
        $objSheet->getColumnDimension('E')->setWidth(13);
        $objSheet->getColumnDimension('F')->setWidth(24);
        $objSheet->getColumnDimension('G')->setWidth(41);
        $objSheet->getColumnDimension('H')->setWidth(18);
        $objSheet->getColumnDimension('I')->setWidth(19);
        $objSheet->setCellValue('A1', $msg[0]['pname']);
        $objSheet->setCellValue('A2', '(导出日期：'.date('Y-m-d',time()).')');
        $objSheet->setCellValue("A3","ID")->setCellValue("B3","姓名")->setCellValue("C3","性别")->setCellValue("D3","年龄")->setCellValue("E3","电话")->setCellValue("F3","学院")->setCellValue("G3","班级")->setCellValue("H3","学号")->setCellValue("I3","报名时间")->setCellValue("J3","自我评价");
        $j=4;
        foreach ($data as $key => $value) {
            $objSheet->setCellValue("A".$j,$value['mid'])->setCellValue("B".$j,$value['mname'])->setCellValue("C".$j,$value['msex'])->setCellValue("D".$j,$value['mage'])->setCellValue("E".$j,$value['mtel'])->setCellValue("F".$j,$value['colloge_name'])->setCellValue("G".$j,$value['class_name'])->setCellValue("H".$j," ".$value['mnumber'])->setCellValue("I".$j,$value['mcreate_date'])->setCellValue("J".$j,$value['mdetail']);
            $j++;
        }
        $objSheet->getStyle('A1:J'.($j))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('A1:J'.($j))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objSheet->setAutoFilter('A3:J'.($j));
        //$objWriter->save($dir."/test.xlsx");
        
        ob_end_clean();//重要！！！！！！！！！解决乱码
        Header("Content-type: application/octet-stream;charset=utf-8");
        if($type=="Excel5"){
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            //$objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$msg[0]['pname'].date('Ymd-His').".xls".'"');
            header('Cache-Control: max-age=0');
        }else{
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$msg[0]['pname'].date('Ymd-His').".xlsx".'"');
            header('Cache-Control: max-age=0');
        }
        
        $objWriter->save("php://output");
    }
    public function del(){
        $mid = I('get.mid');
        $User = D("member"); // 实例化User对象
        $User->delete($mid); // 删除主键为1,2和5的用户数据
        $this->success("删除了");
    }
}