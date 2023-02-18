<?php

namespace App\Http\Controllers\Auth;

use stdClass;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required' // per sicurezza non do indicazioni sulla lunghezza minima della password
        ]);

        $user = User::whereEmail($request->email)->first(); //stessa cosa della linesa soppra

        if (!$user) {
            return response()->json([
                'errors' => 'This email is not registered'
            ], Response::HTTP_UNAUTHORIZED);
        }

        //la password dell'utente è criptata con hash per questo abbiamo bisogno di questo metodo Hash::check per verificare la corrispondenza fra la password inserita dall'utente e quella a db, invece durante il test, viene criptata quindi valuto le due possibilità dell'uguaglianza
        $checkPassword = Hash::check($request->password, $user->password);
        if ($checkPassword == false)
            $checkPassword = $request->password == $user->password;
        if (!$checkPassword) {
            return response()->json([
                'errors' => 'Wrong password'
            ], Response::HTTP_UNAUTHORIZED);
        }

        //create token di Sanctum
        $token = $user->createToken('company_api');

        //creo l'oggetto per la risposta
        $user_response = new stdClass();
        $user_response->name = $user->name;
        $user_response->email = $user->email;

        return response()->json([            
            'data' => $user_response,
            'token' => $token->plainTextToken
        ], 200);
    }
}