<?php
			//this function's aim is to validate all fields that make up the login form
			function validaFormLogin( $dados ){
				
				//include the web application configuration file to have boundaries to be able to validate fields
				require_once('config.php');
				
				$errors = array('username' => array(false, "Invalid username: it must have between $minUsername and $maxUsername chars."),
								'password' => array(false, "Invalid password: it must have between $minPassword and $maxPassword chars and special chars."),
							    );	
				
				$flag = false;		
						
				/* Check if the imputed data is according what is expected for this function
				 * In short, $dados must have at least the necessary fields to enable their validation.
				 */			
				if ( !is_array($dados) || count (array_intersect_key(array_keys($errors), array_keys($dados) ) ) < 2 ){
					return("This function needs a parameter with the following format: array(\"username\"=> \"value\", \"password\"=> \"value\")");
					die();
				}			
			
				$dados['username'] = trim($dados['username']);
				$dados['password'] = trim($dados['password']);
					
				//validate username
				if( !validateUsername($dados['username'], $minUsername, $maxUsername) ){
					$errors['username'][0] = true;
					$flag = true;				
				}
			
				if( !validatePassword($dados['password'], $minPassword, $maxPassword) ){
					$errors['password'][0] = true;
					$flag = true;				
				}
				
				//deal with the validation results
				if ( $flag == true ){
					//there are fields with invalid contents: return the errors array
					return($errors);
				}
				else{
					//all fields have valid contents
					return(true);				
				}
			}
			
			//this function's aim is to validate all fields that make up the register form
			function validaFormRegisto( $dados ){
				
				//include the web application configuration file to have boundaries to be able to validate fields
				require('config.php');
		
				$errors = array('username' => array(false, "Invalid username: it must have between $minUsername and $maxUsername chars."),
								'password' => array(false, "Invalid password: it must have between $minPassword and $maxPassword chars and special chars."),
							    'rpassword' => array(false, "Passwords mismatch."),
								'email' => array(false,'Invalid email.'),
                                'morada' => array(false, 'Morada invalida.'),
                                'codigoPostal' => array(false, 'Código Postal invalido'),
                                'cidade' => array(false, 'Cidade invalida.'),
                                'pais' => array(false, 'País Invalido.')
								   );

				$flag = false;
				
				/* Check if the imputed data is according what is expected for this function
				 * In short, $dados must have at least the necessary fields to enable their validation.
				 */			
				if ( !is_array($dados) || count (array_intersect_key(array_keys($errors), array_keys($dados) ) ) < 8 ){
					return("This function needs a parameter with the following format: array(\"username\"=> \"value\", \"password\"=> \"value\", \"rpassword\"=> \"value\", \"email\"=> \"value\", \"morada\"=> \"value\", \"cidade\"=> \"value\", \"codigoPostal\"=> \"value\", \"pais\"=> \"value\")");
					die();
				}	

				$dados['username'] = trim($dados['username']);
				$dados['password'] = trim($dados['password']);
				$dados['rpassword'] = trim($dados['rpassword']);
                $dados['email'] = trim($dados['email']);
                $dados['morada'] = trim($dados['morada']);		
                $dados['codigoPostal'] = trim($dados['codigoPostal']);
                $dados['cidade'] = trim($dados['cidade']);
                $dados['pais'] = trim($dados['pais']);	

				//validate username
				if( !validateUsername($dados['username'], $minUsername, $maxUsername) ){
					$errors['username'][0] = true;
					$flag = true;				
				}
											
				//check password
				if( !validatePassword($dados['password'], $minPassword, $maxPassword) ){
					$errors['password'][0] = true;
					$flag = true;				
				}
				elseif( $dados['rpassword'] != $dados['password']){
					$errors['rpassword'][0] = true;
					$flag = true;
				}
				
				if( !validateEmail($dados['email'])){
					$errors['email'][0] = true;
					$flag = true;				
                }
                
                if( !validaMoradaDescMail($dados['morada'], $minMorada, $maxMorada)){
				 	$errors['morada'][0] = true;
				 	$flag = true;				
                }
                
                if( !validaCPostal($dados['codigoPostal'])){
					$errors['codigoPostal'][0] = true;
					$flag = true;				
                }

                if( !validaCidade($dados['cidade'])){
					$errors['cidade'][0] = true;
					$flag = true;				
                }

                if( !validaPais($dados['pais'])){
					$errors['pais'][0] = true;
					$flag = true;				
                }
				
				//deal with the validation results
				if ( $flag == true ){
					//there are fields with invalid contents: return the errors array
					return($errors);
				}
				else{
					//all fields have valid contents
					return(true);				
				}
			}			
			
            function validaProdutos($dados,$imagem){

                require_once('config.php');

                $errors = array('nome' => array(false, "Apenas são aceites letras no nome."),
                                'descricao' => array(false, "Descrição demasiado comprida."),
                                'preco' => array(false, "O preço deve ser um número real."),
                                'stock' => array(false,'O stock deve ser um número inteiro.'),
                                'categoria' => array(false,'Por favor selecione uma categoria.'),
                                'imgError' => array(false, 'Erro a carregar a imagem, por favor tente mais tarde.'),
                                'imgFormato' => array(false, 'Formato inválido. Apenas são aceites jpg e png.'),
                                'imgTamanho' => array(false, 'Tamanho da imagem inválido, apenas aceites até 2MB.')
                            );

                $dados['nome'] = trim($dados['nome']);
                $dados['descricao'] = trim($dados['descricao']);
                $dados['preco'] = trim($dados['preco']);
                $dados['stock'] = trim($dados['stock']);
                $dados['categoria'] = trim($dados['categoria']);
                
                $flag = false;

                if(!validaNomeProd($dados['nome'], $minNomeProd, $maxNomeProd))
                {
                    $errors['nome'][0] = true;
                    $flag = true;
                }

                if(!validaMoradaDescMail($dados['descricao'],$minMorada,$maxMorada))
                {
                    $errors['descricao'][0] = true;
                    $flag = true;
                }

                if(!validaImagemError($imagem))
                {
                    $errors['imgError'][0] = true;
                    $flag = true;
                }

                if(!validaImagemForm($imagem))
                {
                    $errors['imgFormato'][0] = true;
                    $flag = true;
                }
                if(!validaImagemTam($imagem))
                {
                    $errors['imgTamanho'][0] = true;
                    $flag = true;
                }

                if(!is_numeric($dados['preco']) && $dados['preco']<= 0)
                {
                    $errors['preco'][0] = true;
                    $flag = true;
                }

                if(!is_numeric($dados['stock']) && $dados['stock']<0)
                {
                    $errors['stock'][0] = true;
                    $flag = true;
                }

                if(!array_key_exists('categoria',$dados))
                {
                    $errors['categoria'][0] = true;
                    $flag = true;
                }

                if( $flag == true)
                {
                    return($errors);
                }
                else
                {
                    return(true);
                }
            }

            function AlterarDadosCliente($dados){
                //include the web application configuration file to have boundaries to be able to validate fields
                require('config.php');
            
                $errors = array('username' => array(false, "Invalid username: it must have between $minUsername and $maxUsername chars."),
                                'email' => array(false,'Invalid email.'),
                                'morada' => array(false, 'Morada invalida.'),
                                'codigoPostal' => array(false, 'Código Postal invalido.'),
                                'cidade' => array(false, 'Cidade invalida.'),
                                'pais' => array(false, 'País Invalido.'),
                                'preencher' => array(false, 'Preencha os campos todos, por favor.')
                               );

                $flag = false;

                if(array_key_exists('username',$dados))
                {
                    $dados['username'] = trim($dados['username']);
                    if(!validateUsername($dados['username'], $minUsername, $maxUsername))
                    {
                        $errors['username'][0] = true;
					    $flag = true;	
                    }
                }

                if(array_key_exists('email',$dados))
                {
                    $dados['email'] = trim($dados['email']);
                    if(!validateEmail($dados['email']))
                    {
                        $errors['email'][0] = true;
					    $flag = true;
                    }
                }

                if(array_key_exists('habitacao',$dados))
                {
                    if(!empty($dados['morada']) && !empty($dados['codigoPostal']) && !empty($dados['pais']))
                    {
                        $dados['morada'] = trim($dados['morada']);
                        $dados['codigoPostal'] = trim($dados['codigoPostal']);
                        $dados['cidade'] = trim($dados['cidade']);

                        if( !validaCPostal($dados['codigoPostal'])){
                            $errors['codigoPostal'][0] = true;
                            $flag = true;				
                        }

                        if(!validaPais($dados['pais'])){
                            $errors['pais'][0] = true;
                            $flag = true;				
                        }

                        if(!validaMoradaDescMail($dados['morada'],$minMorada,$maxMorada))
                        {
                            $errors['morada'][0] = true;
                            $flag = true;
                        }
                    }
                    elseif(empty($dados['morada']) && empty($dados['codigoPostal']) && empty($dados['pais']))
                    {
                        $errors['preencher'][0] = true;
                        $flag = true;
                    }
                }

				
				//deal with the validation results
				if ( $flag == true ){
					//there are fields with invalid contents: return the errors array
					return($errors);
				}
				else{
					//all fields have valid contents
					return(true);				
				}

            }

            function pesquisar($dados){
                require_once('cookies/config.php');

                $errors = array('valida' => array(false, "Nome de produto inválido!"),
                                'existe' => array(false, "Produto não encontrado.")
                                );

                if(!validaNomeProd($dados['pesquisa'], $minNomeProd, $maxNomeProd))
                {
                    $errors['valida'][0] = true;
                    return($errors);
                }
                else{
                    return(true);
                }


            }

            function Comentarios($dados)
            {
                $errors = array( 'comentario' => array(false, "O comentário deve ter entre 10 a 200 caracteres. Tenha em atenção os caracteres especiais."));
                
                if ( !is_array($dados) || count (array_intersect_key(array_keys($errors), array_keys($dados) ) ) < 1 ){
					return("This function needs a parameter with the following format: array(\"comentário\"=> \"value\")");
					die();
                }
                
                $comentario =trim($dados['comentario']);
                
                $exp = '/^[^\<\>\-\*]{5,200}$/';

                if(preg_match($exp,$comentario))
                {
                    return(true);
                }
                else{
                    $errors['comentario'][0] = true;
                    return($errors);
                }
            }

            function encomenda($dados)
            {
                //include the web application configuration file to have boundaries to be able to validate fields
                require('config.php');
            
                $errors = array('username' => array(false, "Invalid username: it must have between $minUsername and $maxUsername chars."),
                                'email' => array(false,'Invalid email.'),
                                'morada' => array(false, 'Morada invalida.'),
                                'codigoPostal' => array(false, 'Código Postal invalido.'),
                                'cidade' => array(false, 'Cidade invalida.'),
                                'valor' => array(false, 'Por favor, volte mais tarde.')
                               );
                
                $dados['username'] = trim($dados['username']);
                $dados['morada'] = trim($dados['morada']);
                $dados['codigoPostal'] = trim($dados['codigoPostal']);
                $dados['cidade'] = trim($dados['cidade']);
                $dados['email'] = trim($dados['email']);
                $dados['valor'] = trim($dados['valor']);
            
                $flag = false;

                if(!is_numeric($dados['valor']))
                {
                    $errors['valor'][0] = true;
                    $flag = true;
                }

                if(!validaNome($dados['username'], $minUsername, $maxUsername))
                {
                    $errors['username'][0] = true;
				    $flag = true;	
                }

                if(!validateEmail($dados['email']))
                {
                    $errors['email'][0] = true;
				    $flag = true;
                }

                if( !validaCPostal($dados['codigoPostal'])){
                        $errors['codigoPostal'][0] = true;
                        $flag = true;				
                    }

                if(!validaMoradaDescMail($dados['morada'],$minMorada,$maxMorada))
                {
                    $errors['morada'][0] = true;
                    $flag = true;
                }
                
                if(!validaCidade($dados['cidade']))
                {
                    $errors['cidade'][0] = true;
                    $flag = true;
                }
				//deal with the validation results
				if ( $flag == true ){
					//there are fields with invalid contents: return the errors array
					return($errors);
				}
				else{
					//all fields have valid contents
					return(true);				
				}
            }

            function validaAltProdutos($dados,$imagem)
            {
                
                require_once('config.php');

                $errors = array('nome' => array(false, "Apenas são aceites letras no nome."),
                                'descricao' => array(false, "Descrição não aceita caracteres especiais e deve ter entre 10 a 200 caracteres."),
                                'preco' => array(false, "O preço deve ser um número real."),
                                'stock' => array(false,'O stock deve ser um número inteiro.'),
                                'categoria' => array(false,'Por favor selecione uma categoria.'),
                                'imgError' => array(false, 'Erro a carregar a imagem, por favor tente mais tarde.'),
                                'imgFormato' => array(false, 'Formato inválido. Apenas são aceites jpg e png.'),
                                'imgTamanho' => array(false, 'Tamanho da imagem inválido, apenas aceites até 2MB.')
                            );
                
                $flag = false;

                if(array_key_exists('AltNome',$dados))
                {
                    $dados['nome'] = trim($dados['nome']);
                    if(!validaNomeProd($dados['nome'], $minNomeProd, $maxNomeProd))
                    {
                        $errors['nome'][0] = true;
                        $flag = true;
                    }
                }

                if(array_key_exists('AltDesc',$dados))
                {
                    $dados['descricao'] = trim($dados['descricao']);
                    if(!validaMoradaDescMail($dados['descricao'],$minMorada,$maxMorada))
                    {
                        $errors['descricao'][0] = true;
                        $flag = true;
                    }
                }

                if(array_key_exists('AltImg',$dados))
                {
                    if(!validaImagemError($imagem))
                    {
                        $errors['imgError'][0] = true;
                        $flag = true;
                    }

                    if(!validaImagemForm($imagem))
                    {
                        $errors['imgFormato'][0] = true;
                        $flag = true;
                    }

                    if(!validaImagemTam($imagem))
                    {
                        $errors['imgTamanho'][0] = true;
                        $flag = true;
                    }
                }

                if(array_key_exists('AltPreco',$dados))
                {
                    $dados['preco'] = trim($dados['preco']);
                    if(!is_numeric($dados['preco'] && $dados['preco']<0))
                    {
                        $errors['preco'][0] = true;
                        $flag = true;
                    }
                }

                if(array_key_exists('AltStock',$dados))
                {
                    $dados['stock'] = trim($dados['stock']);
                    if(!is_numeric($dados['stock']) && $dados['stock']<0)
                    {
                        $errors['stock'][0] = true;
                        $flag = true;
                    }
                }

                if( $flag == true)
                {
                    return($errors);
                }
                else
                {
                    return(true);
                }
            }

            // function contacto($dados){

            //     require_once('cookies/config.php');

            //     $errors = array('email' => array(false, 'Email nválido, por favor insira um email válido.'),
            //                     'username' => array(false, 'Username inválido.'),
            //                     'assunto' => array(false, 'Assunto inválido, evite usar caracteres especiais, deve ter entre '.$minAssunto.' e '.$maxAssunto.'.')

            //     );

            //     $dados['username'] = trim($dados['username']);
            //     $dados['email'] = trim($dados['email']);
            //     $dados['assunto'] = trim($dados['assunto']);

            //     $flag=false;

            //     if( !validateUsername($dados['username'], $minUsername, $maxUsername) ){
			// 		$errors['username'][0] = true;
			// 		$flag = true;				
            //     }
            //     if(!validaMoradaDescMail($dados['email'])){
			// 		$errors['email'][0] = true;
			// 		$flag = true;				
            //     }

            //     if(!validaAssunto($dados['assunto'],$minAssunto,$maxAssunto))
            //     {
            //         $errors['assunto'][0] = true;
			// 		    $flag = true;
            //     }

            //     if ( $flag == true ){
			// 		//there are fields with invalid contents: return the errors array
			// 		return($errors);
			// 	}
			// 	else{
			// 		//all fields have valid contents
			// 		return(true);				
			// 	}
            // }

			/*---------------------------------------------------------------------------------------------------------------------------------------------
			 * Validation functions
			 */ 
	
			function validateUsername($username, $min, $max){
				
				$exp = "/^[A-z0-9_]{" . $min . "," . $max .'}$/';			
										
				if( !preg_match($exp, $username )){
					return (false);				
				}else {
					return(true);
				}
            }
            
            function validaNome($nome)
            {
                $exp ="/^[A-z ]{3,30}$/";

                if( !preg_match($exp, $nome)){
					return (false);				
				}else {
					return(true);
				}
            }

			function validatePassword($data, $min, $max){
				
				$exp = "/^[A-z0-9_\\\*\-]{" . $min . "," . $max .'}$/';			
					
				if( !preg_match($exp, $data)){
					return (false);				
				}else {
					return(true);
				}
			}

			function validateEmail($email){
				
				//remove unwanted chars that maybe included in the email field content
				$email = filter_var($email, FILTER_SANITIZE_EMAIL);
				
				//verify if the inputted email is according to validation standards
				if( !filter_var($email, FILTER_VALIDATE_EMAIL)){
					return (false);				
				}else {
					return(true);
				}
            }
            
            function validaMoradaDescMail($texto, $min, $max)
            {
                $exp = '/^[^\<\>\-\*]{'.$min.','.$max.'}$/';

                if(!preg_match($exp, $texto))
                {
                    return(false);
                }
                else
                {
                    return(true);
                }
            }

            function validaCPostal($codigoPostal)
            {
                $exp = '/^[0-9]{4}\-[0-9]{3}$/';

                if(!preg_match($exp, $codigoPostal))
                {
                    return(false);
                }
                else
                {
                    return(true);
                }
            }

            function validaCidade($cidade)
            {
                $exp = '/^[A-z \ç]+$/';
                
                if(!preg_match($exp, $cidade))
                {
                    return(false);
                }
                else
                {
                    return(true);
                }
            }
            function validaPais($pais)
            {
                $exp = '/^[A-z ]+$/';
                if(!preg_match($exp, $pais))
                {
                    return(false);
                }
                else
                {
                    return(true);
                }
            }

            function validaNomeProd($nome, $min, $max)
            {
                $exp = '/^[A-z0-9\- ]{'.$min.",".$max.'}$/';

                if(!preg_match($exp, $nome))
                {
                    return(false);
                }
                else
                {
                    return(true);
                }
            }

            function validaImagemError($imagem){
                $imagem = $imagem['imagem']['error'];

                if($imagem > 0)
                {
                    return(false);
                }
                else
                {
                    return(true);
                }
            }

            function validaImagemForm($imagem)
            {
                $imgNome = $imagem['imagem']['name'];  
                $imgExt = explode('.',$imgNome);
                $imagem = strtolower(end($imgExt));
                $extensoes = array('jpg','jpeg','png');

                if(!in_array($imagem,$extensoes))
                {
                    return(false);
                }
                else{
                    return(true);
                }
            }

            function validaImagemTam($imagem)
            {
                $imagem = $imagem['imagem']['size'];
                if($imagem > 2097152)
                {
                    return(false);
                }
                else
                {
                    return(true);
                }
            }

            function validaAssunto($assunto,$min,$max)
            {
                $exp = '/^[A-z0-9\-\_ ]{'.$min.",".$max.'}$/';
                if(!preg_match($exp, $assunto))
                {
                    return(false);
                }
                else
                {
                    return(true);
                }
            }

?>