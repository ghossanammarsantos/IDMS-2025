<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\Role;
use App\AccessMenu;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role_id == 1) { // Asumsikan role_id 1 adalah administrator
                return $next($request);
            } elseif (auth()->guest()) {
                return $next($request);
            } else {
                return redirect('/');
            }
        });
    }

    public function showRegistrationForm()
    {
        $roles = Role::all(); // Ambil semua role
        $menus = AccessMenu::all();
        return view('auth.register', compact('roles', 'menus')); // Kirimkan variabel roles ke view
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'], // Validasi role_id
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
        ]);

        // Menyimpan menu_id ke tabel access_role
        if (isset($data['menu_id'])) {
            foreach ($data['menu_id'] as $menu_id) {
                \DB::table('access_role')->insert([
                    'role_id' => $data['role_id'],
                    'menu_id' => $menu_id,
                ]);
            }
        }

        return Redirect::route('auth.userlist')->with('success', 'User registered successfully. Please login.');
    }
}
