<?php require_once 'head.php'; ?>
<?php require_once 'db.php'; ?>
<<<<<<< HEAD
  <body>

    <!-- Navigation -->
    <header>
      <nav class="black navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
          <a class="navbar-brand" href="index.php"><b>Eenmaal</b> andermaal</a>
          <button class="navbar-toggler navbar-toggler-right collapsed" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
  			
                   <li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					  Alle veilingen
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
					<?= showMenuRubrieken(); ?>
					</div>
				  </li>
			 <?= showLoginMenu(); ?>
  			<li class="no_hover">
  				<form action="overview.php" method="get">
  					<input list="producten" name="search" placeholder="Uw product" maxlength="50" type="search">
  					<input value="zoeken" type="submit">
  				</form>
  			</li>
			</li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
=======
<body>
  <!-- Navigation -->
  <header>
    <nav class="black navbar navbar-expand-lg navbar-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="./index.php"><b>Eenmaal</b> andermaal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
>>>>>>> 531673006e4de34ccc865c9246f04be90795d13b

            <li class="nav-item">
              <a class="nav-link" href="#">Alle veilingen</a>
            </li>
            <?=showLoginMenu()?>
            <li class="no_hover">
              <form action="filmoverzicht.php" method="get">
               <input list="films" name="search" placeholder="Uw gewenste film" maxlength="50" type="search">
               <input value="zoeken" type="submit">
             </form>
           </li>
         </ul>
       </div>
     </div>
   </nav>
 </header>