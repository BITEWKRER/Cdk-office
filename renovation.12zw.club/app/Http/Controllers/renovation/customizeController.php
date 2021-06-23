<?php

namespace App\Http\Controllers\renovation;

use App\content;
use App\Http\Controllers\utils\filesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class customizeController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        $openid = $request->get('openid');
        $content = $request->get('content');
        $phpword = new PhpWord();


        $isExists = DB::table('contents')->where('name',$name)->where('openid', $openid)->where('stencils',2)->get();
        $isExists = json_decode($isExists,true);


        if ($isExists != []) {
            $result = DB::table("contents")->where('openid', $openid)->where('name',$name)->where('stencils',2)->update(['content' => json_encode([
                'content' => $content,
                'name' => $name,
            ], true)]);

            $section = $phpword->addSection();
            Html::addHtml($section, $content, false, false);
            filesController::saveWord($phpword, $openid, $name);

            return response($result.'',200 );
        } else {
            $bill = new content();
            $bill->openid = $openid;
            $bill->stencils = 2;
            $bill->name = $name;
            $bill->content = json_encode([
                'content' => $content,
                'name' => $name,
            ], true);
            $bill->save();

            $section = $phpword->addSection();
            Html::addHtml($section, $content, false, false);
            filesController::saveWord($phpword, $openid, $name);
        }


    }
}
