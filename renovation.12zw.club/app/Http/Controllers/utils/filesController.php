<?php

namespace App\Http\Controllers\utils;


use App\User;
use App\users;
use App\wxUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

class filesController extends Controller
{
    public static function style($style)
    {
        $styleRe = null;
        switch ($style) {
            case "fTitle":
                $styleRe = array(
                    'name' => '宋体',
                    'size' => 22,
                    'color' => 'black',
                    'bold' => true,
                    'lineHeight' => 1.6
                );
                break;
            case "sTitle":
                $styleRe = array(
                    'size' => 12,
                    'name' => '宋体',
                    'color' => 'black',
                    'bold' => true,
                    'alignment' => Jc::CENTER,
                );
                break;
            case "img":
                $styleRe = array(
                    'width' => 120,
                    'height' => 160,
                    'wrappingStyle' => 'tight',
                );
                break;
            case "header":
                $styleRe = array(
                    'name' => '宋体',
                    'size' => 10,
                );
                break;
            case "table":
                $styleRe = array(
                    'size' => 14,
                    'name' => '宋体',
                    'bold' => true,
                    'alignment' => Jc::CENTER,
                    "lineHeight" => 1.5
                );
                break;
            case "tableCont":
                $styleRe = array(
                    'name' => '宋体',
                    'size' => 12,
                    'bold' => false,
                    "lineHeight" => 1.5
                );
                break;
            case "normal":
                $styleRe = array(
                    'name' => '宋体',
                    'size' => 12,
                    'lineHeight' => 1.2
                );
                break;
            case "tTitle":
                $styleRe = array(
                    'name' => '宋体',
                    'size' => 12,
                    'bold' => true,
                    'lineHeight' => 1.4
                );
                break;
            case "center":
                $styleRe = array('alignment' => Jc::CENTER);
                break;
            case "signature":
                $styleRe = array(
                    'name' => '宋体',
                    'size' => 12,
                    'lineHeight' => 2.5
                );
                break;
            case "cellColSpan7":
                $styleRe = array('gridSpan' => 7, 'valign' => 'center');
                break;
            case 'cellColSpan9':
                $styleRe = array('gridSpan' => 9, 'valign' => 'center');
                break;
            case 'cellColSpan8':
                $styleRe = array('gridSpan' => 8, 'valign' => 'center');
                break;
            case "cellColSpan2":
                $styleRe = array('gridSpan' => 2, 'valign' => 'center');
                break;
            case "cellColSpan6":
                $styleRe = ['gridSpan' => 6, 'valign' => 'center'];
                break;
            case 'cellRowSpan':
                $styleRe = array('vMerge' => 'restart', 'valign' => 'center'); // 设置可跨行，且文字在居中
                break;
            case 'cellColSpan':
                $styleRe = array('gridSpan' => 2, 'valign' => 'center'); //设置跨列
                break;
            case 'cellColSpan5':
                $styleRe = ['gridSpan' => 5, 'valign' => 'center'];
                break;
            case 'cellColSpan4':
                $styleRe = ['gridSpan' => 4, 'valign' => 'center'];
                break;
            case 'cellColSpan3':
                $styleRe = ['gridSpan' => 3, 'valign' => 'center'];
            default:
                break;
        }
        return $styleRe;
    }

    public static function saveWord($phpWord, $openid, $name)
    {

        $path = storage_path() . '/userFiles/' . $openid;
        if (!file_exists($path)) {
            \Illuminate\Support\Facades\File::makeDirectory($path, $mode = 0777, true, true);
        }

        // todo 保存数据
        try {
            $OBJETWriter = IOFactory::createWriter($phpWord, 'Word2007');
        } catch (Exception $e) {
            $e->getMessage();
        }
        if (!empty($OBJETWriter)) {
            $OBJETWriter->save($path . '/' . $name . '.docx');
            return response('success', 200);
        } else {
            return response('fail', 404);
        }

    }

    public function downloadWord(Request $request)
    {
        $name = $request->get('name');
        $openid = $request->get('openid');

        //获取当前文件所在的绝对目录


        $file = scandir(storage_path('/userFiles/' . $openid . '/'));

        for ($i = 0; $i < count($file); $i++) {
            if ($file[$i] === $name . '.docx') {
                return response()->download(storage_path('userFiles/' . $openid . '/' . $file[$i]), $file[$i],
                    $header = [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'Content-Description' => 'File Transfer',
                        'Content-Disposition' => ' attachment; filename="' . $name . '.docx',
                        'Content-Transfer-Encoding' => 'binary',
                        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                        'Expires' => 0
                    ]);
            }
        }

        return response('fail', 404);
    }

    public function getOpenID(Request $request)
    {
        $appid = 'wx7634a0ab3056d9a2';
        $secret = '2142d219d21cf51cf64a6481c5e9ed86';
        $grant_type = 'authorization_code';
        $js_code = $request->get('js_code');


        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $secret . '&js_code=' . $js_code . '&grant_type=' . $grant_type);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); //不验证证书

        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        $data = json_decode($data);
		
        try {
            $openid = $data->openid;
          	$wx = new wxUser();
            $wx->openid = $openid;
            $wx->save();
        } catch (\Exception $e) {

        }

        return $openid;
    }

    public function isAdd(Request $request)
    {
        $openid = $request->get('openid');
        $add = DB::table('wx_users')->where('openid', $openid)->get();
        $result = json_decode($add, true);

        if ($result == []) {
            return DB::insert('insert into wx_users(openid,cloud)values (?,?)', [$openid, false]).'';
        } else {
            return true.'';
        }
    }

    public function uploadImg(Request $request)
    {
        $api = $request->get('api');

        if ($request->hasFile('img')) {

            // 使用request 创建文件上传对象
            $profile = $request->file('img');
            // 获取文件后缀名
            $ext = $profile->getClientOriginalExtension();
            // 处理文件名称
            $temp_name = $this->quickRandom();
            $filename = $temp_name . '.' . $ext;
            $dirname = date('Ymd', time());
            // 保存文件
            $profile->move(storage_path() . '/images/' . $dirname, $filename);

        } else {
            return 404;
        }
    }

    private static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public function getDocx(Request $request)
    {
        $openid = $request->get('openid');

        //获取当前文件所在的绝对目录
        if ($openid != null) {
            $dir = storage_path();
            $file = scandir($dir . '/userFiles/' . $openid . '/');
            $arr = array();
            for ($i = 2; $i < count($file); $i++) {
                array_push($arr, $file[$i]);
            }
            return response()->json($arr);
        }

    }

    public function getFileType(Request $request)
    {
        $name = $request->get('name');
        $openid = $request->get('openid');
//        dd($name);
        $type = DB::table('contents')->where('openid', $openid)->where('name', $name)->value('stencils');
        $content = DB::table('contents')->where('openid', $openid)->where('name', $name)->value('content');
        return response([
            'type' => $type,
            'content' => $content
        ], 200);
    }

    public function isVip(Request $request)
    {
        $openid = $request->get('openid');
        $vip = DB::table('wx_users')->where('openid', $openid)->value('cloud');
        return response([
            'vip' => $vip
        ], 200);
    }

    public function getCloudSize(Request $request)
    {
        $openid = $request->get('openid');
        $dirname = storage_path('/userFiles/' . $openid . '/');
        $size = 0;

        if (is_dir($dirname)) {
            $files = scandir($dirname);

            for ($i = 0; $i < count($files); $i++) {
                if ($files[$i] == '.' || $files == '..') {
                    continue;
                } else {
                    $size += filesize($dirname.$files[$i]);
                }
            }
            $size /= 1024;
            return response(['size' => ceil($size)]);
        } else {
            return response(['size' => 0]);
        }
    }

    public function deleteDocx(Request $request)
    {
        $openid = $request->get('openid');
        $name = $request->get('name');

        $dir = storage_path();
        $file = scandir($dir . '/userFiles/' . $openid . '/');

        for ($i = 2; $i < count($file); $i++) {
            if ($name == $file[$i]) {
                if (unlink($dir . '/userFiles/' . $openid . '/' . $name)) {
                    return response('success', 200);
                } else {
                    return response('fail', 500);
                }
            }
        }
    }
}
