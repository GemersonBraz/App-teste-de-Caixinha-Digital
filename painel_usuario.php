<?php
	include_once('../conexao.php');
	session_start();
	include_once('../verificar_autenticacao.php');
?>


<?php 

if($_SESSION['nivel_usuario'] != 'Tecnico' && $_SESSION['nivel_usuario'] != 'Administrador'){
	header('Location: ../login.php');
	exit();
}

 ?>


<!DOCTYPE html>
<html lang="pt-br">
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Biblioteca Digital GAerNavMan, a melhor Biblioteca Digital de Publicações Técnicas da Marinha do Brasil!!">
	<meta name="author" content="Marcio Veiga">
	<meta name="keywords" content="biblioteca digital, biblioteca, publicações, publicações técnicas, biblioteca online">
	<title>Painel do Usuário - GAerNavMan</title>

	<link rel="stylesheet" href="../bootstrap-4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<link rel="shortcut icon" href="../img/logo.ico" type="image/x-icon" />

	<link rel="stylesheet" href="../fontawesome/css/all.min.css">

	<link rel="stylesheet" href="../css/estilos-site.css">

	<link rel="stylesheet" href="../css/estilos-padrao.css">

	<link rel="stylesheet" href="../css/publicacoes.css">

	<link rel="stylesheet" href="../css/painel.css">

	<link rel="stylesheet" href="../css/cards.css">


	<script src="../js/jquery.min.js"></script>

	<script src="../js/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="../js/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="../js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</head>

<body id="page-top">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
	<div class="container">
		<a class="navbar-brand js-scroll-trigger" href="../index.php#page-top"  target="_blank"><img src="../img/logo-gam.png" class="img-logo"><span class="texto-logo"> Biblioteca Digital - GANM</span></a>

		<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" arial-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle Navigation">Menu <i class="fas fa-bars"></i>
		</button>

		<div class="collapse navbar-collapse" id="navbarResponsive">
		
<ul class="navbar-nav ml-auto nav-flex-icons">

			 <li class="nav-item dropdown mr-1">
		        <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-333" data-toggle="dropdown"
		          aria-haspopup="true" aria-expanded="false">
		          <i class="fas fa-user"></i>
		        </a>
		        <div class="dropdown-menu dropdown-menu-right dropdown-default lista"
		          aria-labelledby="navbarDropdownMenuLink-333">
		          <a class="dropdown-item" href="../logout.php">Sair</a>

		          <?php

		          if($_SESSION['nivel_usuario'] == 'Administrador'){

		          	?>

		          	<a class="dropdown-item" href="../painel-admin/painel_admin.php">Painel do Administrador</a>
		         	 <a class="dropdown-item" href="../painel-bibliotecario/painel_bibliotecario.php">Painel dos Bibliotecários</a>

		          	<?php
		        	  }

		           ?>
		          
		        </div>
		      </li>

		      	<?php 
      
     		 //RECUPERAR FOTO DO BANCO

    			$nip = $_SESSION['nip_usuario'];

 			$query = "select * from tecnicos where nip = '$nip' ";
  			$result = mysqli_query($conexao, $query);

 			while($res = mysqli_fetch_array($result)){
       		$nome = $res["nome"];
        	        $nip = $res["nip"];
        	        $projeto = $res["projeto"];
        	        $foto = $res["foto"];

		       ?>

				<li class="nav-item avatar mt-1 mr-2">
			        <a class="nav-link p-0" href="#">
			          <img src="../img/perfil/<?php echo $foto; ?>" class="rounded-circle z-depth-0"
			            alt="avatar image" width="35" height="35">
			        </a>

			   </li>
			   <li class="nav-item avatar mt-2">
			  	 <span class="text-muted"><a class=" nome_usuario" href="painel_usuario.php?acao=perfil&nip=<?php echo $_SESSION['nip_usuario']; ?>"><?php echo $nome; ?> - NIP: <?php echo $nip; ?> - Projeto: <?php echo $projeto; ?></a> </span>
		     	<li class="nav-item avatar">

		     <?php } ?>
 </ul>
 	</div>

	</div>

</nav>

<div class="container_admin">
	<div class="row">
		<div class="col-lg-2 col-md-3 col-sm-12">

<!-- SIDEBAR-->
            <div class="bg-light" id="sidebar-wrapper">

                <div class="list-group list-group-flush lista">

                	<span href="#" class="list-group-item ativo">Painel do Usuário</span>

                	<a class="list-group-item list-group-item-action bg-light" href="painel_usuario.php?acao=home"><i class="fas fa-home mr-1"></i>Home</a>                	
                    <a class="list-group-item list-group-item-action bg-light" href="painel_usuario.php?acao=perfil&nip=<?php echo $_SESSION['nip_usuario']; ?>"><i class="fas fa-user-friends mr-1"></i>Editar Perfil</a>
                    <a class="list-group-item list-group-item-action bg-light" href="painel_usuario.php?acao=publicacoes"><i class="fas fa-book-reader mr-1"></i>Publicações / Manuais</a>
                    <a class="list-group-item list-group-item-action bg-light" href="painel_usuario.php?acao=projetos_publicacoes"><i class="fas fa-project-diagram mr-1"></i>Projetos e Publicações</a>
                    <a class="list-group-item list-group-item-action bg-light" href="#"><i class="fas fa-bars mr-1"></i>Categorias</a>
                    <a class="list-group-item list-group-item-action bg-light" href="#"><i class="fas fa-chart-line mr-1"></i>Desempenho</a>
                    <a class="list-group-item list-group-item-action bg-light" href="#"><i class="fas fa-sitemap mr-1"></i>Profile</a>
                    <a class="list-group-item list-group-item-action bg-light" href="#"><i class="fas fa-signal mr-1"></i>Status</a>
                </div>
            </div>

        </div>

	    <div class="col-lg-10 col-md-9 col-sm-12">    

	    	<!--CARREGAMENTO DAS DEMAIS PÁGINAS DO PAINEL-->
	    	<?php 
	    	if(@$_GET['acao'] == 'perfil'){
	    		include_once('perfil.php');	
	    	}elseif(@$_GET['acao'] == 'publicacoes' or isset($_GET['txtpesquisarPublicacoes'])){
	    		include_once('publicacoes.php');
	    	}elseif(@$_GET['acao'] == 'projetos_publicacoes'){
	    		include_once('projetos_publicacoes.php');
	    	}else{
	    		include_once('home.php');
	    	}

	    		

	    	 ?>
		
		</div>
	        
	</div> 

</div>           
