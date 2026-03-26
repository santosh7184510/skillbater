use Illuminate\Http\Request;

public function login(Request $request)
{
    return response()->json([
        'success' => true,
        'message' => 'Login success'
    ]);
}

public function register(Request $request)
{
    return response()->json([
        'success' => true,
        'message' => 'Registered successfully'
    ]);
}