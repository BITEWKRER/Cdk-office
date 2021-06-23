<?php

namespace App\Http\Controllers\renovation;

use App\content;
use App\Http\Controllers\utils\filesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\TemplateProcessor;

class profileController extends Controller
{
    public function index(Request $request)
    {
        $flag = $request->get('flag');
        $openid = $request->get('openid');
        $content = $request->get('content');

        $content = json_decode($content, true);
        if ($content == '') {
            return;
        }
        switch ($flag) {
            case 0:
                try {
                    $template = new TemplateProcessor(storage_path('model/简历模板.docx'));
                    $this->model($openid, $content, $template);
                } catch (CopyFileException $e) {
                } catch (CreateTemporaryFileException $e) {
                }
                break;
            case 1:
                try {
                    $template = new TemplateProcessor(storage_path('model/简历模板2.docx'));
                    $this->model($openid, $content, $template);
                } catch (CopyFileException $e) {
                } catch (CreateTemporaryFileException $e) {
                }
                break;
        }
    }

    public function ezProfile($openid, $content)
    {

        $cellRowContinue = array('vMerge' => 'continue', 'gridSpan' => 2,); //使行连接，且无边框线

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $phpWord->addTableStyle('Colspan Rowspan', array('borderSize' => 10, 'borderColor' => '999999'));
        $section->addHeader()->addText('个人简历  RESUME', filesController::style("header"), array('alignment' => Jc::CENTER));
        $table = $section->addTable('Colspan Rowspan');
        $table->addRow();
        $table->addCell(1200)->addText("姓名", filesController::style("table"), filesController::style("center"));
        $table->addCell(1500)->addText($content["name"], filesController::style("tableCont"), filesController::style("center"));
        $table->addCell(1200)->addText("性别", filesController::style("table"), filesController::style("center"));
        $table->addCell(1000)->addText($content["sex"], filesController::style("tableCont"), filesController::style("center"));
        $table->addCell(1200)->addText("名族", filesController::style("table"), filesController::style("center"));
        $table->addCell(1500)->addText($content["national"], filesController::style("tableCont"), filesController::style("center"));
        $style = ['vMerge' => 'restart', 'gridSpan' => 2, 'valign' => 'center'];
        $table->addCell(2500, $style)->addText("");

        $table->addRow();
        $table->addCell(1200)->addText("政治面貌", filesController::style("table"), filesController::style("center"));
        $table->addCell(1500)->addText($content["political_status"], filesController::style("tableCont"), filesController::style("center"));
        $table->addCell(1200)->addText("出生年月", filesController::style("table"), filesController::style("center"));
        $table->addCell(1000)->addText($content["bron"], filesController::style("tableCont"), filesController::style("center"));
        $table->addCell(1200)->addText("婚姻状况", filesController::style("table"), filesController::style("center"));
        $table->addCell(1500)->addText($content["marital_status"], filesController::style("tableCont"), filesController::style("center"));
        $table->addCell(2500, $cellRowContinue);

        $table->addRow();
        $table->addCell(1200)->addText("籍贯", filesController::style("table"), filesController::style("center"));
        $table->addCell(1000, filesController::style("cellColSpan5"))->addText($content["birthplace"], filesController::style("tableCont"), filesController::style("center"));
        $table->addCell(2500, $cellRowContinue);
        $table->addRow();
        $table->addCell(1200)->addText("现所在地", filesController::style("table"), filesController::style("center"));
        $table->addCell(1000, filesController::style("cellColSpan3"))->addText($content["now_living"], filesController::style("tableCont"), filesController::style("center"));
        $table->addCell(1200)->addText("学历", filesController::style("table"), filesController::style("center"));
        $table->addCell(1500)->addText($content["education"], filesController::style("tableCont"), filesController::style("center"));
        $table->addCell(2500, $cellRowContinue);

        $table->addRow();
        $table->addCell(1200)->addText("毕业院校", filesController::style("table"), filesController::style("center"));
        $table->addCell(1000, filesController::style("cellColSpan3"))->addText($content["university"], filesController::style("tableCont"), filesController::style("center"));
        $table->addCell(1000)->addText("专业", filesController::style("table"), filesController::style("center"));
        $table->addCell(1000, filesController::style("cellColSpan3"))->addText($content["major"], filesController::style("tableCont"), filesController::style("center"));
        $table->addRow();
        $table->addCell(1000, filesController::style("cellColSpan8"))->addText('个人履历', filesController::style("table"), filesController::style("center"));
        $table->addRow();
        $style = ['vMerge' => 'restart', 'gridSpan' => 8, 'valign' => 'center'];
        $table->addCell(1000, $style)->addText($content['experiences'], filesController::style("tableCont"), ['alignment' => Jc::START]);
        $cellRowContinue = array('vMerge' => 'continue', 'gridSpan' => 8,); //使行连接，且无边框线
        for ($i = 0; $i < 4; $i++) {
            $table->addRow();
            $table->addCell(2500, $cellRowContinue)->addText('', filesController::style("tableCont"), ['alignment' => Jc::START]);
        }


        $table->addRow();
        $table->addCell(1000, filesController::style("cellColSpan8"))->addText('个人荣誉', filesController::style("table"), filesController::style("center"));
        foreach ($content['honers'] as $item) {
            $table->addRow();
            $table->addCell(1000, filesController::style("cellColSpan8"))->addText($item, filesController::style("tableCont"), ['alignment' => Jc::START]);
        }

        $table->addRow();
        $table->addCell(1000, filesController::style("cellColSpan8"))->addText('个人评价', filesController::style("table"), filesController::style("center"));
        $table->addRow();
        $table->addCell(1000, filesController::style("cellColSpan8"))->addText($content['self_evaluation'], filesController::style("tableCont"), ['alignment' => Jc::START]);
        $table->addRow();
        $table->addCell(1000, filesController::style("cellColSpan2"))->addText('E-mail', filesController::style("table"), filesController::style("center"));
        $table->addCell(1000, filesController::style("cellColSpan2"))->addText($content['E-mail'], filesController::style("tableCont"), filesController::style("center"));
        $table->addCell(1000, filesController::style("cellColSpan2"))->addText('联系电话', filesController::style("table"), filesController::style("center"));
        $table->addCell(1000, filesController::style("cellColSpan2"))->addText($content['phone'], filesController::style("tableCont"), filesController::style("center"));
        $table->addRow();
        $table->addCell(1000, filesController::style("cellColSpan4"))->addText('求职意向', filesController::style("table"), filesController::style("center"));
        $table->addCell(1000, filesController::style("cellColSpan4"))->addText($content['purpose'], filesController::style("tableCont"), filesController::style("center"));

        filesController::saveWord($phpWord, $content["openid"], date('Y-m-d') . '日' . $content["name"] . '的个人简历');

    }

    public function model($openid, $content, $template)
    {
        $isExists = DB::table('contents')->where('name', $content["name"])->where('openid', $openid)->where('stencils', 3)->get();
        $isExists = json_decode($isExists, true);

        if ($isExists != []) {
            $result = DB::table("contents")->where('openid', $openid)->where('stencils', 3)->where('name', $content["name"])->update(['content' => json_encode([
                'name' => $content["name"],
                'content' => $content
            ], true)]);

            $this->save($template, $content, $openid);
            return response($result . '', 200);
        } else {
            $resume = new content();
            $resume->openid = $openid;
            $resume->stencils = 3;
            $resume->name = $content["name"];
            $resume->content = json_encode([
                'name' => $content["name"],
                'content' => $content
            ], true);
            $resume->save();
            $this->save($template, $content, $openid);
        }

    }

    private function save($template, $content, $openid)
    {
        try {
            $template->setValue('name', $content["name"]);
            $template->setValue('sex', $content["sex"]);
            $template->setValue('money', $content["money"]);
            $template->setValue('phone', $content["phone"]);
            $template->setValue('email', $content["E_mail"]);
            $template->setValue('purpose', $content["purpose"]);
            $template->setValue('university', $content["university"]);
            $template->setValue('education', $content["education"]);
            $template->setValue('major', $content["major"]);
            $template->setValue('honers', $content["honers"]);
            $template->setValue('experience', $content["experiences"]);
            $template->setValue('communicate', $content["communicate"]);
            $template->setValue('self_evaluation', $content["self_evaluation"]);
            $template->saveAs(storage_path('userFiles/' . $openid . '/' . $content["name"] . '.docx'));
        } catch (CopyFileException $e) {
        } catch (CreateTemporaryFileException $e) {
        }
    }

}
