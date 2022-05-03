<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flash;
use Auth;
use App\User;
use Hash;

class AuthController extends Controller
{
    /* Show my account page */
    function getMyAccount()
    {
        $data['user'] = Auth::user();

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('user.account', $data);
    }

    /* Show my account page */
    function postUpdateProfile(Request $request)
    {

        $user = Auth::user();

        $this->validate($request, [
            'full_name' => 'required|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user->full_name = $request->full_name;
        $user->email = $request->email;

        if ($user->save()) {
            Flash::success('Your profile is saved.');
        } else {
            Flash::error('Unable to save your profile.');
        }

        return redirect('user/profile');
    }

    /* Show my account page */
    function postUpdatePassword(Request $request)
    {

        $user = User::findOrFail($request->user_id);

        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|min:8|max:255|different:current_password|confirmed',
            'new_password_confirmation' => 'required'
        ]);

        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->new_password);

            if ($user->save()) {
                Flash::success('Your password is updated.');
            } else {
                Flash::error('Unable to update your password.');
            }
        } else {
            Flash::error('Your current password is wrong.');
        }

        return redirect('user/profile');
    }
}
