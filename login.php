<?php
session_start();

// Configuración de la base de datos
$servername = "localhost";  // o la dirección IP de tu servidor MySQL
$username_db = "jenkins";      // tu usuario de MySQL
$password_db = "password";          // tu contraseña de MySQL
$dbname = "login_system";           // tu base de datos (debes cambiar el nombre según tu caso)

// Crear conexión
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prevenir inyecciones SQL con consultas preparadas
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);  // "s" para string
    $stmt->execute();
    $stmt->store_result();
    
    // Verificar si el usuario existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        // Depuración: imprime valores 
        //echo "<p>Username entered: $username</p>"; 
        //echo "<p>Stored password: $stored_password</p>";

        // Verificar si la contraseña es correcta
        if ($password == $stored_password) {
            $_SESSION['username'] = $username;  // Guardar usuario en sesión
            $message = "<p class=\"success\">Welcome, $username!</p>";
        } else {
            $message = "<p class=\"error\">Invalid username or password.</p>";
        }
    } else {
        $message = "<p class=\"error\">Invalid username or password.</p>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .login-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .login-container form {
            display: flex;
            flex-direction: column;
        }
        .login-container input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .login-container button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login de Ciberseguridad</h1>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php echo $message; ?>
    </div>
</body>
</html>
