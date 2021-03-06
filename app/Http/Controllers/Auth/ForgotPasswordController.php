<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use DB;
    use Carbon\Carbon;
    use App\Models\User;
    use Mail;
    use Hash;
    use Illuminate\Support\Str;

    class ForgotPasswordController extends Controller
    {
        /**
         * Write code on Method
         *
         * @return response()
         */
        public function showForgetPasswordForm()
        {
            return view('auth.passwords.reset');
        }

        /**
         * Write code on Method
         *
         * @return response()
         */
        public function submitForgetPasswordForm(Request $request)
        {
            $request->validate([
                'email' => 'required|email|exists:users',
            ]);

            $token = Str::random(64);

            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            Mail::send('email.forgetpassword', ['token' => $token], function ($message) use ($request)
            {
                $message->to($request->email);
                $message->subject('Restablece tu contraseña');
            });

            return back()->with('message', '¡Hemos enviado su enlace de restablecimiento de contraseña por correo electrónico!');
        }

        /**
         * Write code on Method
         *
         * @return response()
         */
        public function showResetPasswordForm($token)
        {
            return view('auth.passwords.email', ['token' => $token]);
        }

        /**
         * Write code on Method
         *
         * @return response()
         */
        public function submitResetPasswordForm(Request $request)
        {
            $request->validate([
                'email' => 'required|email|exists:users',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required'
            ]);

            $updatePassword = DB::table('password_resets')->where([
                'email' => $request->email,
                'token' => $request->token
            ])->first();

            if ( ! $updatePassword)
            {
                return back()->withInput()->with('error', '¡Dato no valido!');
            }

            $user = User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

            DB::table('password_resets')->where(['email' => $request->email])->delete();

            return redirect('/login')->with('message', '¡Tu contraseña ha sido cambiada!');
        }
    }
