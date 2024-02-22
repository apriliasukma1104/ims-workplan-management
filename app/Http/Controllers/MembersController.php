<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Members;

class MembersController extends Controller
{
    public function PageListMembers(Request $request)
    {
        $user = Auth::user();
        $membersQuery = Members::select('id', 'name', 'nip', 'position', 'sub_department','role', 'email', 'members_status');
        
        $search = $request->input('search');
        if ($search) {
            $membersQuery->where(function ($query) use ($search) {
                $columns = ['name', 'nip', 'position', 'sub_department', 'role', 'email'];
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        // Setting ascending pada members
        $members = $membersQuery->orderBy('role', 'asc')->paginate(10);

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

    public function UpdateStatusMember(Request $request)
    {
        $member = Members::find($request->input('id'));
        $member->members_status=$request->input('members_status');
        $member->save();
    }
}