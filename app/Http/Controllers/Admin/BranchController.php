<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Branch;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BranchController extends Controller
{
    public function list()
    {
        $branches = Branch::paginate(10);
        return view('admin-views.branch.list', compact('branches'));
    }

    public function add_new()//returns blade file AKA:html
    {
        return view('admin-views.branch.add-new');
    }


    public function store(Request $request)//Handling create action
    {
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'service_type' => 'required',
            'address' => 'required|string',
            'coverage' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif',
        ]);


        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('branch')) {
                Storage::disk('public')->makeDirectory('branch');
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('branch/' . $image_name, $note_img);

            $validatedData['image'] = $image_name;

        } else {
            $image_name = 'def.png';
        }
        $validatedData['password'] = bcrypt($request->password);

        Branch::create($validatedData);
        Toastr::success('Branched added successfully!');
        return redirect()->route('admin.branch.list');    
    }



    public function edit($id)//returns blade file AKA:html
    {
        $branch = Branch::findOrFail($id);
        return view('admin-views.branch.edit', compact('branch'));
        
    }



    public function update(Request $request,$id)//Handling update action
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'service_type' => 'required',
            'address' => 'required|string',
            'coverage' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif',
        ]);

        $branch = Branch::find($id);


        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('branch')) {
                Storage::disk('public')->makeDirectory('branch');
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('branch/' . $image_name, $note_img);
        } else {
            $image_name = 'def.png';
        }

        
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->password = bcrypt($request->password); 
        $branch->service_type = $request->service_type;
        $branch->address = $request->address;
        $branch->coverage = $request->coverage;
        $branch->image = $image_name;
        $branch->save();
       
        return redirect()->route('admin.branch.list'); 

        
    }



    public function status($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->status = $branch->status == 1 ? 0 : 1;
        $branch->save();
        Toastr::success('Status updated successfully');
        return back();
    }




    
    public function destroy($id)
    {
        $branch = Branch::find($id);
        if (!$branch) 
        {
            return back()->with('error', 'Branch not found');
        }

        if (Storage::disk('public')->exists('branch/' . $branch->image)) //using [] raised offset array null as we're trying to access object not array
        {
            Storage::disk('public')->delete('branch/' . $branch->image);
        }

        $branch->delete();
        Toastr::success('Branch deleted successfully');
        return back();
    }

    
}
