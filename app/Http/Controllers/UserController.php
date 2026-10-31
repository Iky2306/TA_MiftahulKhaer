<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $departemen = DB::table('departemen')->orderBy('kode_dept')->get();
        $role = DB::table('roles')->orderBy('id')->get();
        $query = User::query();
        $query->select('admins.id', 'admins.name', 'email', 'roles.name as role', 'kode_cabang');
        $query->join('model_has_roles', 'admins.id', '=', 'model_has_roles.model_id');
        $query->join('roles', 'model_has_roles.role_id', '=', 'roles.id');
        $query->where('roles.name', '!=', 'administrator');
        if (!empty($request->name)) {
            $query->where('admins.name', 'like', '%' . $request->name . '%');
        }
        $admins = $query->paginate(10);
        $admins->appends(request()->all());

        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('admins.index', compact('admins', 'departemen', 'role', 'cabang'));
    }


    public function store(Request $request)
    {
        $nama_user = $request->nama_user;
        $email = $request->email;
        $kode_dept = $request->kode_dept;
        $role = $request->role;
        $password = bcrypt($request->password);
        $kode_cabang = $request->kode_cabang;
        DB::beginTransaction();
        try {

            $user = User::create([
                'name' => $nama_user,
                'email' => $email,
                'password' => $password,
                'kode_cabang' => $kode_cabang
            ]);

            $user->assignRole($role);

            DB::commit();

            return Redirect::back()->with(['success' => ' Data Berhasil Disimpan']);
        } catch (\Exception $e) {

            dd($e);
            DB::rollBack();
            return Redirect::back()->with(['warning' => ' Data Gagal Disimpan']);
        }
    }

    public function edit(Request $request)
    {
        $id_user = $request->id_user;
        $departemen = DB::table('departemen')->orderBy('kode_dept')->get();
        $role = DB::table('roles')->orderBy('id')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $user = DB::table('admins')
            ->join('model_has_roles', 'admins.id', '=', 'model_has_roles.model_id')
            ->where('id', $id_user)->first();
        return view('admins.edituser', compact('departemen', 'role', 'user', 'cabang'));
    }

    public function update(Request $request, $id_user)
    {
        $nama_user = $request->nama_user;
        $email = $request->email;
        $kode_dept = $request->kode_dept;
        $role = $request->role;
        $password = bcrypt($request->password);
        $kode_cabang = $request->kode_cabang;

        if (isset($request->password)) {
            $data = [
                'name' => $nama_user,
                'email' => $email,
                'kode_cabang' => $kode_cabang,
                'password' => $password
            ];
        } else {
            $data = [
                'name' => $nama_user,
                'email' => $email,
                'kode_cabang' => $kode_cabang
            ];
        }

        DB::beginTransaction();
        try {
            //UPdate Data User
            DB::table('admins')->where('id', $id_user)
                ->update($data);

            //UPdate Data Role
            DB::table('model_has_roles')->where('model_id', $id_user)
                ->update([
                    'role_id' => $role
                ]);

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {

            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }
    //fungsi delete sisitem absensi
    public function delete($id_user)
    {
        try {
            DB::table('admins')->where('id', $id_user)->delete();
            return Redirect::back()->with(['success' => ' Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => ' Data Gagal Dihapus']);
        }
    }

    /**
     * Form Ganti Password Admin/Staff
     */
    public function gantiPasswordForm()
    {
        $user = \Illuminate\Support\Facades\Auth::guard('user')->user();
        return view('admins.gantipassword', compact('user'));
    }

    /**
     * Proses Update Password Admin/Staff
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:5|confirmed',
        ], [
            'old_password.required' => 'Password lama wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 5 karakter',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok',
        ]);

        $user = \Illuminate\Support\Facades\Auth::guard('user')->user();

        // Verifikasi password lama
        if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $user->password)) {
            return Redirect::back()->with(['warning' => 'Password lama yang Anda masukkan salah']);
        }

        // Cek agar password baru tidak sama dengan password lama
        if (\Illuminate\Support\Facades\Hash::check($request->new_password, $user->password)) {
            return Redirect::back()->with(['warning' => 'Password baru tidak boleh sama dengan password lama']);
        }

        try {
            DB::table('admins')->where('id', $user->id)->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
                'updated_at' => now()
            ]);
            return Redirect::back()->with(['success' => 'Password berhasil diperbarui']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Password gagal diperbarui']);
        }
    }
}
