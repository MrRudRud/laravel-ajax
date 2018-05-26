<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Contact;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        // set Null if the photo didn't exists
        $input['photo'] = NULL;
        // if it has photo
        if($request->hasFile('photo')){
            $input['photo'] = '/upload/photo/'.str_slug($input['name'], '-').'.'.$request->photo->getClientOriginalExtension();
            // dd($input['photo']);
            $request->photo->move(public_path('/upload/photo/'), $input['photo']);
        }

        Contact::create($input);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // (1) edit
        $contact = Contact::find($id);
        return $contact;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $contact = Contact::findOrFail($id);

        if($request->hasFile('photo')){
            // check photo if exists, just replaced
            if($contact->photo != NULL ) {
                unlink(public_path($contact->photo));
            }
            $input['photo'] = '/upload/photo/'.str_slug($input['name'], '-').'.'.$request->photo->getClientOriginalExtension();
            $request->photo->move(public_path('/upload/photo/'), $input['photo']);
        }

        $contact->update($input);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        if($contact->photo != NULL) {
            unlink(public_path($contact->photo));
        }

        Contact::destroy($id);
        return response()->json([
            'success' => true
        ]);
    }

    public function massremove(Request $request)
    {
        $contact_id = $request->input('id');
        $contact = Contact::whereIn('id',$contact_id)->delete();
        
        return response()->json([
            'success' => true
        ]);
    }

    public function apiContact(){
        $contact = Contact::orderBy('id','desc');
        return Datatables::of($contact)
            // Add New Column with closure function Contact
            
            ->addColumn('checkbox', '<input type="checkbox" name="contact[]" class="contact" value="{{$id}}">')
            ->addColumn('show_photo', function($contact){
                if($contact->photo == NULL){
                    return 'No Image';
                }
                return '<img class="rounded-square" width="50" height="50" src="'.url($contact->photo).'" alt="">';
             })
            ->addColumn('action', function($contact) {
                return 
                '<a href="#" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-open"></i> Show</a>'.
                ' <a onclick="editForm('.$contact->id.')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a>'.
                ' <a onclick="deleteData('.$contact->id.')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
            })
            ->rawColumns(['show_photo','action','checkbox', 'action'])
            ->make(true);
    }
}
