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
        
        $membersQuery = Members::select('members.*');
        
        $search = $request->input('search');
        if ($search) {
            $membersQuery->where(function ($query) use ($search) {
                $columns = ['name', 'nip', 'position', 'sub_department', 'role', 'email'];
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        $members = $membersQuery->orderBy('sub_department','asc')
        ->orderBy('id','asc')
        ->paginate();

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
        $members = $request->all();

        $member = Members::where('nip', $request->nip)->first();
        if ($member) {
            return response()->json([
                'error' => "The account (NIP: $request->nip ) already exists."
            ], 422);
        }

        $newPassword = $members['password'];
        if (!empty($newPassword)) {
            $hashedPassword = bcrypt($newPassword);
            $members['password'] = $hashedPassword;
        }

        Members::create($members);
        return response()->json(['success' => 'Data Saved Successfully!'], 200);
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
        $request->validate([
            'name' => 'required',
            'nip' => 'required',
            'position' => 'required',
            'sub_department' => 'required',
            'role' => 'required',
            'email' => 'required|email',
        ]);
        
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

    public function DeleteMember(Request $request)
    {
        $member = Members::findOrFail($request->id);
        $member->delete();
    }
}