<?php

namespace App\Http\Controllers\SysAdmin;

use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Http\Controllers\Controller;

//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\Datatables\Datatables;

//Enables us to output flash messaging
use Session;
use Validator;

class UserController extends Controller {

    public function __construct() {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Validation rule
     * @return array
     */
    public function rules()
    {
        return [
            'email'=>'required|email|unique:users|max:100',
            'password'=>'required|confirmed' //'password'=>'required|min:6|confirmed'
        ];
    }

    /**
     * Data base label
     * @return array
     */
    public function table()
    {
        return [
            'tableVar' => 'users',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if(Auth::user()->hasRole('admin')) {
            return view('sysadmin.users.index', $this->table());
        }else{
            $user = Auth::user();
            return view('sysadmin.profile.update', ['user' => $user]);
        }
    }

    /**
     * Load post data for view table
     * @return mixed
     */
    public function getdata()
    {
        //$data = User::all('id','name','email','created_at','avatar');
        $data = User::all();
        return Datatables::of($data)
            ->editColumn('id', function($user){
                return '<input type="checkbox" name="checkbox[]" data-id="checkbox" id="box-'. $user->id .'" class="check-style filled-in blue" onclick="toggleBtn()" value="'.$user->id.'"> 
                        <label for="box-'. $user->id .'" class="checkinner"></label>';
            })
            ->editColumn('show_avatar', function($data){
                 if (file_exists(public_path('uploads/avatars/'.$data->avatar)) and $data->avatar!=''){
                     return '<img class="rounded-circle" style="width: 40px; height: 40px;" src="' . asset('uploads/avatars/' . $data->avatar) . '" alt="">';
                 }
                return '<img class="rounded-circle" style="width: 40px; height: 40px;" src="'. asset('templates/admin/images/user.jpg') .'" alt=""';
             })
            ->editColumn('role_assigned', function($data){
                return $data->roles()->pluck('name')->implode(' ');
            })
            ->editColumn('created_at', function($data){
                if($data->created_at)
                return $data->created_at->format('F d, Y h:ia');
            })

            ->addColumn('action', function($data){
                return '
           <div class="btn-group btn-group-xs" role="group" aria-label="actions"> 
           <a href="#" data-id="row-'. $data->id .'" onclick="editForm(\''.url('admin/users/editpassword').'\','.$data->id.')" class="btn btn-info btn-xs"><i class="fa fa-key"></i></a> 
           <a href="#" data-id="row-'. $data->id .'" onclick="editForm(\''.url('admin/users/edit').'\','.$data->id.')" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i></a> 
           <a href="#" data-id="row-'. $data->id .'" onclick="deleteData(\''.url('admin/users/delete').'\','.$data->id.')" class="btn btn-danger delete-link btn-xs" ><i class="fa fa-times"></i></a> 
           </div>';
            })
            ->rawColumns(['id','show_avatar', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //Get all roles and pass it to the view
        $roles = Role::get();
        return view('sysadmin.users.create', ['roles'=>$roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //Validate name, email and password fields
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            $user = User::create($request->only('email', 'name','username', 'password'));

            $roles = $request['roles']; //Retrieving the roles field
            //Checking if a role was selected
            if (isset($roles)) {
                foreach ($roles as $role) {
                    $role_r = Role::where('id', '=', $role)->firstOrFail();
                    $user->assignRole($role_r); //Assigning role to user
                }
            }
            //Redirect to the users.index view and display message
            return response()->json([
                'success' => true,
                'message' => 'Record Added Successfully'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $post = User::findOrFail($id);
        return view('sysadmin.users.details', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $user = User::findOrFail($id);
        $roles = Role::get();

        return view('sysadmin.users.edit', compact('user', 'roles')); //pass user and roles data to view
    }


    /**
     * Update the specified resource in storage
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request){
        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(),
            [
                'username'=>'required|max:120',
                'email'=>'required|email|unique:users,email,'.$id,
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->all()]);
        }
        else {
            $input = $request->only(['username', 'email','name']);
            $roles = $request['roles']; //Retreive all roles
            $user->fill($input)->save();

            if (isset($roles)) {
                $user->roles()->sync($roles);  //If one or more role is selected associate user to roles
            } else {
                $user->roles()->detach(); //If no role is selected remove exisiting role associated to a user
            }
            return response()->json(['success' => true, 'message' => 'Record Updated Successfully']);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPassword($id) {
        $user = User::findOrFail($id);
        return view('sysadmin.users.editpassword', compact('user'));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function UpdatePassword($id, Request $request){
        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(), ['password'=>'required|confirmed']
        );
        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->all()]);
        }
        else {
            $user->password = $request->input('password');
            $user->save();

            return response()->json(['success' => true, 'message' => 'Password Updated Successfully']);
        }
    }
    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        User::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Record Deleted Successfully']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletemulti(Request $request){
        $requestData = $request->except('_token');
        if(count($requestData)>1) {
            foreach ($requestData as $id) {
                User::where('id', $id)->delete();
            }
            return response()->json(['success' => 'delete', 'message' => 'Record Deleted Successfully']);
        }
        return response()->json(['error' => true, 'message' => 'Failed deleting. Make sure you check 1 or more boxes.']);
    }
}