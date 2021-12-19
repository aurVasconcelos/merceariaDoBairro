<?php

    //setcookie('carrinho',null,-1);
    //die();
    require_once('cookies/header.php');
    require_once('cookies/funcoes.php');

	//check if the user arrived here with a request
   if(!empty($_POST))
   {
        //check if the request is valid
        if (array_key_exists('quantidade',$_POST) && is_numeric($_POST['quantidade']) && $_POST['quantidade'] > 0 ){
                    
            //is there a cookie set already? If so, are there cart elements already chosen?
            if( !array_key_exists('carrinho', $_COOKIE) ){
                //well, the cart is empty. Let's create a new one and add the product the user sent
                    
                //is there a numeric product's ID?
                if ( !array_key_exists('id_produto', $_POST) || !is_numeric($_POST['id_produto']) ){
                    //request error. Send the user back with an error message
                    header('Location:catalogo.php');    	
                    die();
                }			
                
                /*Aurora, outra validação que deveria ser feita seria verificar se o produto em causa existe na BD e, caso uses stock, se a quantidade solicitada está disponível. 
                *Uma simples consulta na BD resolve isto. Não faço porque não tenho a tua BD e uma tabela preenchida (obviamente, em elseif). 				
                */
                    
                //all is right with the request. Let's create the cookie and send it to the user
                setcookie("carrinho", $_POST['id_produto']."=".$_POST['quantidade'].";", $expire = mktime().time()+60*60*24*30);
                
                //reload this page after cleaning the $_POST variable
                $_POST = array();
                header('Location:carrinho.php');
                die();
            }
            else{		
                //there is already a cart defined with products. Let's get them so that we can deal with the client's request
                $products = explode(';',$_COOKIE['carrinho']);

                //remove the last empty element (; without nothing to the right
                unset($products[array_key_last($products)]);

                //get the products lists with the respective quantities for each product
                $lista = array();

                for($i=0; $i<sizeof($products);$i++){ 
                    $produto = explode('=', $products[$i]);
                    $lista[$produto[0]] = $produto[1];
                }

                //is the added product already in the cart? If so, add to the existing quantity
                if(array_key_exists($_POST['id_produto'],$lista)){
                    $lista[$_POST['id_produto']] += $_POST['quantidade']; 
                }else{
                    $lista[$_POST['id_produto']] = $_POST['quantidade'];
                }

                //now create a new string of products
                $final = "";

                foreach($lista as $id => $quantidade)
                {
                    $final .= $id . "=" . $quantidade . ";";
                }
                
                //create the new cookie            
                setcookie("carrinho", $final, $expire = mktime().time()+60*60*24*30);

                //reload this page
                $_POST = array();
                header('Location:carrinho.php');
                die();
            }
        }
        else{
            //invalid request. Send the user back to the products page with an error message
            header('Location:catalogo.php');    	
            die();
        }
    }
    else{
        //show the cart or whatever	
        echo '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" href="css/geral.css">
                    <link rel="stylesheet" href="css/carrinho.css">
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
                    <title>Carrinho</title>
                </head>
                <body>
                    <h2 class="text-center my-3">O meu carrinho</h2>';
            
            if ( !empty($_COOKIE) && array_key_exists('carrinho', $_COOKIE)){
                
                $products = explode(';',$_COOKIE['carrinho']);

                //remove the last empty element (; without nothing to the right
                unset($products[array_key_last($products)]);

                //get the products lists with the respective quantities for each product
                $lista = array();

                for($i=0; $i<sizeof($products);$i++){
                    $produto = explode('=', $products[$i]);
                    $lista[$produto[0]] = $produto[1]; 
                }

                mostrarCarrinho($lista);

                echo '<div class="Comprar">
                    <form action="altCarrinho.php" method="POST">
                        <input type="submit" value="Limpar carrinho" name="limpar">
                    </form>
                    <form action="encomenda.php" method="POST">
                        <input type="hidden" name="produtos" value="'.$_COOKIE['carrinho'].'">
                        <input type="submit" value="Comprar" name="finalizar">
                    </form>
                </div>';
            }
            else{
                echo 'O seu carrinho ainda está vazio.';
            }
        require_once('footer.php');
        echo '
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
        </body>
            </html>';	
    }
?>