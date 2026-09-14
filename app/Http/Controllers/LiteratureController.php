<?php
namespace App\Http\Controllers;
use App\Http\Requests\LiteratureSearchRequest;
use App\Services\Literature\LiteratureSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class LiteratureController extends Controller
{
    public function index(){ return view('literature.index'); }
    public function search(LiteratureSearchRequest $request, LiteratureSearchService $service)
    {
        $input=$request->validated(); $input['only_abstract']=$request->boolean('only_abstract'); $input['open_access']=$request->boolean('open_access'); $input['use_ai']=$request->boolean('use_ai');
        $result=$service->run($input); session(['literature_export'=>['query'=>$input['query'],'result'=>$result]]);
        return view('literature.results',compact('input','result'));
    }
    public function export(Request $request,string $format)
    {
        abort_unless(in_array($format,['txt','md']),404); $data=session('literature_export'); abort_unless($data,404);
        $r=$data['result']; $content="# Literature Review\n\nTopik: {$data['query']}\n\n## Synthesis\n\n".$r['syn']['text']."\n\n## Daftar Pustaka\n\n";
        foreach($r['refs'] as $ref)$content.="- {$ref}\n";
        if($format==='txt')$content=preg_replace('/^#+\s*/m','',$content);
        return response($content)->header('Content-Type',$format==='md'?'text/markdown; charset=UTF-8':'text/plain; charset=UTF-8')->header('Content-Disposition','attachment; filename="literature-review.'.($format==='md'?'md':'txt').'"');
    }
}
