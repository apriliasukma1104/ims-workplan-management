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
        $membersQuery = Members::select('id', 'image', 'name', 'position', 'role', 'email');

        $search = $request->input('search');
        if ($search) {
            $membersQuery->where('name', 'like', '%' . $search . '%');
        }

        $members = $membersQuery->paginate(5); 

        if ($request->ajax()) {
            return response()->json(['data' => $members]);
        }

        return Inertia::render('Members/ListMembers', [
            'title' => $title,
            'members' => $members,
        ]);
    }


    public function PageAddMember()
    {
        $title = 'Members';
        return Inertia::render('Members/AddMember', [
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

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $imagePath = $image->store('public/images'); 
            $validatedData['image'] = str_replace('public/images/', '', $imagePath);
        }
        $validatedData['password'] = Hash::make($request->password); // Simpan data ke database
        Members::create($validatedData);
        return redirect()->route('members.list_members')->with('message', 'Data Created successfully!');
    }

    public function EditMember(Request $request)
    {
        $member = Members::findOrFail($request->id);
        return Inertia::render('Members/EditMember', [
            'formData' => $member
        ]);
    }

    public function UpdateMember(Request $request)
    {
        $member = Members::find($request->input('id'));
        $member->update($request->all());
        return redirect()->route('members.list_members')->with('message', 'Data Successfully Updated!');
    }

    public function DeleteMember(Request $request)
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