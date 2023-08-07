<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Members;

class MembersController extends Controller
{

    public function PageManageMember()
    {
        $user = Auth::user();
        return Inertia::render('Members/ManageMember', [
            'formData' => $user
        ]);
    }

    public function UpdateManageMember(Request $request)
    {
        $members = Members::find($request->input('id'));
        $members->update($request->all());
        return redirect()->route('members.list_members')->with('message', 'Data Successfully Updated!');
    }
    
    public function PageListMembers(Request $request)
    {
        $title = 'Members';
        $members = Members::select('id', 'image', 'name', 'position', 'role', 'email')->get();
        return Inertia::render('Members/ListMembers', [
            'title' => $title,
            'members' => $members, // Mengirim data anggota ke halaman Vue.js
        ]);
    }

    public function PageAddMembers()
    {
        $title = 'Members';
        return Inertia::render('Members/AddMembers', [
            'title' =>  $title
        ]);
    }

    public function StoreMembers(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255|in:Super Admin,Admin 1,Admin 2,User',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'position' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:members,email',
            'password' => 'required|string|min:8',  
        ])->validate();
        
        // if ($validator->fails()) {
        //     return Inertia::render('Members/AddMembers', [
        //         'errors' => $validator->errors()->toArray(), 
        //         'formData' => $request->all(), 
        //     ])->withViewData(['message' => 'Failed to save data']); 
        // }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
                        
            $imagePath = $image->store('public/uploads'); // Simpan gambar di folder storage/app/public/uploads
            $validatedData['image'] = asset(str_replace('public', 'storage', $imagePath));
        }
        $validatedData['password'] = Hash::make($request->password); // Simpan data ke database
        Members::create($validatedData);
        return redirect()->route('members.list_members')->with('message', 'Data Created successfully!');
    }

    public function EditMembers(Request $request)
    {
        $members = Members::findOrFail($request->id);
        return Inertia::render('Members/EditMembers', [
            'formData' => $members
        ]);
    }

    public function UpdateMembers(Request $request)
    {
        $members = Members::find($request->input('id'));
        $members->update($request->all());
        return redirect()->route('members.list_members')->with('message', 'Data Successfully Updated!');
    }

    public function DeleteMembers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:members,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $member = Members::findOrFail($request->id);
            // Hapus data anggota dari database
            $member->delete();
            return response()->json(['message' => 'Data Deleted Successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data Failed to Delete!'], 500);
        }
    }
}