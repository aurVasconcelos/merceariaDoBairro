<?php
  	// VERIFICAR FORMULÁRIO DE LOGIN	
  		if (!empty($_POST) && array_key_exists('login',$_POST)){
  			
  			//include validation tools
  			require_once('cookies/valida.php');
  		
  			//call general form validation function
  			$errors = validaFormLogin($_POST);
  		
  			//check validation result and act upon it
  			if (!is_array( $errors) && !is_string($errors) ){
				
				require_once('cookies/configDb.php');
				
				//connected to the database
				$db = connectDB();
				
				//success?				
				if ( is_string($db) ){
					//error connecting to the database
					echo ("Fatal error! Please return later.");
					die();
				}
				
				//building query string
				$username = trim($_POST['username']);		
				$password = trim(md5($_POST['password']));

				//construct the intend query
				$query = "SELECT * FROM Clientes WHERE username=? AND password=?";
				
				//prepare the statement				
				$statement = mysqli_prepare($db,$query);
				
				if (!$statement ){
					//error preparing the statement. This should be regarded as a fatal error.
					echo "Something went wrong. Please try again later.";
					die();				
				}				
								
				//now bind the parameters by order of appearance
				$result = mysqli_stmt_bind_param($statement,'ss',$username,$password); # 'ss' means that both parameters are expected to be strings.
								
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
				
				if(!$result){
					//again a fatal error: if the result cannot be stored there is no going forward
					echo "Something went wrong. Please try again later.";	
					die();
				}	
				elseif( mysqli_num_rows($result) == 1){
					//there is one user only with these credentials
										
					//open session
					session_start();
					
					//get user data
					$user = mysqli_fetch_assoc($result);
					
					//save username and id in session					
					$_SESSION['username'] = $user['username'];
                    $_SESSION['id'] = $user['id_cliente'];
                    $_SESSION['type'] = true;
				
					//user registered - close db connection
                    $result = closeDb($db);
                    echo "<script>alert('Bem vindo!')</script>";
					
					//send the user to another page
					header('Location:pagCliente.php');
				}
				elseif(mysqli_num_rows($result) == 0){

                    $query = "SELECT * FROM adminer WHERE username=? AND password=?";
				
				    //prepare the statement				
				    $statement = mysqli_prepare($db,$query);
				
				    if (!$statement ){
					    //error preparing the statement. This should be regarded as a fatal error.
					    echo "Something went wrong. Please try again later.";
					    die();				
				    }				
								
				    //now bind the parameters by order of appearance
				    $result = mysqli_stmt_bind_param($statement,'ss',$username,$password); # 'ss' means that both parameters are expected to be strings.
								
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
                    if (!$result){
                        //again a fatal error: if the result cannot be stored there is no going forward
                        echo "Something went wrong. Please try again later.";	
                        die();
                    }	
                    elseif( mysqli_num_rows($result) == 1){
                        //there is one user only with these credentials
                                            
                        //open session
                        session_start();
                        
                        //get user data
                        $user = mysqli_fetch_assoc($result);
                        
                        //save username and id in session					
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['id'] = $user['id_cliente'];
                        $_SESSION['type'] = false;
                    
                        //user registered - close db connection
                        $result = closeDb($db);
                        echo "<script>alert('Bem vindo!')</script>";
                        //send the user to another page
                        header('Location:adminer.php');
                    }
                    else{
					    echo "<script>alert('Invalid Username/Password')</script>";
                        $result = closeDb($db);
                    }
                }
                else{
                    echo "Fatal error, please return later.";
                }	
  			}
  			elseif( is_string($errors) ){
				  	//the function has received an invalid argument - this is a programmer error and must be corrected
				  	echo $errors;
				  	
				  	//so that there is no problem when displaying the form
				  	unset($errors);
  			}
  		}
		//VERIFICAR FORMULÁRIO DE REGISTO
  	    elseif ( !empty($_POST) && array_key_exists('submeter',$_POST)){
  			
  			//include validation tools
  			require_once('cookies/valida.php');
  		
  			//call general form validation function
  			$errors = validaFormRegisto($_POST);
  		
  			//check validation result and act upon it
  			if (!is_array( $errors) && !is_string($errors) ){
				
				require_once('cookies/configDb.php');
			
				//connected to the database
				$db = connectDB();
				
				//success?				
				if ( is_string($db) ){
					//error connecting to the database
					echo ("Fatal error! Please return later.");
					die();
				}
	
				//building query string
				$username = trim($_POST['username']);
				$email = trim($_POST['email']);			
				$password = md5(trim($_POST['password']));
                $morada = trim($_POST['morada']);
                $cidade = trim($_POST['cidade']);
                $codigoPostal = trim($_POST['codigoPostal']);
                $pais = trim($_POST['pais']);
				
				//check if username or email already exist - Prepared statement
				$query = "SELECT username,email FROM Clientes WHERE username=? OR email=?";

				//prepare the statement				
				$statement = mysqli_prepare($db,$query);
				
				if (!$statement ){
					//error preparing the statement. This should be regarded as a fatal error.
					echo "Something went wrong. Please try again later.";
					die();				
				}				
				
				//now bind the parameters by order of appearance
				$result = mysqli_stmt_bind_param($statement,'ss',$username,$email); # 'ss' means that both parameters are expected to be strings.
								
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
			
					$query = "INSERT INTO Clientes (username, email, password, morada, cidade, codigoPostal, pais) VALUES (?,?,?,?,?,?,?)"; 
					
					//prepare the statement				
					$statement = mysqli_prepare($db,$query);
				
					if (!$statement ){
						//error preparing the statement. This should be regarded as a fatal error.
						echo "Something went wrong. Please try again later.";
						die();				
					}				
				
					//now bind the parameters by order of appearance
					$result = mysqli_stmt_bind_param($statement,'sssssss',$username,$email,$password,$morada, $cidade, $codigoPostal, $pais); # 'ssss' means that all parameters are expected to be strings.
								
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
								
						//open session
						session_start();
					
						//get user data
						$user = mysqli_fetch_assoc($result);
					
						//save username and id in session					
						$_SESSION['username'] = $user['username'];
                        $_SESSION['id'] = $user['id_cliente'];
                        $_SESSION['type'] = true;
					
						//send the user to another page
						header('Location:pagCliente.php');
					}
				}
				else{
					//there already an username or an email in the database matching the imputed data. Which one is it? Or they both exist?
					
					//get all rows returned in the result: one can have a row if there is only the email or username or two rows if both exist in different records
					$existingRecords = array('email' => false, 'username' => false);					
					
					//now do it as you normally did it					
					while( $row = mysqli_fetch_assoc($result) ) {	
				
						if ( $row['username'] == $username ){
							$existingRecords['username'] = true;						
						}
						if( $row['email'] == $email ) {
							$existingRecords['email'] = true;
						}
					}//end while																
				}//end else	
  			}
  			elseif( is_string($errors) ){
				  	//the function has received an invalid argument - this is a programmer error and must be corrected
				  	echo $errors;
				  	
				  	//so that there is no problem when displaying the form
				  	unset($errors);
  			}
		}
		elseif(!empty($_POST) && !array_key_exists('login',$_POST) && !array_key_exists('submeter',$_POST)){
			echo "Erro! Tente ser uma pessoa normal. Obrigada.";
			die();
		} 
    ?>
    <!-- VERIFICA SE DADOS DO REGISTO SÃO REPETIDOS -->
    <?php
		//show if there is alresady either the same username or email in the user table on the database. This code can be placed anywhere the student desires. 
		if ( !empty($existingRecords) ){
			
			if ( $existingRecords['username'] && $existingRecords['email'] ){
				//both the username and the email already exist in the database
				echo "<script>alert('Both username and email already exist in our records.')</script>";				
			}
			elseif( $existingRecords['username'] ) {
				//only the username exists (you can erase the written username so that it does not show up in the filled form, but it seams better to keep it so that the user knows what was the input)
				echo "<script>alert('This username is already taken. Please choose another one.')</script>";
			}
			else {
				//only the email exists (you can erase the written email so that it does not show up in the filled form, but it seams better to keep it so that the user knows what was the input)
				echo "<script>alert('This email is already taken. Please choose another one.')</script>";
			}
		}//end main if
    ?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="css/geral.css">
    <link rel="stylesheet" type="text/css" href="css/formLogin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
  </head>
  <body> 
    
  <?php
		//include the header div, where authentication is checked and the navigation menu is placed.
  		require_once('cookies/header.php');
  ?>
    
    <div class="container">

        <!-- FORMULÁRIO DE LOGIN -->
        <div class="formEsquerda">
            <h1>Iniciar Sessão</h1>
            <form action="" method="POST" class="formLogin">
                <input type="text" id="username" name="username" class="form-control" placeholder="Nome de Utilizador" value="<?php
                    if (array_key_exists('login',$_POST) && !empty($errors) && !$errors['username'][0] ){ #this is done to keep the value inputted by the user if this field is valid but others are not
                        echo $_POST['username'];
                    }  
                ?>"><br>
                <?php
                    if (array_key_exists('login',$_POST) && !empty($errors) && $errors['username'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                        echo $errors['username'][1] . "<br>";
                    }  		
                ?>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" ><br>
                <?php
                    if (array_key_exists('login',$_POST) && !empty($errors) && $errors['password'][0] ){
                        echo $errors['password'][1] . "<br>";
                    }  		
                ?>
                <input type="submit" value="Login" name="login">
            </form>
        </div>
        <!-- FORMULÁRIO DE REGISTO -->
        <div class="formDireita">
            <h1>Criar Conta</h1>
            <form action="" method="POST" class="formRegister">
                <input type="text" id="username" name="username" class="form-control" placeholder="Nome de Utilizador"  value="<?php
                if ( array_key_exists('submeter',$_POST) && !empty($errors) && !$errors['username'][0] ){ #this is done to keep the value inputted by the user if this field is valid but others are not
                    echo $_POST['username'];
                }  
                ?>" require><br>
                <?php
                    if (array_key_exists('submeter',$_POST) && !empty($errors) && $errors['username'][0] ){ # Equal to "if ( !empty($errors) && $errors['username'][0] == true ){" #presents an error message if this field has invalid content
                        echo $errors['username'][1] . "<br>";
                    }  		
                ?>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" ><br>
                <?php
                    if (array_key_exists('submeter',$_POST) && !empty($errors) && $errors['password'][0] ){
                        echo $errors['password'][1] . "<br>";
                    }  		
                ?>

                <input type="password" id="rpassword" name="rpassword" class="form-control" placeholder="Repita a password" ><br>
                <?php
                    if ( array_key_exists('submeter',$_POST) && !empty($errors) && $errors['rpassword'][0] ){
                        echo $errors['rpassword'][1] . "<br>";
                    }  		
                ?>
            
                <input type="text" id="email" name="email" class="form-control" placeholder="Email" value="<?php
                if ( array_key_exists('submeter',$_POST) && !empty($errors) && !$errors['email'][0] ){ 
                    echo $_POST['email'];
                }  
                ?>"><br>
                <?php
                    if(array_key_exists('submeter',$_POST) && !empty($errors) && $errors['email'][0]){
                        echo $errors['email'][1] . "<br>";
                    }  		
                ?>

                <input type="text" id="morada" name="morada" class="form-control" placeholder="Morada" value="<?php
                if (array_key_exists('submeter',$_POST) && !empty($errors) && !$errors['morada'][0] ){ 
                    echo $_POST['morada'];
                }  
                ?>"><br>
                <?php
                    if (array_key_exists('submeter',$_POST) && !empty($errors) && $errors['morada'][0] ){ 
                        echo $errors['morada'][1] . "<br>";
                    }  		
                ?>

                <input type="text" id="codigoPostal" name="codigoPostal" class="form-control" placeholder="Código-Postal" value="<?php
                if (array_key_exists('submeter',$_POST) && !empty($errors) && !$errors['codigoPostal'][0] ){ 
                    echo $_POST['codigoPostal'];
                }
                ?>"><br>
                <?php
                    if (array_key_exists('submeter',$_POST) && !empty($errors) && $errors['codigoPostal'][0] ){ 
                        echo $errors['codigoPostal'][1] . "<br>";
                    }  		
                ?>
                    <input type="text" id="cidade" name="cidade" class="form-control" placeholder="Cidade" value="<?php
                    if (array_key_exists('submeter',$_POST) && !empty($errors) && !$errors['cidade'][0] ){ 
                        echo $_POST['cidade'];
                    }  
                ?>"><br>
                <?php
                    if (array_key_exists('submeter',$_POST) && !empty($errors) && $errors['cidade'][0] ){ 
                        echo $errors['cidade'][1] . "<br>";
                    }  		
                ?>
                <input type="text" id="pais" name="pais" class="form-control" placeholder="País"  value="<?php
                if (array_key_exists('submeter',$_POST) && !empty($errors) && !$errors['pais'][0] ){ 
                    echo $_POST['pais'];
                }  
                ?>"><br>
                <?php
                    if (array_key_exists('submeter',$_POST) && !empty($errors) && $errors['pais'][0] ){ 
                        echo $errors['pais'][1] . "<br>";
                    }  		
                ?>
                <input type="submit" value="Submeter" name="submeter">
            </form>
        </div>
    </div>

    <?php require_once('footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
  </body>
</html>