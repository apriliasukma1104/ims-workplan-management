<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $user = Auth::user();
        $membersQuery = Members::select('id', 'name', 'position', 'role', 'email');

        if ($user->role === 'Kadiv' || $user->role === 'Kadep') {
            $membersQuery->where(function ($query) use ($user) {
                $query->where('role', 'Kadiv')
                      ->orWhere('role', 'Kadep')
                      ->orWhere('role', 'User');
            });
        }
        
        $search = $request->input('search');
        if ($search) {
            $membersQuery->where(function ($query) use ($search) {
                $columns = ['name', 'position', 'role', 'email'];
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        // Setting ascending pada members
        $members = $membersQuery->orderBy('name', 'asc')->paginate(10);

        if ($request->ajax()) {
            return response()->json(['data' => $members]);
        }

        return Inertia::render('Members/ListMembers', [
            'members' => $members,
            'auth' => $user 
        ]);
    }

    public function PageAddMember()
    {
        return Inertia::render('Members/AddMember');
    }
    
    public function StoreMembers(Request $request)
    {
        $member = $request->all();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('public/images'); 
            $member['image'] = str_replace('public/', '', $imagePath);
        }

        $newPassword = $member['password'];
        if (!empty($newPassword)) {
            $hashedPassword = bcrypt($newPassword);
            $member['password'] = $hashedPassword;
        }

        Members::create($member);
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