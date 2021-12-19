<?php
    function mostrarProdutoCart()
    {
        require_once('cookies/configDb.php');

        $db = connectDB();

        if(is_string ($db)){
                
            echo("Fatal error! Please return later.");
            die();
        }

        $query ="SELECT produtos.*,categorias.nome AS categoria FROM produtos LEFT JOIN CatProd ON CatProd.id_produto=produtos.id_produto LEFT JOIN categorias ON CatProd.id_categoria=categorias.id_categoria ORDER BY categorias.nome";
        $result = mysqli_query($db, $query);

        if(!$result){     
            echo "Fatal Error! Return later";
            die();
        }

        $produtos = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $categorias = array_column($produtos,'categoria');
        $j=0;
            
        for($i=0;$i<sizeof($categorias);$i++)
        {
            if($i==0)
            {
                echo '<h3 class="text-center mt-3">'.$categorias[0].'</h3><br>';
                echo '<div class="containerCard">';
            }
            elseif($categorias[$i] != $categorias[$i-1])
            {
                echo '<h3 class="text-center mt-3">'.$categorias[$i].'</h3><br>';
                echo '<div class="containerCard">';
            }
            while($j<sizeof($produtos) && $produtos[$j]['categoria']==$categorias[$i])
            {
                if($produtos[$j]['stock'] > 0)
                {
                echo '<div class="card">
                        <img class="card-img-top" src="'.$produtos[$j]['imagem'].'" alt="Imagem do produto">
                        <div class="card-body">
                            <h4 class="card-title">'.$produtos[$j]['nome'].'</h4>
                            <p class="card-text">'.$produtos[$j]['descricao']." ".$produtos[$j]['preco'].'€</p>
                            <form action="carrinho.php" method="POST">
                                <input type="number" min="1" max="'.$produtos[$j]['stock'].'" step="1" name="quantidade" value="quantidade" placeholder="Quantidade">
                                <br>
                                <input type="hidden" name="id_produto" value="'.$produtos[$j]['id_produto'].'">
                                <input type="submit" name="addcart" value="Adicionar ao Carrinho">
                            </form>
                        </div>
                    </div>';
                }
                $j++;
            }
            
            echo '</div>'; 
        }
    }

    function mostrarCarrinho($dados)
    { 
        require_once('cookies/configDb.php');

        $j = 0;
        foreach($dados as $key => $value)
        {
            $id[$j] = (trim($key));
            $quantidade[$j] = (trim($value));
            $j++;
        }

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
            $precFinal = 0;

            echo'<table class="table table-striped align-middle">
            <thead>
              <tr>
                <th scope="col"></th>
                <th scope="col">Nome</th>
                <th scope="col">Quantidade</th>
                <th scope="col">Preço</th>
                <th scope="col">Subtotal</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>';
            for($i=0;$i<sizeof($produtos);$i++)
            {
                $j = $i+1; 
                $precFinal = $precFinal + $produtos[$i]['preco']*$quantidade[$i];
                echo'<tr class="align-items-center">
                <td>'.$j.'</td>
                <td>'.$produtos[$i]['nome'].'</td>
                <td> 
                    <form action="altCarrinho.php" method="POST">
                        <input type="number" min="1" max="'.$produtos[$i]['stock'].'" step="1" name="UpQuantidade" value="quantidade" placeholder="'.$quantidade[$i].'">
                        <input type="hidden" name="id_produto" value="'.$produtos[$i]['id_produto'].'">
                        <input type="submit" value="Alterar" name="alterarQuantidade">
                    </form>
                </td>
                <td>'.$produtos[$i]['preco'].'€</td>
                <td>'.$produtos[$i]['preco']*$quantidade[$i].'€</td>
                <td>
                    <form action= "altCarrinho.php" method="POST">
                        <input type="hidden" name="id_produto" value="'.$produtos[$i]['id_produto'].'">
                        <input type="submit" value="Apagar" name="apagarDoCarrinho">
                    </form>
                </td>
              </tr>';
            }

            echo '</tbody><tfooter><td>Total:</td><td></td><td></td><td></td><th>'.$precFinal.'€</th><td></td></tfooter></table>';
            $result = closeDb($db);
        }	
    }

    function sectionCategorias()
    {
        require_once('cookies/configDb.php');
        $db = connectDB();
            
        if(is_string($db))
        {
            echo("Fatal error, please return later.");
            die();
        }

        $query = "SELECT * FROM categorias";
        $result = mysqli_query($db, $query);
            
        if(!$result)
        {
            echo "Fatal error. Return later.";
            die();
        }

        while($row = mysqli_fetch_assoc($result))
        {
            echo '<option value = "'.$row['nome'].'">'.$row['nome'].'</option>';
        }

        $result = closeDb($db);
    }

    function mostrarCategorias()
    {
        require_once('cookies/configDb.php');
        $db = connectDB();
            
        if(is_string($db))
        {
            echo("Fatal error, please return later.");
            die();
        }

        $query = "SELECT * FROM categorias";
        $result = mysqli_query($db, $query);
            
        if(!$result)
        {
            echo "Fatal error. Return later.";
            die();
        }

        echo '<form action="" method="POST">
        <select class="form-select" name="id">';

        while($row = mysqli_fetch_assoc($result))
        {
            echo'
                <option value="'.$row['id_categoria'].'"name="id">'.$row['nome'].'</option>';
        }
        echo '
        </select>
        <input type="text" name="nome" id="nome" class="form-control" placeholder="Novo nome">
        <input type="submit" value="Alterar" name="Alterar">
        </form>';

        $result = closeDb($db);
    }

    function mostrarProdutoAdmin()
    {
        require_once('cookies/configDb.php');
        $db = connectDB();

        if(is_string ($db)){
                
            echo("Fatal error! Please return later.");
            die();
        }

        $query ="SELECT produtos.*,categorias.nome AS categoria FROM produtos LEFT JOIN CatProd ON CatProd.id_produto=produtos.id_produto LEFT JOIN categorias ON CatProd.id_categoria=categorias.id_categoria ORDER BY categorias.nome";
        $result = mysqli_query($db, $query);

        if(!$result){     
            echo "Fatal Error! Return later";
            die();
        }

        $produtos = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $categorias = array_column($produtos,'categoria');
        $j=0;
        
        echo '<div class="ProdContainer text-center">
            <hr><a id="editarProd"><h2 class="text-center">Alterar Produtos</h2></a>
            <form action="" method="POST" class="MostrarPord">
              <select class="form-select" name="categoriaProd">';

        for($i=0;$i<sizeof($categorias);$i++)
        {
            if($i==0)
            {
                echo '<option value="'.$categorias[0].'">'.$categorias[0].'</option>';
                
            }
            elseif($categorias[$i] != $categorias[$i-1])
            {
                echo '<option value="'.$categorias[$i].'">'.$categorias[$i].'</option>';
            }
        }
        echo '</select><input type="submit" value="Escolher" name="Escolher"></form>';

        if(!empty($_POST) && array_key_exists('Escolher',$_POST))
        {
            echo '<div class="containerCard">';
            while($j<sizeof($produtos))
            { 
                if($produtos[$j]['categoria']==$_POST['categoriaProd'])
                {
                    echo'<div class="card">
                            <div class="card-body">
                                <form action ="adminer.php" method="POST">
                                <input type="text" name="nome" class="form-control" value="'.$produtos[$j]['nome'].'">';
                            echo'<input type="hidden" name="id" value="'.$produtos[$j]['id_produto'].'">
                                <input type="submit" name="AltNome" value="Alterar Nome">
                                </form>';
                                echo '<form action ="adminer.php" method="POST">
                                <textarea name="descricao" class="form-control" rows="4" cols="20">'.$produtos[$j]['descricao'].'</textarea>';
                            echo'<input type="hidden" name="id" value="'.$produtos[$j]['id_produto'].'">
                                <input type="submit" name="AltDesc" value="Alterar Descrição">
                                </form>';
                                echo '<form action ="adminer.php" method="POST">
                                <input type="number" name="stock" class="form-control" value="'.$produtos[$j]['stock'].'">';
                            echo'<input type="hidden" name="id" value="'.$produtos[$j]['id_produto'].'">
                                <input type="submit" name="AltStock" value="Alterar Stock">
                                </form>';
                                echo '<form action="adminer.php" method="POST">
                                <input type="text" name="preco" class="form-control" value="'.$produtos[$j]['preco'].'">';
                            echo'<input type="hidden" name="id" value="'.$produtos[$j]['id_produto'].'">
                                <input type="submit" name="AltPreco" value="Alterar">
                                </form>';
                                echo'<form action="adminer.php" method="POST" enctype="multipart/form-data">
                                <input type="file" name="imagem" class="form-control" id="imagem"><br> <br>
                                <input type="hidden" name="id" value="'.$produtos[$j]['id_produto'].'">
                                <input type="hidden" name="imagemAntiga" value="'.$produtos[$j]['imagem'].'">
                                <input type="submit" name="AltImg" value="Alterar Imagem">
                            </form>
                        </div>
                    </div>';
                }
                $j++;
            }
            echo '</div>';
        }
    }

    function apagarComentario(){
        require_once('cookies/configDb.php');

        $db = connectDB();

        if(is_string ($db)){  
            echo("Fatal error! Please return later.");
            die();
        }

        $query ="SELECT comentarios.*,Clientes.username AS cliente FROM Clientes LEFT JOIN comentarios ON Clientes.id_cliente=comentarios.id_clientes WHERE comentario IS NOT NULL";
        $result = mysqli_query($db, $query);

        if(!$result){     
            echo "Fatal Error! Return later";
            die();
        }

        $comentario = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $result = closeDb($db);
        echo '<hr><a id="comentariosAdmin"><h2>Comentários</h2></a>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Comentário</th>
                    <th>Avalição</th>
                    <th></th>
                </tr></thead><tbody>';
                for($i=0;$i<sizeof($comentario);$i++)
                {
                    echo '<tr><td>'.$comentario[$i]['cliente'].'</td>';
                    echo '<td>'.$comentario[$i]['comentario'].'</td>';
                    echo '<td>'.$comentario[$i]['avaliacao'].'</td>';
                    echo '<td><form action="adminer.php" method="POST">
                        <input type="hidden" name="id_comentario" value="'.$comentario[$i]['id_comentario'].'">
                        <input type="submit" name="apagarCom" value="Apagar"> 
                    </form></td></tr>';
                }
        echo '</tbody></table>';

    }

    function mostrarEnc(){
        require_once('cookies/configDb.php');
        $db = connectDB();
            
        if(is_string($db))
        {
            echo("Fatal error, please return later.");
            die();
        }

        $query = "SELECT encomendas.*,EncProd.quantidade,produtos.nome AS produtos FROM encomendas
        LEFT JOIN EncProd ON EncProd.id_encomendas=encomendas.id_encomenda 
        LEFT JOIN produtos ON EncProd.id_produtos=produtos.id_produto WHERE produtos.nome IS NOT NULL ORDER BY encomendas.id_encomenda";
        $result = mysqli_query($db, $query);
            
        if(!$result)
        {
            echo "Fatal error. Return later.";
            die();
        }

        $encomendas = mysqli_fetch_all($result,MYSQLI_ASSOC);

        echo'<hr><a id="encomendasAdmin"><h2 class="text-center">Encomendas</h2></a>
            <table class="table table-striped">';
        echo'<thead><tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Encomenda</th>
                <th>Cliente</th>
                <th>Valor</th>
                <th>Morada</th>
                <th>Data</th>
                <th>Estado</th>
                <th></th>
            </tr></thead><tbody>';
            for($i=0;$i<sizeof($encomendas);$i++)
            {
                if($i==0)
                {
                    echo'<tr>
                        <td>'.$encomendas[$i]['produtos'].'</td>
                        <td>'.$encomendas[$i]['quantidade'].'</td>
                        <td>'.$encomendas[$i]['id_encomenda'].'</td>
                        <td>'.$encomendas[$i]['nomeCliente'].'</td>
                        <td>'.$encomendas[$i]['valor'].'</td>
                        <td>'.$encomendas[$i]['morada'].' '.$encomendas[$i]['codigoPostal'].','.$encomendas[$i]['cidade'].'</td>
                        <td>'.$encomendas[$i]['data'].'</td>
                        <td>'.$encomendas[$i]['estado'].'</td>';
                        if($encomendas[$i]['estado'] != "Paga")
                        {
                            echo'<td><form action="adminer.php" method="POST">
                                <input type="hidden" value="'.$encomendas[$i]['id_encomenda'].'" name="id">
                                <input type="submit" value="Finalizar" name="finalizar">
                            </form><td>';
                            }
                        else{
                            echo '<td></td>';
                        }
                    echo '</tr>';
                }
                elseif($encomendas[$i]['id_encomenda'] != $encomendas[$i-1]['id_encomenda'])
                {
                    echo'<tr>
                        <td>'.$encomendas[$i]['produtos'].'</td>
                        <td>'.$encomendas[$i]['quantidade'].'</td>
                        <td>'.$encomendas[$i]['id_encomenda'].'</td>
                        <td>'.$encomendas[$i]['nomeCliente'].'</td>
                        <td>'.$encomendas[$i]['valor'].'</th>
                        <td>'.$encomendas[$i]['morada'].' '.$encomendas[$i]['codigoPostal'].','.$encomendas[$i]['cidade'].'</td>
                        <td>'.$encomendas[$i]['data'].'</td>
                        <td>'.$encomendas[$i]['estado'].'</td>';
                        if($encomendas[$i]['estado'] != "Paga")
                        {
                            echo'<td><form action="adminer.php" method="POST">
                                <input type="hidden" value="'.$encomendas[$i]['id_encomenda'].'" name="id">
                                <input type="submit" value="Finalizar" name="finalizar">
                            </form><td>';
                            }
                            else{
                                echo '<td></td>';
                            }
                    echo '</tr>';
                }
                elseif($encomendas[$i]['id_encomenda'] == $encomendas[$i-1]['id_encomenda'] && $encomendas[$i]['produtos'] != $encomendas[$i-1]['produtos']){
                    echo'<td>'.$encomendas[$i]['produtos'].'</td>
                        <td>'.$encomendas[$i]['quantidade'].'</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
                }
            }
        echo '</tbody></table>';
    }

    function listarProdCliente($id){

        require_once('cookies/configDb.php');

        $db=connectDb();
            
        if(is_string($db))
        {
            echo("Fatal error, please return later.");
            die();
        }

        $query="SELECT encomendas.*,EncProd.quantidade,produtos.nome AS produtos FROM encomendas
        LEFT JOIN EncProd ON EncProd.id_encomendas=encomendas.id_encomenda 
        LEFT JOIN produtos ON EncProd.id_produtos=produtos.id_produto WHERE encomendas.id_cliente=? ORDER BY encomendas.id_encomenda";

        //prepare the statement				
		$statement = mysqli_prepare($db,$query);
				
		if (!$statement ){
			//error preparing the statement. This should be regarded as a fatal error.
			echo "Something went wrong. Please try again later.";
			die();				
		}				
								
		//now bind the parameters by order of appearance
		$result = mysqli_stmt_bind_param($statement,'i',$id); # 'ss' means that both parameters are expected to be strings.
								
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
            $encomendas = mysqli_fetch_all($result, MYSQLI_ASSOC);

            echo'<hr><h2 class="text-center">Encomendas</h2>
            <table class="table table-striped">';
            echo'<thead><tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Encomenda</th>
            <th>Valor</th>
            <th>Morada</th>
            <th>Data</th>
            <th>Estado</th>
            <th>Referência</th>
            </tr></thead><tbody>';
            $j=0;
            for($i=0;$i<sizeof($encomendas);$i++)
            {
                $j++;
                if($i==0)
                {
                    echo'<tr>
                        <td>'.$encomendas[$i]['produtos'].'</td>
                        <td>'.$encomendas[$i]['quantidade'].'</td>
                        <td>'.$j.'</td>
                        <td>'.$encomendas[$i]['valor'].'€</td>
                        <td>'.$encomendas[$i]['morada'].' '.$encomendas[$i]['codigoPostal'].','.$encomendas[$i]['cidade'].'</td>
                        <td>'.$encomendas[$i]['data'].'</td>
                        <td>'.$encomendas[$i]['estado'].'</td>';
                        if($encomendas[$i]['estado'] != 'Paga')
                        {
                            echo '<td>'.$encomendas[$i]['referencia'].'</td>';
                        }
                        else
                        {
                            echo '<td>----------------</td>';
                        }
                    echo '</tr>';
                }
                elseif($encomendas[$i]['id_encomenda'] != $encomendas[$i-1]['id_encomenda'])
                {
                    echo'<tr>
                        <td>'.$encomendas[$i]['produtos'].'</td>
                        <td>'.$encomendas[$i]['quantidade'].'</td>
                        <td>'.$encomendas[$i]['id_encomenda'].'</td>
                        <td>'.$encomendas[$i]['valor'].'€</th>
                        <td>'.$encomendas[$i]['morada'].' '.$encomendas[$i]['codigoPostal'].','.$encomendas[$i]['cidade'].'</td>
                        <td>'.$encomendas[$i]['data'].'</td>
                        <td>'.$encomendas[$i]['estado'].'</td>';
                        if($encomendas[$i]['estado'] != 'Paga')
                        {
                            echo '<td>'.$encomendas[$i]['referencia'].'</td>';
                        }
                        else
                        {
                            echo '<td>----------------</td>';
                        }
                    echo '</tr>';
                }
                elseif($encomendas[$i]['id_encomenda'] == $encomendas[$i-1]['id_encomenda'] && $encomendas[$i]['produtos'] != $encomendas[$i-1]['produtos']){
                    echo'<td>'.$encomendas[$i]['produtos'].'</td>
                        <td>'.$encomendas[$i]['quantidade'].'</td><td></td><td></td><td></td><td></td><td></td><td></td>';
                }
            }
            echo '</tbody></table>';
        }
    }
?>