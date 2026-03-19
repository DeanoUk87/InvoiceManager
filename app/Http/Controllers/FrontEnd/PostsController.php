<?php

namespace App\Http\Controllers\FrontEnd;
use App\Http\Controllers\Traits\Uploader;
use App\Models\System\Post;
use App\Models\System\Upload;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;
use Validator;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use PDF;
use DB;
use Excel;
use Image;

class PostsController extends Controller
{
    use Uploader;
    /**
     * Display post view.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $posts = Post::orderBy('id','DESC')->paginate(6);
        //send to view
        return view('frontend.posts.index',['posts' => $posts])->with('i', ($request->input('page', 1) - 1) * 6);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function details($id){
        $post = Post::findOrFail($id);
        return view('frontend.posts.details', compact('post'));
    }


}