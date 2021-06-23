<?php

namespace App\Http\Controllers\renovation;

use App\content;
use App\Http\Controllers\utils\filesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

class renovationController extends Controller
{

    public function index(Request $request)
    {
        $modelFlag = $request->get("flag");
        $content = $request->get("content");

        switch ($modelFlag) {
            case 1:
                $this->model1($content, $request);
                break;
            case 2:
                $this->model2($content);
                break;
            default:
                break;
        }
    }


    public function model1($content, $request)
    {

        $addressName = $request->get('addressName');
        $sum = $request->get('sum');
        $openid = $request->get('openid');


        $isExists = DB::table('contents')->where('name', $addressName)->where('openid', $openid)->where('stencils', 4)->get();
        $isExists = json_decode($isExists, true);

        if ($isExists != []) {
            $result = DB::table("contents")->where('openid', $openid)->where('name', $addressName)->where('stencils', 4)->update(['content' => json_encode([
                'content' => $content,
                'name' => $addressName
            ], true)]);

            $this->updatefile($content, $sum, $openid, $addressName);
            return response($result . '', 200);
        } else {
            $bill = new content();
            $bill->openid = $openid;
            $bill->stencils = 4;
            $bill->name = $addressName;
            $bill->content = json_encode([
                'content' => $content,
                'name' => $addressName,
                'sum' => $sum
            ], true);
            $bill->save();
            $this->updatefile($content, $sum, $openid, $addressName);
        }
    }

    private function updatefile($content, $sum, $openid, $addressName)
    {

        $PhpWord = new PhpWord();
        $section = $PhpWord->addSection();
        $PhpWord->addTableStyle('Colspan Rowspan', array('borderSize' => 10, 'borderColor' => '999999'));

        $tableTextStyle = array(
            'alignment' => Jc::CENTER
        );
        $tabletitle = array(
            'size' => 14,
            'name' => '宋体',
            'bold' => true,
            'alignment' => Jc::CENTER,
        );

        $cellColSpan7 = array('gridSpan' => 7, 'valign' => 'center');

        $classifyName = array(
            'size' => 12,
            'name' => '宋体',
            'color' => 'black',
            'bold' => true,
            'alignment' => Jc::CENTER,
        );

        $section->addHeader()->addText($addressName . '工程预算', filesController::style("header"), array('alignment' => Jc::START));

        $table = $section->addTable('Colspan Rowspan');
        $table->addRow(500);
        $table->addCell(2000, $cellColSpan7)->addText($addressName . '装修预算表',
            array('size' => 22, 'bold' => true, 'name' => '宋体', 'space' => array('lineHeight' => 2.0)), array('alignment' => Jc::CENTER));
        $table->addRow(500);

        $table->addCell(2800)->addText('项目名称', $tabletitle, $tabletitle);
        $table->addCell(2000)->addText('单位', $tabletitle, $tabletitle);
        $table->addCell(2000)->addText('工程量', $tabletitle, $tabletitle);
        $table->addCell(2000)->addText('单价', $tabletitle, $tabletitle);
        $table->addCell(2000)->addText('小计', $tabletitle, $tabletitle);
        $table->addCell(2000, filesController::style("cellColSpan2"))->addText('备注', $tabletitle, $tabletitle);

        $tmp = null;
        $item = null;
        foreach (json_decode($content, true) as $key => $classify) {
            if ($classify['isShow'] == true) {
                $table->addRow();
                $table->addCell(20000, $cellColSpan7)->addText($classify['classifyName'], $classifyName, $classifyName);
            }
            if (isset($classify['goods'])) {
                foreach ($classify['goods'] as $index => $item) {
                    if ($item != '') {
                        $table->addRow();
                        $table->addCell(3000)->addText($this->trim($item['name']), null, $tableTextStyle);
                        $table->addCell(1200)->addText($this->trim($item["unit"]), null, $tableTextStyle);
                        $table->addCell(1500)->addText($item["num"], null, $tableTextStyle);
                        $table->addCell(1500)->addText($item["price"], null, $tableTextStyle);
                        $table->addCell(2500)->addText($item["count"], null, $tableTextStyle);
                        $table->addCell(3000, filesController::style("cellColSpan2"))->addText($this->trim($item["remark"]), null, null);
                    }
                }
            }
        }
        $table->addRow();
        $table->addCell(null, filesController::style("cellColSpan7"))->addText("总计:" . $sum . '元', null, filesController::style("center"));
        $section->addText('');
        $section->addText('');
        $section->addText('付款：首付款为总价的70%，木工完工付款25%，完工付款剩余5%。 ');
        $section->addText('备注：此报价仅包含列出项，未列出项不在包含范围之内。如需增加项目，需增加相应的资费。');
        $section->addText('其他注意事项：');
        $section->addText('');
        $section->addText('');
        $section->addText('');
        $section->addText('');
        $section->addText('');
        $section->addText('建设方(甲方)（签字或盖章）：                                               电话：');
        $section->addText('');
        $section->addText('');
        $section->addText('');
        $section->addText('承建方(乙方)（签字或盖章）：                                               电话：');

        filesController::saveWord($PhpWord, $openid, $addressName);
    }

    private function model2($content)
    {

    }


    private function trim($data)
    {
        return trim(json_encode($data, JSON_UNESCAPED_UNICODE), '"');
    }

    public function modelJson()
    {
        $data = json_encode([
            array(
                'id' => 'kitchen',
                'classifyName' => '厨房',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '厨房吊顶',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '吊柜',
                        'unit' => 'm(米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '包管子',
                        'unit' => '根',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '橱柜',
                        'unit' => 'm(米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '厨房墙砖',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '厨房抹灰',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '厨房防水',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '厨房大理石',
                        'unit' => 'm(米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '厨房推拉门',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '厨房门套',
                        'unit' => 'm(米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ),
                ],
            ), array(
                'id' => 'restaurant',
                'classifyName' => '餐厅',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '餐厅吊顶',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '餐厅酒柜',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '鞋柜',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '屏风',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '挂衣板',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ),
                ],
            ), array(
                'id' => 'living_room',
                'classifyName' => '客厅',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '电视墙',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '客厅吊顶',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '客厅窗套',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '窗台大理石',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '客厅矮柜',
                        'unit' => 'm(米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ),
                ],
            ), array(
                'id' => 'Ma_bedroom',
                'classifyName' => '主卧',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '衣柜',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卧室门',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卧室窗套',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卧室矮柜',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '床头背景',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '书柜',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '推拉门',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    )
                ],
            ), array(
                'id' => 'Mi_bedroom',
                'classifyName' => '中卧',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '衣柜',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卧室门',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卧室窗套',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卧室矮柜',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '床头背景',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '书柜',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '推拉门',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    )
                ],
            ), array(
                'id' => 'Sa_bedroom',
                'classifyName' => '小卧',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '衣柜',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卧室门',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卧室窗套',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卧室矮柜',
                        'unit' => '个',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '床头背景',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '书柜',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '榻榻米',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '推拉门',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    )
                ],
            ), array(
                'id' => 'bathroom',
                'classifyName' => '卫生间',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '卫生间门头',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卫生间推拉门',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卫生间吊顶',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卫生间包管子',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卫生间墙砖',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卫生间防水',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卫生间马桶',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '卫生间淋浴',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ),
                ],
            ), array(
                'id' => 'Toilet',
                'classifyName' => '洗手间',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '洗手柜',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '洗手间吊顶',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '洗手间防水',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    )]
            ), array(
                'id' => 'Miscellaneous',
                'classifyName' => '杂项',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '地砖',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '沙子',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '水泥',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '沙子水泥砖上楼',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '家政',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '墙面处理',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '顶部处理',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '壁纸',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '乳胶漆',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '垃圾清理',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '踢脚线',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '石膏线',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '改水',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '改电',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '开关插座',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '合页',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '滑道',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '拉手',
                        'unit' => 'm²(平方米)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ),
                ]
            ), array(
                'id' => 'Custom_item',
                'classifyName' => '自定义项目',
                'isShow' => false,
                'Custom_item' => true,
                'itemNum' => 0,
                'goods' => []
            )
        ]);

        $data = json_decode($data);
        echo json_encode($data);
    }

    public function getunit()
    {
        $file = file(storage_path() . '/reFiles/Runit');
        $file = preg_replace("/\r\n/", '', $file);
        return Response()->json($file);
    }

    public function getCustomID()
    {
        $file = file(storage_path() . '/reFiles/RcustomId');
        $file = preg_replace("/\r\n/", '', $file);
        return Response()->json((int)$file[0]);
    }

    public function getClassify()
    {
        $file = file(storage_path() . '/reFiles/Rclassify');
        $file = preg_replace("/\r\n/", '', $file);
        return Response()->json($file);
    }


}
