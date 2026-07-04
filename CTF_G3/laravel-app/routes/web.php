<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::get("/", function () {
    return view("welcome");
});

Route::post("/login", function (Request $request) {
    $email = $request->input("email");
    $password = $request->input("password");
    ini_set("zend.exception_ignore_args", "0");

    try {
        // Conexão direta via PDO — sem abstração do Laravel
        $host = config("database.connections.mysql.host");
        $db = config("database.connections.mysql.database");
        $user = config("database.connections.mysql.username");
        $pass = config("database.connections.mysql.password");

        $pdo = new \PDO("mysql:host={$host};dbname={$db}", $user, $pass);
        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = ? AND password = ? LIMIT 1",
        );
        $stmt->execute([$email, md5($password)]);
        $user = $stmt->fetch();

        if ($user) {
            return redirect("/");
        }
        return redirect("/")->with("error", "Invalid credentials.");
    } catch (\Exception $e) {
        \Log::error($e->getMessage(), ["trace" => $e->getTraceAsString()]);
        return redirect("/")->with("error", "Service temporarily unavailable.");
    }
});
