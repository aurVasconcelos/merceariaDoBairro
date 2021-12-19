
<?php
        if(!empty($_POST))
        {
            require_once('cookies/valida.php');
            $errors = pesquisar($_POST);

            if(!is_array($errors) && !is_string($errors))
            {
                require_once('cookies/configDb.php');
                $db = connectDb();

                if (is_string($db))
                {
                    echo("Fatal error! Please return later.");
                    die();
                }

                $nome = trim($_POST['pesquisa']);
                //construct the intend query
                $query = "SELECT * FROM produtos WHERE nome=?";

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
                elseif( mysqli_num_rows($result) == 1){
                    
                    $produto = mysqli_fetch_assoc($result);
                    $result = closeDb($db);
                    echo '
                    <section class="mostrarProd">
                        <table class="table table-striped align-middle">
                        <tbody><tr>
                            <td>'.$produto['nome'].'</td>
                            <td>'.$produto['descricao'].'</td>
                            <td>Preço:'.$produto['preco'].'€</td>
                            <td><form action="carrinho.php" method="POST">
                            <input type="number" min="1" max="'.$produto['stock'].'" step="1" name="quantidade" value="quantidade" placeholder="Quantidade">
                            <br>
                            <input type="hidden" name="id_produto" value="'.$produto['id_produto'].'">
                            <input type="submit" name="addcart" value="Adicionar ao Carrinho">
                        </form></td>
                        </tr></tbody></table>
                    </section>';
                }
                elseif(mysqli_num_rows($result) == 0){
                    $existe[0] = true;
                    $existe[1] = 'Produto não encontrado, tente outro nome, por favor.';
                    return($existe);
                }
                else{
                    echo "Fatal error, please return later.";
                    die();
                }
            }
        }
?>