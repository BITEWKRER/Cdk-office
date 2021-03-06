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
            'name' => '??????',
            'bold' => true,
            'alignment' => Jc::CENTER,
        );

        $cellColSpan7 = array('gridSpan' => 7, 'valign' => 'center');

        $classifyName = array(
            'size' => 12,
            'name' => '??????',
            'color' => 'black',
            'bold' => true,
            'alignment' => Jc::CENTER,
        );

        $section->addHeader()->addText($addressName . '????????????', filesController::style("header"), array('alignment' => Jc::START));

        $table = $section->addTable('Colspan Rowspan');
        $table->addRow(500);
        $table->addCell(2000, $cellColSpan7)->addText($addressName . '???????????????',
            array('size' => 22, 'bold' => true, 'name' => '??????', 'space' => array('lineHeight' => 2.0)), array('alignment' => Jc::CENTER));
        $table->addRow(500);

        $table->addCell(2800)->addText('????????????', $tabletitle, $tabletitle);
        $table->addCell(2000)->addText('??????', $tabletitle, $tabletitle);
        $table->addCell(2000)->addText('?????????', $tabletitle, $tabletitle);
        $table->addCell(2000)->addText('??????', $tabletitle, $tabletitle);
        $table->addCell(2000)->addText('??????', $tabletitle, $tabletitle);
        $table->addCell(2000, filesController::style("cellColSpan2"))->addText('??????', $tabletitle, $tabletitle);

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
        $table->addCell(null, filesController::style("cellColSpan7"))->addText("??????:" . $sum . '???', null, filesController::style("center"));
        $section->addText('');
        $section->addText('');
        $section->addText('??????????????????????????????70%?????????????????????25%?????????????????????5%??? ');
        $section->addText('??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????');
        $section->addText('?????????????????????');
        $section->addText('');
        $section->addText('');
        $section->addText('');
        $section->addText('');
        $section->addText('');
        $section->addText('?????????(??????)????????????????????????                                               ?????????');
        $section->addText('');
        $section->addText('');
        $section->addText('');
        $section->addText('?????????(??????)????????????????????????                                               ?????????');

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
                'classifyName' => '??????',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm(???)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm(???)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '???????????????',
                        'unit' => 'm(???)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '???????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm(???)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ),
                ],
            ), array(
                'id' => 'restaurant',
                'classifyName' => '??????',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ),
                ],
            ), array(
                'id' => 'living_room',
                'classifyName' => '??????',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '?????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '???????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm(???)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ),
                ],
            ), array(
                'id' => 'Ma_bedroom',
                'classifyName' => '??????',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    )
                ],
            ), array(
                'id' => 'Mi_bedroom',
                'classifyName' => '??????',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    )
                ],
            ), array(
                'id' => 'Sa_bedroom',
                'classifyName' => '??????',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => '???',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    )
                ],
            ), array(
                'id' => 'bathroom',
                'classifyName' => '?????????',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '???????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '???????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '???????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '???????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '???????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '???????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ),
                ],
            ), array(
                'id' => 'Toilet',
                'classifyName' => '?????????',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '?????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '???????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '???????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    )]
            ), array(
                'id' => 'Miscellaneous',
                'classifyName' => '??????',
                'isShow' => false,
                'goods' => [
                    array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '?????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '????????????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ), array(
                        'name' => '??????',
                        'unit' => 'm??(?????????)',
                        'num' => '',
                        'price' => '',
                        'count' => '0.00',
                        'remark' => '',
                        'checked' => false
                    ),
                ]
            ), array(
                'id' => 'Custom_item',
                'classifyName' => '???????????????',
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
