<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
class BranchController extends Controller
{
    
    public function index()
    {
        $Branches = Branch::all();
        return response()->json($Branches);
    }



    
    public function store(Request $request)
    {
         
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                // 'store_id' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'service_type' => 'required',
                'address' => 'required|string',
                // 'status' => 'required',
                // 'featured' => 'required|boolean',
                'coverage' => 'required',
                'image' => 'nullable|image',
            ], [
                'image.image' => 'The image must be an image file.',
                'image.max' => 'The image size must not exceed 4 MB.'
            ]);
    
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
            } else {
                $imagePath = null; 
            }
    
            $branch = Branch::create([
                'name' => $request->name,
                // 'store_id' => $request->store_id,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'service_type' => $request->service_type,
                'address' => $request->address,
                // 'status' => $request->status,
                // 'featured' => $request->featured,
                'coverage' => $request->coverage,
                'image' => $imagePath
            ]);
    
            return response()->json(['message' => 'Branch created successfully'], 201);
        } 
        
        catch (ValidationException $e) 
        {
            return response()->json(['error' => $e->validator->errors()], 422);
        } 
        catch (\Exception $e) 
        {
            Log::error('Error storing a new branch: ' . $e->getMessage()); 
            return response()->json(['error' => $e->getMessage()], 500); 
        }
}





   
   
    public function update(Request $request, $id)
{
    try{$productBranch = Branch::findOrFail($id);

        
        $request->validate([
            'name' => 'required|string|max:255',
            // 'store_id' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'service_type' => 'required',
            'address' => 'required|string',
            // 'status' => 'required',
            // 'featured' => 'required|boolean',
            'coverage' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif',
        ], [
            'image.image' => 'The image must be an image file.',
            'image.max' => 'The image size must not exceed 4 MB.'
        ]);

        
        $imagePath = $productBranch->image;

        
        if ($request->hasFile('image')) 
        {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        
        $productBranch->update([
            'name' => $request->name,
            // 'store_id' => $request->store_id,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'service_type' => $request->service_type,
            'address' => $request->address,
            // 'status' => $request->status,
            // 'featured' => $request->featured,
            'coverage' => $request->coverage,
            'image' => $imagePath 
        ]);

        
        Log::info('Branch updated successfully');
        return response()->json(['message' => 'Branch updated successfully'], 201);

    }
    catch (ValidationException $e) 
    {
        return response()->json(['error' => $e->validator->errors()], 422);
    } 
    catch (\Exception $e) 
    {
        Log::error('Error updating branch: ' . $e->getMessage());
        return response()->json(['error' => 'An error occurred while updating branch'], 500);
    }
}





    public function destroy($id)
    {
        
        try {
            $productBranch = Branch::findOrFail($id);
    
            $productBranch->delete();
    
            return response()->json(['message' => 'Product branch deleted successfully']);
        } 

        catch (ValidationException $e) 
        {
            return response()->json(['error' => $e->validator->errors()], 422);
        } 
        catch (\Exception $e) 
        {
            Log::error('Error in deleting a  branch: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while deleting a branch'], 500);
        }

    }
}