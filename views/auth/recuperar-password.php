<h1 class="nombre-pagina">Recuperar Password</h1>
<p class="descripcion-pagina">Coloca tu nuevo password a continuación.</p>

<?php include_once __DIR__ . '/../templates/alertas.php'; ?>
<?php if($error) return; ?>

<form class="formulario" method="POST" action="/recuperar">

    <div class="campo">
    <label for="password">Password</label>
    <input
        type="password"
        id="password"
        name="password"
        placeholder="Escribe tu nuevo Password"
    />
    </div>

    <div class="campo">
    <label for="coPassword">Vuelve a escribir tu password</label>
    <input
        type="password"
        id="coPassword"
        name="coPassword"
        placeholder="Confirma tu nuevo Password"
    />
    </div>

    <input type="submit" class="boton" value="Guardar">

</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta? Crear una</a>
</div>