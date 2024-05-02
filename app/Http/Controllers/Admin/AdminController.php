<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminChangePasswordRequest;
use App\Http\Requests\AdminProfileRequest;
use Illuminate\Support\Facades\Session;
use App\Repositories\Admin\AdminRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public $adminRepository;
    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function index()
    {
        return view('admin.home');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function changePassword()
    {
        return view('admin.changePassword');
    }

    public function changePasswordStore(AdminChangePasswordRequest $request)
    {
        try {
            $this->adminRepository->changePassword($request);

            Session::flash('message', 'Password updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
        } catch (Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function profileEdit()
    {
        return view('admin.profileEdit');
    }

    public function profileUpdate(AdminProfileRequest $request)
    {
        try {
            $this->adminRepository->profileUpdate($request->all());

            if ($request->email != Auth::user()->email) {
                $msg = 'Email verification link has been sent to your email address. Please verify!';
                return redirect('login')->with([Auth::logout(), 'success' => $msg]);
            }

            Session::flash('message', 'Profile updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return redirect()->route('admin.profile');
        } catch (Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }
}
