<?php
    require_once('cookies/header.php');
    require_once('cookies/configDb.php');
    require_once('cookies/funcoes.php');

    $db = connectDB();
    if ( is_string($db)){
        //error connecting to the database
        echo ("Fatal error! Please return later.");
        die();
    }
     
    $id = trim($_SESSION['id']);
    $query = "SELECT * FROM Clientes WHERE id_cliente = ?";

    $statement = mysqli_prepare($db, $query);

    if(!$statement)
    {
        echo "Algo correu mal. Por favor tente mais tarde.";
    }

    $result = mysqli_stmt_bind_param($statement, 'i', $id);
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
    else{
        $row = mysqli_fetch_assoc($result);
        $result = closeDb($db); 
    }
    
    // -------------------------------Editar dados pessoais
    if(!empty($_POST) && (array_key_exists('username',$_POST) || array_key_exists('email',$_POST) || (array_key_exists('morada',$_POST) && array_key_exists('codigoPostal',$_POST) && array_key_exists('cidade',$_POST))))
    {
        require_once('cookies/valida.php');
        $errors = AlterarDadosCliente($_POST);

        if( !is_array($errors) && !is_string($errors)){

            $db = connectDB();

            if ( is_string($db)){
                //error connecting to the database
                echo ("Fatal error! Please return later.");
                die();
            }

            if(array_key_exists('username',$_POST))
            {
                $username = trim($_POST['username']);
                $id = trim($_SESSION['id']);

                $query = "UPDATE Clientes SET username=? WHERE id_cliente=?";

                $statement = mysqli_prepare($db,$query);

                if (!$statement){
                    //error preparing the statement. This should be regarded as a fatal error.
                    echo "Something went wrong. Please try again later.";
                    die();				
                }				
        
                //now bind the parameters by order of appearance
                $result = mysqli_stmt_bind_param($statement,'si',$username,$id);

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
                    $_SESSION['username'] = $username;
                    echo "<script>alert('Alteração feita com sucesso.')</script>";
                    header('Location:pagCliente.php');
                }
            }

            if(array_key_exists('email',$_POST))
            {
                $email = trim($_POST['email']);
                $id = trim($_SESSION['id']);

                $query = "UPDATE Clientes SET email=? WHERE id_cliente=?";

                $statement = mysqli_prepare($db,$query);

                if (!$statement){
                    //error preparing the statement. This should be regarded as a fatal error.
                    echo "Something went wrong. Please try again later.";
                    die();				
                }				
        
                //now bind the parameters by order of appearance
                $result = mysqli_stmt_bind_param($statement,'si',$email,$id);

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
                    $_SESSION['username'] = $username;
                    echo "<script>alert('Alteração feita com sucesso.')</script>";
                    header('Location:pagCliente.php');
                }
            }

            if(array_key_exists('morada',$_POST) && array_key_exists('codigoPostal',$_POST) && array_key_exists('cidade',$_POST))
            {
                $morada = trim($_POST['morada']);
                $codigoPostal = trim($_POST['codigoPostal']);
                $cidade = trim($_POST['cidade']);
                $id = trim($_SESSION['id']);

                $query = "UPDATE Clientes SET morada=?, cidade=?, codigoPostal=? WHERE id_cliente=?";

                $statement = mysqli_prepare($db,$query);

                if (!$statement){
                    //error preparing the statement. This should be regarded as a fatal error.
                    echo "Something went wrong. Please try again later.";
                    die();				
                }				
        
                //now bind the parameters by order of appearance
                $result = mysqli_stmt_bind_param($statement,'sssi',$morada,$cidade, $codigoPostal, $id);

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
                    $_SESSION['username'] = $username;
                    echo "<script>alert('Alteração feita com sucesso.')</script>";
                    header('Location:pagCliente.php');
                }
            }
        }
    }

    //--------------------------------ADICIONAR COMENTÁRIO
    if(!empty($_POST) && array_key_exists('Coment',$_POST))
    {
        require_once('cookies/valida.php');
        $errors = Comentarios($_POST);

        if(!is_array($errors) && !is_string($errors))
        {
            $db = connectDB();

            if ( is_string($db)){
                //error connecting to the database
                echo ("Fatal error! Please return later.");
                die();
            }

            $id = trim($_SESSION['id']);
            $comentario = trim($_POST['comentario']);
            $data = date("Y-m-d h:i:s");
            $avaliacao = trim($_POST['avaliacao']);

            $query = "INSERT INTO comentarios (id_clientes,comentario,data,avaliacao) VALUES (?,?,?,?)";
            //prepare the statement				
			$statement = mysqli_prepare($db,$query);
				
			if (!$statement ){
				//error preparing the statement. This should be regarded as a fatal error.
					echo "Something went wrong. Please try again later.";
					die();				
			}				
				
			//now bind the parameters by order of appearance
			$result = mysqli_stmt_bind_param($statement,'issd',$id,$comentario,$data,$avaliacao); # 'ssss' means that all parameters are expected to be strings.
								
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
			else{	
				//user registered - close db connection
                $result = closeDb($db);
                echo "<script>alert('Alteração feita com sucesso.')</script>";
            }				
        }
    }
?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>Area do Cliente</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="css/geral.css">
        <link rel="stylesheet" href="css/cliente.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    </head>
    <body> 
        <div class="container">
            <div class="forms">
                <div class="left">
                    <h2>Dados Pessoais</h2>
                    <form action="" method="post">
                        <label for="username">Nome:</label><br>
                        <input type="text" name="username" id="username" class="form-control" placeholder="<?php echo trim($row['username']);?>">
                        <input type="submit" value="Alterar Nome" name="nome">
                        <?php
                            if (array_key_exists('username',$_POST) && !empty($errors) && $errors['username'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['username'][1] . "</p>";
                            }  		
                        ?>
                    </form>
                    <form action="" method="post">
                        <label for="email">Email:</label><br>
                        <input type="text" name="email" id="email" class="form-control" placeholder="<?php echo trim($row['email']); ?>">
                        <input type="submit" value="Alterar email" name="mail">
                        <?php
                            if (array_key_exists('email',$_POST) && !empty($errors) && $errors['email'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['email'][1] . "</p>";
                            }  		
                        ?>
                    </form>
                    <form action="" method="post">
                        <label for="morada">Morada:</label><br>
                        <input type="text" name="morada" id="morada" class="form-control" placeholder="<?php echo trim($row['morada']);?>">
                        <?php
                            if(array_key_exists('morada',$_POST) && !empty($errors) && $errors['morada'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['morada'][1] . "</p>";
                            }  		
                        ?>
                        <br>
                        <label for="cidade">Cidade:</label><br>
                        <input type="text" name="cidade" id="cidade" class="form-control" placeholder="<?php echo trim($row['cidade']);?>">
                        <br>
                        <?php
                            if(array_key_exists('cidade',$_POST) && !empty($errors) && $errors['cidade'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['cidade'][1] . "</p>";
                            }  		
                        ?>
                        <label for="codigoPostal">Código Postal:</label><br>
                        <input type="text" name="codigoPostal" id="CodigoPostal" class="form-control" placeholder="<?php echo trim($row['codigoPostal']);?>">
                        <?php
                            if (array_key_exists('codigoPostal',$_POST) && !empty($errors) && $errors['codigoPostal'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['codigoPostal'][1] . "</p>";
                            } 		
                        ?>
                        <input type="submit" value="Alterar morada" name="habitacao">
                        <?php 
                            if ((array_key_exists('morada',$_POST) && array_key_exists('codigoPostal',$_POST) && array_key_exists('cidade',$_POST)) && !empty($errors) && $errors['preencher'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['preencher'][1] . "</p>";
                            }
                        ?>
                    </form>
                </div>
                <div class="right">
                    <h2>Deixe uma avaliação sobre o nosso serviço!</h2>
                    <form action="" method="post">
                        <label class="form-label" for="avaliacao">Pontuação de 0 a 100:</label>
                        <input type="range" id="avaliacao" class="form-range" name="avaliacao" min="0" max="100">
                        <label for="comentario">Deixe um comentário:</label>
                        <textarea name="comentario" id="comentario" class="form-control" rows="3"></textarea>
                        <?php
                            if (array_key_exists('Coment',$_POST) && !empty($errors) && $errors['comentario'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['comentario'][1]."</p>";
                            } 		
                        ?>
                        <input type="submit" value="Enviar" name="Coment">
                    </form>
                </div>
            </div>
            <div>
                <?php listarProdCliente($id) ?>
            </div>
        </div>

        <?php require_once('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    </body>
</html>

