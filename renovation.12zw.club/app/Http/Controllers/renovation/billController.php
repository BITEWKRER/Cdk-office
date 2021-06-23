<?php

namespace App\Http\Controllers\renovation;

use App\content;
use App\Http\Controllers\utils\filesController;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

class billController extends Controller
{
    public function index(Request $request)
    {
        $flag = $request->get('billFlag');
        $openid = $request->get('openid');
        $content = $request->get('content');
        switch ($flag) {
            case 0:
                $this->selfBill($openid, $content);
                break;
            case 1:
                $this->accounting($openid, $content, $request);
                break;
            default:
                break;
        }
    }

    public function selfBill($openid, $content)
    {
        $month = date("Y-m");

        $isExists = DB::table('contents')->where('name',$month.'月收入支出账单')->where('openid',$openid)->where('stencils',1)->get();
        $isExists = json_decode($isExists,true);

        if ($isExists != []) {
            $result=DB::table("contents")->where('openid',$openid)->where('name',$month.'月收入支出账单')->where('stencils',1)->update(['content' => json_encode([
               'content' => $content,
               'name' => $month
          	])]);          	
            $this->bill($openid,$content,$month);
            return response($result.'',200 );
        }else{
            //插入数据库
            $bill = new content();
            $bill->openid = $openid;
            $bill->stencils = 1;
            $bill->name = $month.'月收入支出账单';
            $bill->content = json_encode([
                'content' => $content,
                'name' => $month
            ],true);
            $bill->save();

            $this->bill($openid,$content,$month);
        }

    }

    private function bill($openid,$content,$month){

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $phpWord->addTableStyle('Colspan Rowspan', array('borderSize' => 10, 'borderColor' => '999999'));
        $table = $section->addTable('Colspan Rowspan');

        $table->addRow();
        $table->addCell(null, filesController::style("cellColSpan6"))->addText($month . '月收入支出账单',
            array('size' => 22, 'bold' => true, 'name' => '宋体', 'space' => array('lineHeight' => 2.0)), array('alignment' => Jc::CENTER));

        $table->addRow();
        $table->addCell()->addText("序号", filesController::style("table"), filesController::style("center"));
        $table->addCell(2000)->addText("名称", filesController::style("table"), filesController::style("center"));
        $table->addCell()->addText("类型", filesController::style("table"), filesController::style("center"));
        $table->addCell(1000)->addText("金额", filesController::style("table"), filesController::style("center"));
        $table->addCell(2000)->addText("时间", filesController::style("table"), filesController::style("center"));
        $table->addCell(2000)->addText("备注", filesController::style("table"), filesController::style("center"));
        $tmp = 0;

        foreach (json_decode($content, true) as $i => $item) {
            $table->addRow();
            $table->addCell()->addText($i + 1, filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["itemName"], filesController::style("tableCont"), filesController::style("center"));
            if ($item["income_or_expenditure"] == 0) {
                $table->addCell()->addText('支出', filesController::style("tableCont"), filesController::style("center"));
                $tmp -= $item["money"];
            } else {
                $table->addCell()->addText('收入', filesController::style("tableCont"), filesController::style("center"));
                $tmp += $item["money"];
            }
            $table->addCell()->addText($item["money"], filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["ymd"] . PHP_EOL . $item["time"], filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["remark"], filesController::style("tableCont"), filesController::style("center"));
        }

        $table->addRow();
        if ($tmp > 0) {
            $table->addCell(null, filesController::style("cellColSpan6"))->addText('存钱了哟！共收入' . $tmp . '元', filesController::style("tableCont"), filesController::style("center"));
        } else {
            $table->addCell(null, filesController::style("cellColSpan6"))->addText('我太难了 /(ㄒoㄒ)/~~共支出' . $tmp . '元', filesController::style("tableCont"), filesController::style("center"));
        }
        filesController::saveWord($phpWord, $openid, $month . '月收入支出账单');
    }

    private function accounting($openid, $content, $request)
    {
        $name = $request->get('name');
        $department = $request->get('department');
        $who = $request->get('who');


        $isExists = DB::table('contents')->where('name',$name)->where('openid',$openid)->where('stencils',0)->get();
        $isExists = json_decode($isExists,true);

        if ($isExists != []) {
            $result=DB::table("contents")->where('openid',$openid)->where('name',$name)->where('stencils',0)->update(['content' => json_encode([
                'name' => $name,
                'department' => $department,
                'content' => $content,
                'who' => $who
            ],true)]);
            $this->updatefile($content,$name,$openid,$department,$who);
            return response($result.'',200 );
        }else{
            $bill = new content();
            $bill->openid = $openid;
            $bill->stencils = 0;
            $bill->name = $name;
            $bill->content = json_encode([
                'name' => $name,
                'department' => $department,
                'content' => $content,
                'who' => $who
            ],true);
            $bill->save();
            $this->updatefile($content,$name,$openid,$department,$who);
        }
    }

    private function updatefile($content,$name,$openid,$department,$who)
    {
        $PhpWord = new PhpWord();
        $section = $PhpWord->addSection();
        $PhpWord->addTableStyle('Colspan Rowspan', array('borderSize' => 10, 'borderColor' => '999999'));
        $table = $section->addTable('Colspan Rowspan');

        $table->addRow();
        $table->addCell(null, filesController::style("cellColSpan9"))->addText('财务会计 销售对账单',
            array('size' => 22, 'bold' => true, 'name' => '宋体', 'space' => array('lineHeight' => 2.0)), array('alignment' => Jc::CENTER));
        $table->addRow();
        $table->addCell(null, filesController::style("cellColSpan9"))->addText('单位：' . $department . '    对账人：' . $who . '   日期：' . date("Y-m-d"),
            filesController::style("table"), array('alignment' => Jc::CENTER));

        $table->addRow();
//        $table->addCell()->addText("序号", filesController::style("table"), filesController::style("center"));
        $table->addCell()->addText("发货日期", filesController::style("table"), filesController::style("center"));
        $table->addCell()->addText("到货日期", filesController::style("table"), filesController::style("center"));
        $table->addCell()->addText("商品名称", filesController::style("table"), filesController::style("center"));
        $table->addCell()->addText("应收金额", filesController::style("table"), filesController::style("center"));
        $table->addCell()->addText("已付金额", filesController::style("table"), filesController::style("center"));
        $table->addCell()->addText("欠款余额", filesController::style("table"), filesController::style("center"));
        $table->addCell()->addText("负责人", filesController::style("table"), filesController::style("center"));
        $table->addCell()->addText("摘要", filesController::style("table"), filesController::style("center"));

        foreach (json_decode($content, true) as $i => $item) {
            $table->addRow();
//            $table->addCell()->addText($i + 1, filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["delivery_date"], filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["arrival_date"], filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["merchandise_name"], filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["should_get"], filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["already_get"], filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["left_money"], filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["responsible_for"], filesController::style("tableCont"), filesController::style("center"));
            $table->addCell()->addText($item["remark"], filesController::style("tableCont"), filesController::style("center"));
        }

        filesController::saveWord($PhpWord, $openid, $name);
    }
}
