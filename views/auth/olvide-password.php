<h1 class="nombre-pagina">Olvide mi contraseña</h1>
<p class="descripcion-pagina">Ingresa tu Email para recuperar tu contraseña</p>

<?php include_once __DIR__ . "/../templates/alertas.php" ; ?>

<form action="/olvide" class="formulario" method="POST">   
    <div class="campo">
    <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Tu email">
    </div>  
    <input type="submit" class="boton" value="Recuperar Cuenta">
</form>
<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? inicia sesión</a>
    <a href="/crear-cuenta">¿Aun no tienes una cuenta? Crear una</a>
</div>