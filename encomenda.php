<?php
    require_once('cookies/header.php');
    $precFinal = 0;
    $prod = "";

    if(!empty($_POST) && array_key_exists('submeter',$_POST))
    {
        require_once('cookies/valida.php');
        $errors = encomenda($_POST);

        if (!is_array( $errors) && !is_string( $errors)){
				
            require_once('cookies/configDb.php');
            
            //connected to the database
            $db = connectDB();
            
            //success?				
            if ( is_string($db) ){
                //error connecting to the database
                echo ("Fatal error! Please return later.");
                die();
            }

            //---------------------------------------------------Atualizar BD encomendas---------------------------
            $id = trim($_POST['id']);
            $username=trim($_POST['username']);
            $email=trim($_POST['email']);
            $morada=trim($_POST['morada']);
            $cidade=trim($_POST['cidade']);
            $codigoPostal=trim($_POST['codigoPostal']);
            $valor = trim($_POST['valor']);
            $data = date("Y-m-d h:i:s");
            $estado = "pendente";
            $referencia = trim($_POST['referencia']);

            $query = "INSERT INTO encomendas (id_cliente,nomeCliente,contacto,data,valor,estado,morada,codigoPostal,cidade,referencia) VALUES (?,?,?,?,?,?,?,?,?,?)";
            //prepare the statement				
			$statement = mysqli_prepare($db,$query);
				
			if (!$statement ){
				//error preparing the statement. This should be regarded as a fatal error.
					echo "Something went wrong. Please try again later.";
					die();				
			}				
				
			//now bind the parameters by order of appearance
			$result = mysqli_stmt_bind_param($statement,'isssdssssi',$id,$username,$email,$data,$valor,$estado,$morada,$codigoPostal,$cidade,$referencia); # 'ssss' means that all parameters are expected to be strings.
								
			if ( !$result){
				//error binding the parameters to the prepared statement. This is also a fatal error.
				echo "Something went wrong. Please try again later.1";
				die();
			}
			
			//execute the prepared statement
			$result = mysqli_stmt_execute($statement);
						
			if(!$result){
				//again a fatal error when executing the prepared statement
				echo "Something went very wrong. Please try again later.2";
				die();
			}
			else{ #----------------------------------------Atualizar BD relizacional------------------------------------

                //construct the intend query
				$query = "SELECT id_encomenda FROM encomendas WHERE nomeCliente=? AND data=? AND valor=?";
				
				//prepare the statement				
				$statement = mysqli_prepare($db,$query);
				
				if (!$statement ){
					//error preparing the statement. This should be regarded as a fatal error.
					echo "Something went wrong. Please try again later.";
					die();				
				}				
								
				//now bind the parameters by order of appearance
				$result = mysqli_stmt_bind_param($statement,'ssd',$username,$data,$valor); # 'ss' means that both parameters are expected to be strings.
								
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
                
                $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
                $id_encomenda = $result[0]['id_encomenda'];

                $products = explode(';',$_POST['produtos']);
                
                //remove the last empty element (; without nothing to the right
                unset($products[array_key_last($products)]);
                
                //get the products lists with the respective quantities for each product
                $lista = array();
                
                for($i=0; $i<sizeof($products);$i++){
                    $produto = explode('=', $products[$i]);
                    $lista[$produto[0]] = $produto[1]; 
                }
            
                $j = 0;
                foreach($lista as $key => $value)
                {
                    $id_prod[$j] = (trim($key));
                    $quantidade[$j] = (trim($value));
                    $j++;
                }

                sort($id_prod,SORT_NUMERIC);
                sort($quantidade,SORT_NUMERIC);

                $query = 'INSERT INTO EncProd (id_encomendas, id_produtos, quantidade) VALUES (?,?,?)';
                    
                //prepare the statement				
                $statement = mysqli_prepare($db,$query);
                        
                if (!$statement ){
                    //error preparing the statement. This should be regarded as a fatal error.
                    echo "Something went wrong. Please try again later. 1";
                    die();				
                }				
                                        
                for($i=0;$i<sizeof($id_prod);$i++)
                {
                    //now bind the parameters by order of appearance
                    $result = mysqli_stmt_bind_param($statement,'iii',$id_encomenda,$id_prod[$i],$quantidade[$i]); # 'ss' means that both parameters are expected to be strings.
                                                
                    //execute the prepared statement
                    $result = mysqli_stmt_execute($statement);
                }                  
                if( !$result ) {
                    //again a fatal error when executing the prepared statement
                    echo "Something went very wrong. Please try again later.";
                    die();
                }
                else{ //-----------------------------------------ATUALIZAR STOCK------------------------------
                    
                    $num = implode(",",array_fill(0,count($id_prod),'?'));
                    $sum = implode("",array_fill(0,count($id_prod),'i'));
                
                    $query = 'SELECT id_produto,stock FROM produtos WHERE id_produto IN ('.$num.')';
                            
                    //prepare the statement				
                    $statement = mysqli_prepare($db,$query);
                                
                    if (!$statement ){
                        //error preparing the statement. This should be regarded as a fatal error.
                        echo "Something went wrong. Please try again later. 1";
                        die();				
                    }				
                                                
                    //now bind the parameters by order of appearance
                    $result = mysqli_stmt_bind_param($statement,$sum,...$id_prod); # 'ss' means that both parameters are expected to be strings.
                                                
                    if ( !$result ){
                        //error binding the parameters to the prepared statement. This is also a fatal error.
                        echo "Something went wrong. Please try again later. 2";
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
                    $result = mysqli_fetch_all($result,MYSQLI_ASSOC);
                                
                    if (!$result){
                        //again a fatal error: if the result cannot be stored there is no going forward
                        echo "Something went wrong. Please try again later.";	
                        die();
                    }
                    else{

                        for($i=0;$i<sizeof($result);$i++)
                        {
                            $id_prod[$i] = $result[$i]['id_produto'];
                            $stock[$i] = $result[$i]['stock'] - $quantidade[$i]; 
                        }

                        $query = 'UPDATE produtos SET stock=? WHERE id_produto=?';
                            
                        //prepare the statement				
                        $statement = mysqli_prepare($db,$query);
                                    
                        if (!$statement ){
                            //error preparing the statement. This should be regarded as a fatal error.
                            echo "Something went wrong. Please try again later. 1";
                            die();				
                        }
                        
                        for($i=0;$i<sizeof($id_prod);$i++)
                        {
                            //now bind the parameters by order of appearance
                            $result = mysqli_stmt_bind_param($statement,'ii',$stock[$i],$id_prod[$i]); # 'ss' means that both parameters are expected to be strings.                           
                            if ( !$result ){
                                //error binding the parameters to the prepared statement. This is also a fatal error.
                                echo "Something went wrong. Please try again later. 2";
                                die();
                            }
                                        
                            //execute the prepared statement
                            $result = mysqli_stmt_execute($statement);
                        }
                                                    
                        if( !$result ) {
                            //again a fatal error when executing the prepared statement
                            echo "Something went very wrong. Please try again later.";
                            die();
                        }
                        else{
                            setcookie("carrinho", $final, $expire = mktime().time()+60*60*24*30);
                            echo "<script>alert('Encomenda registada com sucesso!')</script>";
                            echo "<script>window.open('catalogo.php','_self')</script>";

                        }
                    }
                }
            }
        }    

    }

echo'<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="css/geral.css">
            <link rel="stylesheet" href="css/encomendas.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
            <title>Encomendas</title>
        </head>
        <body><h2 class="mt-3 mx-5">Finalizar encomenda</h2><div class="container">';

            if(!empty($_POST) && (array_key_exists('finalizar',$_POST) || array_key_exists('submeter',$_POST)))
            {
                $products = explode(';',$_POST['produtos']);
            
                //remove the last empty element (; without nothing to the right
                unset($products[array_key_last($products)]);
            
                //get the products lists with the respective quantities for each product
                $lista = array();
            
                for($i=0; $i<sizeof($products);$i++){
                    $produto = explode('=', $products[$i]);
                    $lista[$produto[0]] = $produto[1]; 
                }
            
                require_once('cookies/configDb.php');
            
                $j = 0;
                foreach($lista as $key => $value)
                {
                    $id[$j] = (trim($key));
                    $quantidade[$j] = (trim($value));
                    $j++;
                }
            
                // sort($id,SORT_NUMERIC);
                // sort($quantidade,SORT_NUMERIC);
            
                $db = connectDB();
            
                if ( is_string($db) ){
                    //error connecting to the database
                    echo ("Fatal error! Please return later.");
                    die();
                }
            
                $num = implode(",",array_fill(0,count($id),'?'));
                $sum = implode("",array_fill(0,count($id),'i'));
            
                $query = 'SELECT * FROM produtos WHERE id_produto IN ('.$num.')';
                        
                //prepare the statement				
                $statement = mysqli_prepare($db,$query);
                            
                if (!$statement ){
                    //error preparing the statement. This should be regarded as a fatal error.
                    echo "Something went wrong. Please try again later.";
                    die();				
                }				
                                            
                //now bind the parameters by order of appearance
                $result = mysqli_stmt_bind_param($statement,$sum,...$id); # 'ss' means that both parameters are expected to be strings.
                                            
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
                else{
                    $produtos = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    $result = closeDb($db);
                    $precFinal = 0;
                    $prod = "";
                    echo '<div class="left"><table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Quantidade</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead><tbody>';
                    for($i=0;$i<sizeof($produtos);$i++)
                    {
                        $precFinal = $precFinal + $produtos[$i]['preco']*$quantidade[$i];
                        $prod .= $produtos[$i]['id_produto']."=".$quantidade[$i].";";
                            echo '<tr>
                                    <td>'.$produtos[$i]['nome'].'</td>
                                    <td>'.$quantidade[$i].'</td>
                                    <td>'.$produtos[$i]['preco']*$quantidade[$i].'€</td>
                                </tr>';
                    }

                    echo '</tbody>
                          <tfooter>
                            <tr>
                                <td>Total</td>
                                <td></td>
                                <th>'.$precFinal.'€</th>
                            </tr>
                            </tfooter></table>';
                    $entidade = "xxxxx";
                    $referencia = rand(100000000,999999999);
                    echo'<h5 class="mt-3">Dados de Pagamento</h5>
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Entidade</th>
                                    <th>Referência</th>
                                    <th>Montante</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>'.$entidade.'</td>
                                    <td>'.$referencia.'</td>
                                    <td>'.$precFinal.'€</td>
                                </tr>
                            </tbody>
                        </table>
                         </div>';
                } 
            }

            if(!empty($_SESSION) && array_key_exists('username', $_SESSION) && $_SESSION['type'])
            {
                require_once('cookies/configDb.php');
                $db = connectDB();

                $username=trim($_SESSION['username']);

                if(is_string ($db)){
                            
                    echo("Fatal error! Please return later.");
                    die();
                }

                $query="SELECT * FROM Clientes WHERE username=?";
                $statement = mysqli_prepare($db,$query);
                        
                if (!$statement ){
                    //error preparing the statement. This should be regarded as a fatal error.
                    echo "Something went wrong. Please try again later.";
                    die();				
                }				
                                            
                //now bind the parameters by order of appearance
                $result = mysqli_stmt_bind_param($statement,'s',$username); # 'ss' means that both parameters are expected to be strings.
                                            
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

                $result = mysqli_stmt_get_result($statement);

                if( !$result ) {
                    //again a fatal error when executing the prepared statement
                    echo "Something went very wrong. Please try again later.";
                    die();
                }

                if( mysqli_num_rows($result) == 1)
                {
                    $dados = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    $result=closeDb($db);
                    echo '<div class="right"><form action="" method="POST">
                            <label for="username">Nome</label><br>
                            <input type="text" name="username" class="form-control" id="username" value="'.trim($dados[0]['username']).'">';
                            if (!empty($errors) && $errors['username'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['username'][1] . "</p>";
                            } 
                    echo'<br><label for="email">Email</label><br>
                            <input type="text" name="email" class="form-control" id="email" value="'.trim($dados[0]['email']).'">';
                            if (!empty($errors) && $errors['email'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['email'][1] . "</p>";
                            }  
                    echo'<br><label for="morada">Morada</label><br>
                            <input type="text" name="morada" class="form-control" id="morada" value="'.trim($dados[0]['morada']).'">';
                            if(array_key_exists('morada',$_POST) && !empty($errors) && $errors['morada'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['morada'][1] . "</p>";
                            }  		
                    echo'<br>
                            <label for="cidade">Cidade</label><br>
                            <input type="text" name="cidade" class="form-control" id="cidade" value="'.trim($dados[0]['cidade']).'">
                            <br>';
                            if(array_key_exists('cidade',$_POST) && !empty($errors) && $errors['cidade'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['cidade'][1] . "</p>";
                            }
                    echo'<label for="codigoPostal">Código Postal</label><br>
                            <input type="text" name="codigoPostal" class="form-control" id="CodigoPostal" value="'.trim($dados[0]['codigoPostal']).'">';
                            if (!empty($errors) && $errors['codigoPostal'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                                echo '<p class="erro">'.$errors['codigoPostal'][1] . "</p>";
                            } 			

                    echo '
                            <input type="hidden" value="'.$dados[0]['id_cliente'].'" name="id">
                            <input type="hidden" value="'.$prod.'" name="produtos">
                            <input type="hidden" value="'.$precFinal.'" name="valor">
                            <input type="hidden" value="'.$referencia.'" name="referencia">
                            <input type="submit" value="Submeter" name="submeter">
                        </div></form>';
                        if(!empty($errors) && $errors['valor'][0])
                        {
                            echo '<p class="erro">'.$errors['valor'][1].'</p>';
                        }
                }
                else{
                    $result=closeDb($db);
                    echo "<script>alert('Algo correu mal. Por favor, tente mais tarde.')</script>";
                    echo "<script>window.open('index.php','_self')</script>";
                }
            }
            else{
                echo "<script>alert('Por favor, faça login primeiro.')</script>";
                echo "<script>window.open('login.php','_self')</script>";
                die();
            }
            echo '</div>';
            require_once('footer.php');
            echo'
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
                </body>
                    </html>';

?>