<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $member = Members::find($request->input('id'));

        $member->fill($request->except('password'));
        $newPassword = $request->input('password');
            if (!empty($newPassword)) {
                $hashedPassword = bcrypt($newPassword);
                $member->password = $hashedPassword;
            }
        $member->save();

        return redirect()->route('members.list_members');
    }

    public function PageListMembers(Request $request)
    {
        $membersQuery = Members::select('id', 'name', 'position', 'role', 'email');

        $search = $request->input('search');
        if ($search) {
            $membersQuery->where(function ($query) use ($search) {
                $columns = ['name', 'position', 'role', 'email'];
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        $members = $membersQuery->paginate(5); 
        if ($request->ajax()) {
            return response()->json(['data' => $members]);
        }

        return Inertia::render('Members/ListMembers', [
            'members' => $members,
        ]);
    }

    public function PageAddMember()
    {
        return Inertia::render('Members/AddMember');
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
        $newPassword = $validatedData['password'];
            if (!empty($newPassword)) {
            $hashedPassword = bcrypt($newPassword);
            $validatedData['password'] = $hashedPassword;
        }
        Members::create($validatedData);
        return redirect()->route('members.list_members');
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

        $member->fill($request->except('password'));
        $newPassword = $request->input('password');
            if (!empty($newPassword)) {
                $hashedPassword = bcrypt($newPassword);
                $member->password = $hashedPassword;
            }
        $member->save();

        return redirect()->route('members.list_members');
    }

    public function DeleteMember(Request $request)
    {
        $member = Members::findOrFail($request->id);
        $member->delete();
    }
}