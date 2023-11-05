<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">   
     <link rel="stylesheet" href="Estilos/reservar.css">
    
</head>
<body>
    <header>    
        <div class="cabecera">
            <h2>Realizar pedido</h2>&nbsp;
 
        </div>
     </header>
<!-- 
    <form action="../Controllers/compradoresController.php" method="post">
        <div class="contenedor"> 
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
             </div>
             <div class="mb-3">
                <label for="Telefono" class="form-label">Telefono</label>
                <input type="text" class="form-control" id="Telefono" name="Telefono" required>
             </div>
            <div class="mb-3">
                <label for="dni" class="form-label">DNI</label>
                <input type="text" class="form-control" id="dni" name="dni" required>
            </div>
            <div class="mb-3">
                <label for="dni" class="form-label">DNI actor</label>
                <input type="text" class="form-control" id="dni_actor" name="dni_actor" required>
            </div>
            <div class="mb-3">
                <label for="cantidad_entradas" class="form-label">Cantidad de Entradas</label>
                <input type="number" class="form-control" id="cantidad_entradas" name="cantidad_entradas" required>
            </div>
            <input type="hidden" name="pk_eventos" value="<?php echo $_GET['pk_eventos']; ?>">
            <button type="submit" class="btn btn-primary">Comprar</button>
        </div>
    </form>
    <br> -->

    
    <form action="../Controllers/compradoresController.php" method="post">
        <div class="contenedor"> 
        <div class="mb-3">
                <label for="nombrepaellido" class="form-label">Nombre y Apellido</label>
                <input type="string" class="form-control" id="nombrepaellido" name="nombrepaellido" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="Cantidad" class="form-label">Cantidad</label>
                <input type="number" class="form-control" id="Cantidad" name="Cantidad" required>
            </div>
            <input type="hidden" name="pk_eventos" value="<?php echo $_GET['pk_articulos']; ?>">
            <button type="submit" class="btn btn-primary">Reservar</button>
        </div>
    </form>
    <br>
    
  
    

 
     
</body>
</html>