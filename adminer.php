<?php
    require_once('cookies/header.php');
    require_once('cookies/funcoes.php');

    if(!empty($_POST))
    {
        //-----------------------------------REGISTAR PRODUTO--------------------------------
        if(array_key_exists('registoProduto',$_POST))
        {
            require_once('cookies/valida.php');
            $errors = validaProdutos($_POST, $_FILES);

            if(!is_array($errors) && !is_string($errors)){

                $imgNome = $_FILES['imagem']['name'];
                $imgTmp = $_FILES['imagem']['tmp_name'];

                $imgNomeNovo =  time().'_'.$_FILES['imagem']['name'];
                $caminho = 'upload/'.$imgNomeNovo;
                move_uploaded_file($imgTmp, $caminho);

                require_once('cookies/configDb.php');

                $db = connectDB();

                if (is_string($db))
                {
                    echo("Fatal error! Please return later.");
                    die();
                }

                $nome = trim($_POST['nome']);
                $descricao = trim($_POST['descricao']);
                $preco = trim($_POST['preco']);
                $stock = trim($_POST['stock']);
                $categoria = ($_POST['categoria']);
                $caminhoImg = $caminho; 

                $query = "SELECT * FROM produtos WHERE nome=?";

                $statement = mysqli_prepare($db, $query);

                if(!$statement)
                {
                    echo "Algo correu mal. Por favor tente mais tarde.";
                }

                $result = mysqli_stmt_bind_param($statement,'s', $nome);

                if(!$result)
                {
                    echo "Algo correu muito mau, por favor tente mais tarde.";
                    die();
                }

                $result = mysqli_stmt_execute($statement);

                if(!$result)
                {
                    echo "Algo correu mal. Por favor tente mais tarde.";
                    die();
                }
                
                $result = mysqli_stmt_get_result($statement);

                if(!$result)
                {
                    echo "Algo correu mal, por favor tente mais tarde.";
                    die();
                }
                elseif(mysqli_num_rows($result) == 0)
                {
                    $query = "INSERT INTO produtos (nome, descricao, preco, stock, imagem) VALUES (?,?,?,?,?)";

                    $statement = mysqli_prepare($db, $query);

                    if(!$statement)
                    {
                        echo "Algo correu mal, por favor tente mais tarde.";
                        die();
                    }

                    $result = mysqli_stmt_bind_param($statement,'ssdis',$nome, $descricao, $preco, $stock, $caminhoImg);

                    if(!$result)
                    {
                        echo "Algo correu mal. Por favor tente novamente mais tarde.";
                        die();
                    }

                    $result = mysqli_stmt_execute($statement);

                    if(!$result){
                        echo "Algo correu mal. Por favor, tente mais tarde.";
                        die();
                    }
                    else{
                        $query = "SELECT * FROM produtos WHERE nome=?";

                        $statement = mysqli_prepare($db, $query);

                        if(!$statement)
                        {
                            echo "Algo correu mal. Por favor tente mais tarde.";
                        }

                        $result = mysqli_stmt_bind_param($statement, 's', $nome);

                        if(!$result)
                        {
                            echo "Algo correu muito mau, por favor tente mais tarde.";
                            die();
                        }

                        $result = mysqli_stmt_execute($statement);

                        if(!$result)
                        {
                            echo "Algo correu mal. Por favor tente mais tarde.";
                            die();
                        }
                        
                        $result = mysqli_stmt_get_result($statement);

                        if(!$result)
                        {
                            echo "Algo correu mal, por favor tente mais tarde.";
                            die();
                        }

                        $produto = mysqli_fetch_assoc($result);

                        $id_produto = trim($produto['id_produto']);
                        $categoria = trim($_POST['categoria']);

                        $query = "SELECT * FROM categorias WHERE nome=?";

                        $statement = mysqli_prepare($db, $query);

                        if(!$statement)
                        {
                            echo "Algo correu mal. Por favor tente mais tarde.";
                        }

                        $result = mysqli_stmt_bind_param($statement, 's', $categoria);

                        if(!$result)
                        {
                            echo "Algo correu muito mau, por favor tente mais tarde.";
                            die();
                        }

                        $result = mysqli_stmt_execute($statement);

                        if(!$result)
                        {
                            echo "Algo correu mal. Por favor tente mais tarde.";
                            die();
                        }
                        
                        $result = mysqli_stmt_get_result($statement);

                        if(!$result)
                        {
                            echo "Algo correu mal, por favor tente mais tarde.";
                            die();
                        }

                        $categoria = mysqli_fetch_assoc($result);
                        $id_categoria = trim($categoria['id_categoria']);

                        $query = "INSERT INTO CatProd (id_categoria, id_produto) VALUES (?,?)";

                        $statement = mysqli_prepare($db, $query);

                        if(!$statement)
                        {
                            echo "Algo correu mal, por favor tente mais tarde.";
                            die();
                        }

                        $result = mysqli_stmt_bind_param($statement, 'ii',$id_categoria, $id_produto);

                        if(!$result)
                        {
                            echo "Algo correu mal. Por favor tente novamente mais tarde.";
                            die();
                        }

                        $result = mysqli_stmt_execute($statement);

                        if(!$result){
                            echo "Algo correu mal. Por favor, tente mais tarde.";
                            die();
                        }
                        else
                        {
                            unset($_POST);
                            $result = closeDb($db);
                            echo "<script>alert('Produto registado com sucesso.')</script>";
                            echo "<script>window.open('adminer.php','_self')</script>";
                        }
                    }
                }
                else{
                    echo "<script>alert('Produto já registado.')</script>";
                    echo "<script>window.open('adminer.php','_self')</script>";
                }

            }
            elseif(is_string($errors)){
                echo $errors;
                unset($errors);
            }
        }

        //--------------------------------ALTERAR PRODUTO-----------------------------------
        if(array_key_exists('AltNome',$_POST)||array_key_exists('AltDesc',$_POST)||array_key_exists('AltStock',$_POST) || array_key_exists('AltPreco',$_POST)|| array_key_exists('AltImg',$_POST))
        {
            require_once('cookies/valida.php');
            $errors = validaAltProdutos($_POST, $_FILES);

            if(!is_array($errors) && !is_string($errors)){

                require_once('cookies/configDb.php');

                $db = connectDB();

                if (is_string($db))
                {
                    echo("Fatal error! Please return later.");
                    die();
                }

                if(array_key_exists('AltNome',$_POST))
                {
                    $nome = trim($_POST['nome']);
                    $id = trim($_POST['id']);

                    //check if username or email already exist - Prepared statement
                    $query = "SELECT nome FROM produtos WHERE nome=?";

                    //prepare the statement				
                    $statement = mysqli_prepare($db,$query);
                    
                    if (!$statement ){
                        //error preparing the statement. This should be regarded as a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();				
                    }				
                    
                    //now bind the parameters by order of appearance
                    $result = mysqli_stmt_bind_param($statement,'s',$nome); # 'ss' means that both parameters are expected to be strings.
                                    
                    if ( !$result ){
                        //error binding the parameters to the prepared statement. This is also a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();
                    }
                    
                    //execute the prepared statement
                    $result = mysqli_stmt_execute($statement);
                                
                    if( !$result ) {
                        //again a fatal error when executing the prepared statement
                        echo "Something went very wrong. Please try again later.";
                        die();
                    }
                    
                    //get the result set to further deal with it
                    $result = mysqli_stmt_get_result($statement);
                    
                    if (!$result){
                        //again a fatal error: if the result cannot be stored there is no going forward
                        echo "Something went wrong. Please try again later.";	
                        die();
                    }
                    elseif( mysqli_num_rows($result) == 0 ){

                        $query = "UPDATE produtos SET nome=? WHERE id_produto=?";

                        $statement = mysqli_prepare($db,$query);

                        if (!$statement){
                            //error preparing the statement. This should be regarded as a fatal error.
                            echo "Something went wrong. Please try again later.";
                            die();				
                        }				
                
                        //now bind the parameters by order of appearance
                        $result = mysqli_stmt_bind_param($statement,'si',$nome,$id);

                        if (!$result){
                            //error binding the parameters to the prepared statement. This is also a fatal error.
                            echo "Something went wrong. Please try again later.";
                            die();
                        }
                
                        //execute the prepared statement
                        $result = mysqli_stmt_execute($statement);	
                        if(!$result){
                            //again a fatal error when executing the prepared statement
                            echo "Something went very wrong. Please try again later.";
                            die();
                        }
                        else{               
                            //user registered - close db connection
                            $result = closeDb($db);
                            unset($_POST);
                            echo "<script>alert('Produto registado com sucesso.')</script>";
                            echo "<script>window.open('adminer.php','_self')</script>";
                        }

                    }elseif(mysqli_num_rows($result) > 0 ){
                        echo "<script>alert('Produto já existente.')</script>";
                        echo "<script>window.open('adminer.php','_self')</script>";
                        $result = closeDb($db);
                    }
                }

                if(array_key_exists('AltDesc',$_POST))
                {
                    $descricao = trim($_POST['descricao']);
                    $id = trim($_POST['id']);

                    $query = "UPDATE produtos SET descricao=? WHERE id_produto=?";

                    $statement = mysqli_prepare($db,$query);

                    if (!$statement){
                        //error preparing the statement. This should be regarded as a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();				
                    }				
                
                    //now bind the parameters by order of appearance
                    $result = mysqli_stmt_bind_param($statement,'si',$descricao,$id);

                    if (!$result){
                        //error binding the parameters to the prepared statement. This is also a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();
                    }
                
                    //execute the prepared statement
                    $result = mysqli_stmt_execute($statement);	
                    if(!$result){
                        //again a fatal error when executing the prepared statement
                        echo "Something went very wrong. Please try again later.";
                        die();
                    }
                    else{               
                        //user registered - close db connection
                        $result = closeDb($db);
                        unset($_POST);
                        echo "<script>alert('Descrição alterada com sucesso!')</script>";
                        echo "<script>window.open('adminer.php','_self')</script>";
                    }  
                }

                if(array_key_exists('AltStock',$_POST))
                {
                    $stock = trim($_POST['stock']);
                    $id = trim($_POST['id']);

                    $query = "UPDATE produtos SET stock=? WHERE id_produto=?";

                    $statement = mysqli_prepare($db,$query);

                    if (!$statement){
                        //error preparing the statement. This should be regarded as a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();				
                    }				
                
                    //now bind the parameters by order of appearance
                    $result = mysqli_stmt_bind_param($statement,'ii',$stock,$id);

                    if (!$result){
                        //error binding the parameters to the prepared statement. This is also a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();
                    }
                
                    //execute the prepared statement
                    $result = mysqli_stmt_execute($statement);	
                    if(!$result){
                        //again a fatal error when executing the prepared statement
                        echo "Something went very wrong. Please try again later.";
                        die();
                    }
                    else{               
                        //user registered - close db connection
                        $result = closeDb($db);
                        unset($_POST);
                        echo "<script>alert('Stock alterado com sucesso!.')</script>";
                        echo "<script>window.open('adminer.php','_self')</script>";
                    }
                }

                if(array_key_exists('AltPreco',$_POST))
                {
                    $preco = trim($_POST['preco']);
                    $id = trim($_POST['id']);

                    $query = "UPDATE produtos SET preco=? WHERE id_produto=?";

                    $statement = mysqli_prepare($db,$query);

                    if (!$statement){
                        //error preparing the statement. This should be regarded as a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();				
                    }				
                
                    //now bind the parameters by order of appearance
                    $result = mysqli_stmt_bind_param($statement,'di',$preco,$id);

                    if (!$result){
                        //error binding the parameters to the prepared statement. This is also a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();
                    }
                
                    //execute the prepared statement
                    $result = mysqli_stmt_execute($statement);	
                    if(!$result){
                        //again a fatal error when executing the prepared statement
                        echo "Something went very wrong. Please try again later.";
                        die();
                    }
                    else{               
                        //user registered - close db connection
                        $result = closeDb($db);
                        unset($_POST);
                        echo "<script>alert('Preço alterado com sucesso.')</script>";
                        echo "<script>window.open('adminer.php','_self')</script>";
                    }
                }

                if(array_key_exists('AltImg',$_POST))
                {
                    print_r($_POST);
                    print_r($_FILES);
                    $img = trim($_FILES['imagem']['name']);
                    $imgTmp = $_FILES['imagem']['tmp_name'];
                    echo $imgTmp;
                    $caminho = trim($_POST['imagemAntiga']);
                    echo $caminho;
                    
                    if(file_exists($caminho))
                    {
                        $result = unlink($caminho);
                        if($result)
                        {
                            $result = move_uploaded_file($imgTmp, $caminho);
                            if($result)
                            {
                                echo "<script>alert('Imagem alterada com sucesso!')</script>";
                                echo "<script>window.open('adminer.php','_self')</script>";
                            }
                            else
                            {
                                echo "<script>alert('Algo correu mal, tente mais tarde por favor.')</script>";
                                echo "<script>window.open('adminer.php','_self')</script>";
                            }
                        }
                        else
                        {
                            echo "<script>alert('Algo correu mal, tente mais tarde por favor.')</script>";
                            echo "<script>window.open('adminer.php','_self')</script>";
                        }
                    }
                    else
                    {
                        echo "<script>alert('Algo correu mal, tente mais tarde por favor.')</script>";
                        echo "<script>window.open('adminer.php','_self')</script>";
                    }
                }
            }
            else{
                echo "<script>alert('Campo inválido.')</script>";
                echo "<script>window.open('adminer.php','_self')</script>";
                unset($_POST);
            }
        }
        //-----------------------------------REGISTAR CATEGORIA------------------------------
        if(array_key_exists('registoCat', $_POST))
        {
            if(!empty($_POST['categoria']) && preg_match('/^[^\<\>\-\*]{5,30}$/',$_POST['categoria']))
            {
                require_once('cookies/configDb.php');

                $db = connectDB();

                if (is_string($db))
                {
                    echo("Fatal error! Please return later.");
                    die();
                }
                 
                $categoria = trim($_POST['categoria']);

                $query = "SELECT * FROM categorias WHERE nome=?";

                $statement = mysqli_prepare($db, $query);

                if(!$statement)
                {
                    echo "Algo correu mal. Por favor tente mais tarde.";
                }

                $result = mysqli_stmt_bind_param($statement, 's', $categoria);

                if(!$result)
                {
                    echo "Algo correu muito mau, por favor tente mais tarde.";
                    die();
                }

                $result = mysqli_stmt_execute($statement);

                if(!$result)
                {
                    echo "Algo correu mal. Por favor tente mais tarde.";
                    die();
                }
                
                $result = mysqli_stmt_get_result($statement);

                if(!$result)
                {
                    echo "Algo correu mal, por favor tente mais tarde.";
                    die();
                }
                elseif(mysqli_num_rows($result) == 0)
                {
                    $query = "INSERT INTO categorias (nome) VALUES (?)";

                    $statement = mysqli_prepare($db, $query);

                    if(!$statement)
                    {
                        echo "Algo correu mal, por favor tente mais tarde.";
                        die();
                    }

                    $result = mysqli_stmt_bind_param($statement, 's',$categoria);

                    if(!$result)
                    {
                        echo "Algo correu mal. Por favor tente novamente mais tarde.";
                        die();
                    }

                    $result = mysqli_stmt_execute($statement);

                    if(!$result){
                        echo "Algo correu mal. Por favor, tente mais tarde.";
                        die();
                    }
                    else{
                        $result = closeDb($db);
                        unset($_POST);
                        echo "<script>alert('Categoria registada com sucesso.')</script>";
                        echo "<script>window.open('adminer.php','_self')</script>";
                    }
                }
                else{
                    echo "<script>alert('Categoria já existente.')</script>";
                    echo "<script>window.open('adminer.php','_self')</script>";
                }
            }
            else
            {
                echo "<script>alert('Nome inválido.')</script>";
                echo "<script>window.open('adminer.php','_self')</script>";
                unset($_POST);
            }
        }

        //----------------------------------Alterar Categoria----------------------------------
        if(array_key_exists('Alterar',$_POST))
        {
            if(preg_match('/^[^\<\>\-\*]{5,30}$/',$_POST['categoria']))
            {   
                require_once('cookies/configDb.php');

                $db = connectDB();

                if (is_string($db))
                {
                    echo("Fatal error! Please return later.");
                    die();
                }
                
                $nome = trim($_POST['nome']);
                $id = trim($_POST['id']);

                $query = "SELECT * FROM categorias WHERE nome=?";

                $statement = mysqli_prepare($db, $query);

                if(!$statement)
                {
                    echo "Algo correu mal. Por favor tente mais tarde.";
                }

                $result = mysqli_stmt_bind_param($statement, 's', $nome);

                if(!$result)
                {
                    echo "Algo correu muito mau, por favor tente mais tarde.";
                    die();
                }

                $result = mysqli_stmt_execute($statement);

                if(!$result)
                {
                    echo "Algo correu mal. Por favor tente mais tarde.";
                    die();
                }
                    
                $result = mysqli_stmt_get_result($statement);

                if(!$result)
                {
                    echo "Algo correu mal, por favor tente mais tarde.";
                    die();
                }
                elseif(mysqli_num_rows($result) == 0)
                {
                    $query = "UPDATE categorias SET nome=? WHERE id_categoria=?";

                    $statement = mysqli_prepare($db,$query);

                    if (!$statement){
                        //error preparing the statement. This should be regarded as a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();				
                    }				
            
                    //now bind the parameters by order of appearance
                    $result = mysqli_stmt_bind_param($statement,'si',$nome,$id);

                    if (!$result){
                        //error binding the parameters to the prepared statement. This is also a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();
                    }
            
                    //execute the prepared statement
                    $result = mysqli_stmt_execute($statement);	
                    if(!$result){
                        //again a fatal error when executing the prepared statement
                        echo "Something went very wrong. Please try again later.";
                        die();
                    }
                    else{
                        //registered - close db connection
                        $result = closeDb($db);
                        unset($_POST);
                        echo "<script>alert('Nome inválido.')</script>";
                        echo "<script>window.open('adminer.php','_self')</script>";
                    }
                }
                else{
                    echo "<script>alert('Categoria já existente.')</script>";
                    echo "<script>window.open('adminer.php','_self')</script>";
                    unset($_POST);
                }
            }
            else
            {
                echo "<script>alert('Nome inválido.')</script>";
                echo "<script>window.open('adminer.php','_self')</script>";
                unset($_POST);
            }
        }

        //-------------------------------------Apagar Comentários------------------------------
        if(array_key_exists('apagarCom',$_POST))
        {
            if(array_key_exists('id_comentario', $_POST) && is_numeric(trim($_POST['id_comentario'])))
            {
                require_once('cookies/configDb.php');

                $db = connectDB();

                if (is_string($db))
                {
                    echo("Fatal error! Please return later.");
                    die();
                }

                $id = trim($_POST['id_comentario']);
                
                $query="SELECT id_comentario FROM comentarios WHERE id_comentario=?";

                $statement = mysqli_prepare($db,$query);
				
                if (!$statement ){
                    //error preparing the statement. This should be regarded as a fatal error.
                    echo "Something went wrong. Please try again later.";
                    die();				
                }				
                                        
                //now bind the parameters by order of appearance
                $result = mysqli_stmt_bind_param($statement,'i',$id); 
                
                if ( !$result ){
                    //error binding the parameters to the prepared statement. This is also a fatal error.
                    echo "Something went wrong. Please try again later.";
                    die();
                }
                            
                //execute the prepared statement
                $result = mysqli_stmt_execute($statement);
                                        
                if( !$result ) {
                    //again a fatal error when executing the prepared statement
                    echo "Something went very wrong. Please try again later.";
                    die();
                }
                            
                //get the result set to further deal with it
                $result = mysqli_stmt_get_result($statement);
                            
                if (!$result){
                    //again a fatal error: if the result cannot be stored there is no going forward
                    echo "Something went wrong. Please try again later.";	
                    die();
                }	
                elseif( mysqli_num_rows($result) == 1 ){

                    //construct the intend query
                    $query = "DELETE FROM comentarios WHERE id_comentario=?";
                        
                    //prepare the statement				
                    $statement = mysqli_prepare($db,$query);
                        
                    if (!$statement ){
                        //error preparing the statement. This should be regarded as a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();				
                    }				
                                    
                    //now bind the parameters by order of appearance
                    $result = mysqli_stmt_bind_param($statement,'i',$id); 
            
                    if ( !$result ){
                        //error binding the parameters to the prepared statement. This is also a fatal error.
                        echo "Something went wrong. Please try again later.";
                        die();
                    }
                        
                    //execute the prepared statement
                    $result = mysqli_stmt_execute($statement);
                                    
                    if( !$result ) {
                        //again a fatal error when executing the prepared statement
                        echo "Something went very wrong. Please try again later.";
                        die();
                    }

                    //success! Close db connection
                    $result = closeDb($db);
                    unset($_POST);
                    echo "<script>alert('Comentário apagado!')</script>";
                    echo "<script>window.open('adminer.php','_self')</script>";
                }
            }
            else{
                echo "<script>alert('Algo correu mal, tente mais tarde por favor.')</script>";
                echo "<script>window.open('adminer.php','_self')</script>";
                unset($_POST);
            }
        }

        //--------------------------------FINALIZAR ENCOMENDA-------------------------------
        if(array_key_exists('finalizar',$_POST))
        {
            if(array_key_exists('id', $_POST) && is_numeric(trim($_POST['id'])))
            {
                require_once('cookies/configDb.php');

                $db = connectDB();

                if (is_string($db))
                {
                    echo("Fatal error! Please return later.");
                    die();
                }

                $id = trim($_POST['id']);
                
                $query="SELECT id_encomenda FROM encomendas WHERE id_encomenda=?";

                $statement = mysqli_prepare($db,$query);
				
                if (!$statement ){
                    //error preparing the statement. This should be regarded as a fatal error.
                    echo "Something went wrong. Please try again later.1";
                    die();				
                }				
                                        
                //now bind the parameters by order of appearance
                $result = mysqli_stmt_bind_param($statement,'i',$id); 
                
                if ( !$result ){
                    //error binding the parameters to the prepared statement. This is also a fatal error.
                    echo "Something went wrong. Please try again later.2";
                    die();
                }
                            
                //execute the prepared statement
                $result = mysqli_stmt_execute($statement);
                                        
                if( !$result ) {
                    //again a fatal error when executing the prepared statement
                    echo "Something went very wrong. Please try again later.3";
                    die();
                }
                            
                //get the result set to further deal with it
                $result = mysqli_stmt_get_result($statement);
                            
                if (!$result){
                    //again a fatal error: if the result cannot be stored there is no going forward
                    echo "Something went wrong. Please try again later.4";	
                    die();
                }	
                elseif( mysqli_num_rows($result) == 1 ){

                    $estado = "Paga";

                    $query = "UPDATE encomendas SET estado=? WHERE id_encomenda=?";
                    $statement = mysqli_prepare($db,$query);
				
                    if (!$statement ){
                        //error preparing the statement. This should be regarded as a fatal error.
                        echo "Something went wrong. Please try again later.5";
                        die();				
                    }				
                                            
                    //now bind the parameters by order of appearance
                    $result = mysqli_stmt_bind_param($statement,'si',$estado,$id); 
                    
                    if ( !$result ){
                        //error binding the parameters to the prepared statement. This is also a fatal error.
                        echo "Something went wrong. Please try again later.6";
                        die();
                    }
                                
                    //execute the prepared statement
                    $result = mysqli_stmt_execute($statement);
                                            
                    if( !$result ) {
                        //again a fatal error when executing the prepared statement
                        echo "Something went very wrong. Please try again later.7";
                        die();
                    }
                    else{
                        unset($_POST);
                        $result = closeDb($db);
                        echo "<script>alert('Encomenda registada.')</script>";
                        echo "<script>window.open('adminer.php','_self')</script>";
                    }	

                }  
            }else{
                echo "<script>alert('Algo correu mal, tente mais tarde por favor.')</script>";
                echo "<script>window.open('adminer.php','_self')</script>";
                unset($_POST);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/geral.css">
    <link rel="stylesheet" href="css/adminer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Adminer page</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark justify-content-end" style="background-color:#fcc344">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#registarProd">Registar Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#editarCat">Editar Categorias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#editarProd">Editar Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#comentariosAdmin">Editar Comentários</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#encomendasAdmin">Gerir encomendas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>'
                
    <div class="container mt-5">
        <!--FORMULÁRIO REGISTAR PRODUTOS-->
        <div class="regProd mx-3">
            <a id="RegistarProd"><h2>Registar produto</h2></a>
            <form action="" method="POST" enctype="multipart/form-data">
        
                <input type="text" name="nome" class="form-control" placeholder="Nome" id="nome" value="<?php
                if(!empty($errors) && !$errors['nome'] && array_key_exists('registoProduto',$_POST))
                {
                    echo trim($_POST['nome']);
                }
                ?>"><br>
                <?php
                    if(!empty($errors) && $errors['nome'][0] && array_key_exists('registoPorduto',$_POST)) 
                    {
                        echo $errors['nome'][1];
                    }
                ?><br>
                <textarea name="descricao" class="form-control" rows="3" placeholder="Descrição" id="descricao" rows="4" cols="20"><?php 
                if(!empty($errors) && !$errors['descricao'])
                {
                    echo trim($_POST['descricao']);
                }
                ?></textarea><br>
                <?php
                    if(!empty($errors) && $errors['descricao'][0])
                    {
                        echo $errors['descricao'][1]."<br>";
                    }
                ?><br>
                <input type="text" name="preco" class="form-control" id="preco" placeholder="Preço" value="<?php 
                    if(!empty($errors) && !$errors['preco'])
                    {
                        echo trim($_POST['preco']);
                    }
                    ?>"><br>
                <?php
                    if(!empty($errors) && $errors['preco'][0])
                    {
                        echo $errors['preco'][1]."<br>";
                    }
                ?><br>
                <input type="text" name="stock" class="form-control" id="stock" placeholder="Número em stock" value="<?php 
                    if(!empty($errors) && !$errors['stock'])
                    {
                        echo trim($_POST['stock']);
                    }
                    ?>"><br>
                <?php
                    if(!empty($errors) && $errors['stock'][0])
                    {
                        echo $errors['stock'][1]."<br>";
                    }
                ?>
                <select class="form-select" name="categoria" id="categoria">
                    <?php
                        sectionCategorias();
                    ?>
                </select>
                <?php
                    if(!empty($errors) && $errors['categoria'][0])
                    {
                        echo $errors['categoria'][1]."<br>";
                    }
                ?><br>
                <input type="file" name="imagem" class="form-control" id="imagem"><br>
                <?php 
                    if(!empty($errors) && $errors['imgError'][0])
                    {
                        echo $errors['imgError'][1]."<br>";
                    }
                    elseif(!empty($errors) && $errors['imgTamanho'][0])
                    {
                        echo $errors['imgTamanho'][1]."<br>";
                    }
                    elseif(!empty($errors) && $errors['imgFormato'][0])
                    {
                        echo $errors['imgFormato'][1]."<br>";
                    }
                ?><br>
                <input type="submit" value="Submeter" name="registoProduto">
            </form>
        </div>

        <!--FORMULÁRIO REGISTAR CATEGORIA-->
        <div class="regCat">
            <a id="editarCat"><h2>Registar categoria</h2></a>
            <div class="d-flex justify-content-center categoria">
                <form action="" method="POST">
                    <div class="input-group">
                        <input type="text" name="categoria" class="form-control" id="categoria">
                        <br>
                        <input type="submit" value="Submeter" name="registoCat">
                    </div>
                </form>
            </div>


            <!--FORMULÁRIO LISTAR E MODIFICAR CATEGORIA-->
            <h2 class="mt-5">Mostrar categoria</h2>
            <?php mostrarCategorias(); ?>
        </div>
    </div>
        <!-- ALTERAR PRODUTOS-->
        <?php mostrarProdutoAdmin() ?>

        <!--Apagar comentários-->
        <?php apagarComentario() ?>

        <!-- Mostrar Encomendas -->
        <?php mostrarEnc() ?>
    <?php require_once('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>
</html>