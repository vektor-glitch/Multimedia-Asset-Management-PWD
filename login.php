<?php 
session_start();

// inget vek, ini buat cek ya vek apakah user sudah login atau belum, kalo udah bakal diarahin ke index.php
if (isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

$error = "";

include "connection.php";

// ini kalo misal udah ngisi username sama login ya vek, terus mencet login
if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // query + prepare statement yak vek
    $query = "SELECT id_user, username, password FROM users WHERE username = ?";
    $statement = $conn->prepare($query);

    // bind_param buat menghindari sql injection yak vek, banyak orang jahat huhu
    $statement->bind_param("s", $username);
    $statement->execute();

    // ini buat hasilnya ya vek
    $result = $statement->get_result();

    // cek username vek
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        // cek password vek
        if (password_verify($password, $user_data["password"])) {
            $_SESSION['id_user'] = $user_data["id_user"];
            $_SESSION['username'] = $user_data["username"];
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    } else {
        $error = "Usernamme atau password salah.";
    }
    $statement->close();
}

?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="./src/output.css" rel="stylesheet">
</head>
<body class="h-full">
    <div class="flex min-h-full flex-row-reverse">
    <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
    <div class="mx-auto w-full max-w-sm lg:w-96">
        <div>
        <h2 class="font-sans text-xl font-bold text-black dark:text-white">MAM.</h2>
        <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Selamat Datang!</h2>
        <p class="mt-2 text-sm/6 text-gray-500 dark:text-gray-400">Silahkan masuk untuk mengelola aset multimedia</p>
        <p></p>
        </div>

        <?php if ($error): ?>
        <div class="mt-2 rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <div class="mt-5">
        <div>
            <form action="" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-100">Username</label>
                <div class="mt-2">
                <input id="username" type="text" name="username" required autocomplete="username" placeholder="Masukkan Username" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500" value="<?= htmlspecialchars($username ?? '') ?>" />
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-100">Password</label>
                <div class="mt-2">
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan Password" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500" />
                </div>
            </div>

            <div class="mt-10">
                <button type="submit" name="login" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500">Masuk Sekarang</button>
            </div>
            </form>
        </div>

        <div class="mt-7">
            <div>
                <p class="mt-2 text-sm/6 text-gray-500 dark:text-gray-400 text-center">
                    Belum punya akun?
                    <a href="register.php" class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">Daftar Akun</a>
                </p>
            </div>
            <div class="mt-9 text-sm/6 text-gray-500 dark:text-gray-400 text-center">
                <p>@ 2026 Multimedia Lab Team - All rights reserved</p>
            </div>
        </div>
        </div>
    </div>
    </div>
    <div class="relative hidden w-0 flex-1 lg:block">
        <div class="relative z-10 flex h-full flex-col items-center justify-center px-12 text-center">
            <h2 class="font-sans text-4xl font-bold text-white">MAM System</h2>
            <p class="mt-4 text-lg text-gray-200">Portal Manajemen Kamera, Lensa, dan Perlengkapan Produksi.</p>
        </div>
        <img src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="" class="absolute inset-0 size-full object-cover" />
        <div class="absolute inset-0 bg-black/50"></div>
    </div>
</div>
</body>
</html>