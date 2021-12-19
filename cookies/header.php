<?php
    //Here we control the header and the permissions. The pagcliente.php and adminer.php pages only can be reached by authorized users.
    
    session_start(); #start session
    $currentScript = basename($_SERVER['PHP_SELF'], '.php'); #gets the current page



    if(!empty($_SESSION) && array_key_exists('username', $_SESSION) && $_SESSION['type']) 
    {
        //cliente
        echo '
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
        <a class="navbar-brand" id="topo" href="index.php">Mercearia Do Bairro</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="pagCliente.php">Espaço Cliente</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="catalogo.php">Catálogo</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="carrinho.php">Carrinho</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
            </ul>
        </div>
        </div>
        </nav>';
    }
    elseif(!empty($_SESSION) && array_key_exists('username', $_SESSION) && !$_SESSION['type'])
    {
        //adminer
        echo '
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
        <a class="navbar-brand"  id="topo" href="index.php">Mercearia Do Bairro</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="adminer.php">Adminer área</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="catalogo.php">Catálogo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
        </div>
        </nav>';
    }
    else{
        //users trying to access adminer or cliente areas
        if($currentScript == "pagCliente" || $currentScript == "adminer" || $currentScript == "encomenda" || $currentScript == "carrinho"){
            echo'
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="container-fluid">
                    <a class="navbar-brand" id="topo" href="index.php">Mercearia Do Bairro</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="catalogo.php">Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="carrinho.php">Carrinho</a>
                        </li>
                        </ul>
                    </div>
                    </div>
                </nav>';
                echo "<script>alert('Faça login primeiro.')</script>";
                echo "<script>window.open('login.php','_self')</script>";
        }
        else{
                //users
                echo '
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="container-fluid">
                    <a class="navbar-brand"  id="topo" href="index.php">Mercearia Do Bairro</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="catalogo.php">Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="carrinho.php">Carrinho</a>
                        </li>
                        </ul>
                    </div>
                    </div>
                </nav>';
        }
    }
?>