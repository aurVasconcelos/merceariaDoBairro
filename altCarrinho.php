<?php
    if(!empty($_POST))
    {
        if(array_key_exists('alterarQuantidade',$_POST) && is_numeric($_POST['UpQuantidade']) && $_POST['UpQuantidade'] > 0)
        {
            $id = trim($_POST['id_produto']);
            
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

            if(isset($lista[$id])){
                $lista[$id] = $_POST['UpQuantidade']; 
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
            echo "<script>alert('Quantidade alterada com sucesso.')</script>";
            echo "<script>window.open('carrinho.php','_self')</script>";
            die();

        }


        if(array_key_exists('apagarDoCarrinho',$_POST))
        {
            $id=trim($_POST['id_produto']);
            
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
            if(isset($lista[$id])){
                unset($lista[$id]); 
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
            echo "<script>alert('Apagado com sucesso.')</script>";
            echo "<script>window.open('carrinho.php','_self')</script>";
            die();
        }

        if(array_key_exists('limpar',$_POST))
        {
            setcookie('carrinho',null,-1);
            $_POST = array();
            echo "<script>alert('Carrinho apagado.')</script>";
            echo "<script>window.open('carrinho.php','_self')</script>";
            die();
        }
    }


?>