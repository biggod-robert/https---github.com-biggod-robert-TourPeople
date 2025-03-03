<!doctype html>
<html lang="es">

<head>
    <title>Login</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- cdn de sweet alert -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.4/sweetalert2.css" integrity="sha512-6qScZESleBziOBqJwOPurSy6lhLqHGjHNALOOFX0mzRVPiE5SZQvepRzeSO1OB475fcZywuMjxtkrFaO93aq9g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- hoja de estilos para el loader -->
    <link rel="stylesheet" href="../assets/css/loader.css">
    <!-- hoja de extilos para el login -->
    <link rel="stylesheet" href="../css/general_forms.css">
</head>

<body class="hidenn">
    <header>
        <!-- place navbar here -->
    </header>
    <main>
        <div class="conLoader" id="loader">
            <span class="loader"></span>
        </div>
        <div class="general-form">
            <form id="login_user">
                <div class="form-icon">
                    <img src="../assets/images/logo.png" alt="">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control item" id="correo" name="correo" placeholder="Correo">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control item" id="pass" name="pass" placeholder="Contraseña">
                </div>
                <div class="form-group captcha-container">
                    <label for="captcha">Captcha:</label>
                    <div class="d-flex align-items-center">
                        <canvas id="captchaCanvas" class="border"></canvas>
                        <button type="button" class="btn btn-dark ml-2 mx-2" id="reloadCaptcha">Recargar CAPTCHA</button>
                    </div>
                    <input type="text" class="form-control mt-2" id="captchaInput" name="captcha" placeholder="Ingrese el CAPTCHA">
                </div>


                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-send" id="loginBtn">Iniciar sesión</button>
                    <button type="button" class="btn btn-block btn-send registro">Registrarme</button>
                </div>
            </form>
            <div class="social-media">
                <h5><a href="../reset-password/">Olvide mi contraseña</a></h5>
            </div>
        </div>
    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3CJNRFC4P8"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-3CJNRFC4P8');
    </script>

    <!-- Bootstrap JavaScript Ldibracries -->
    <script src="../assets/js/bootstrap.min.js"></script>
    <!-- jquery librari f-->
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <!-- cdn js de sweet alert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.4/sweetalert2.min.js" integrity="sha512-a/ljmGyCvVDl+QZXCxw/6hKcG4V7Syo7qmb9lUFTwrP12lCCItvQKeTMBMjtpa+3RE6UZ7gk+/IZzj4H04y4ng==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- scrips del main.js -->
    <script src="../js/main.js"></script>
    <!-- scrips del login -->
    <script src="../js/login.js"></script>

</body>

</html>