<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Support\Facades\Validator;
use Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['employee'] = Employee::all();
        return response()->json($data,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'photo_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'number' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails())
        {
            $data['message'] = 'Oops there are some errors with input values!!';
            $data['status'] = 'failed';
            $data['error'] = $validator->errors();
            $data['values'] = $request->all();
            
            return response()->json($data,400);
        }
        else{
            //Storing img file
            $file = $request->file('photo_url');
            $imageName = time().$file->getClientOriginalName();
            $path = $request->file('photo_url')->storeAs('avatars',$imageName);
            $path = 'avatars/'.$imageName;

            $insert_data['name'] = $request->name;
            $insert_data['photo_url'] = $path;
            $insert_data['number'] = $request->number;
            $insert_data['email'] = $request->email;
            // $content = Storage::get($path);
            $data['message'] = 'Employee has been added successfully!!';
            $data['status'] = 'success';
            $data['data'] = Employee::create($insert_data);
            
            return response()->json($data,201);
        }
    }
    
    public function show($id)
    {
        $data = Employee::where('id',$id)->get()->toArray();
        $path = $data[0]['photo_url'];
        
        $content = Storage::get($path);
        $data['photo'] = base64_encode($content);

        return response()->json($data,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeeRequest  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'photo_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'number' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails())
        {
            $data['message'] = 'Oops there are some errors with input values!!';
            $data['status'] = 'failed';
            $data['error'] = $validator->errors();
            $data['values'] = $request->all();
            
            return response()->json($data,400);
        }
        else{
            $Employee = Employee::findOrFail($id);
            // update image file in storagedisk and url in db table
            $file = $request->file('photo_url');
            $imageName = time().$file->getClientOriginalName();
            $path = $request->file('photo_url')->storeAs('avatars',$imageName);
            $path = 'avatars/'.$imageName;

            $update_data['name'] = $request->name;
            $update_data['photo_url'] = $path;
            $update_data['number'] = $request->number;
            $update_data['email'] = $request->email;
            $data['message'] = 'Employee has been updated successfully!!';
            $data['status'] = 'success';
            $data['data'] = Employee::create($update_data);
            
            return response()->json($data,201);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Employee = Employee::findOrFail($id);
        $Employee->delete();
        $data['message'] = 'Employee has been delete successfully!!';
        $data['status'] = 'success';
        return response()->json($data,200);
    }
}
