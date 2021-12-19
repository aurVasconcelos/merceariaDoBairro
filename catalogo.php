<?php
    require_once('cookies/header.php');
    require_once('cookies/funcoes.php');

    if(!empty($_POST) && array_key_exists('procurar',$_POST))
    {
        require_once('procurar.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/geral.css">
    <link rel="stylesheet" href="css/catalogo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>
<body>
    <div class="d-flex justify-content-center mt-5">
        <form action="" method="POST">
            <div class="input-group">
                <div class="form-outline">
                    <input type="search" id="form1" class="form-control" placeholder="O que procura?" name="pesquisa"/>
                </div>
                <button type="submit" class="btn btn-primary" name="procurar">
                    <img src="imagens/search.png" alt="Pesquisar" width="24" height="auto">
                </button>
            </div>
        </form>
    </div>
    <?php
        if(!empty($_POST))
        {
            if(isset($errors['valida']) && $errors['valida'][0])
            {
                echo '<p class="erro">'.$errors['valida'][1].'</p>';
            }
            if(isset($existe) && $existe[0])
            {
                echo '<p class="erro">'.$existe[1].'</p>';
            }
        }
     ?>
    <?php mostrarProdutoCart()?>
    <?php require_once('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>
</html>