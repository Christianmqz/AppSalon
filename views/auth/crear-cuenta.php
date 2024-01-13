<h1 class="nombre-pagina">Crea tu cuenta</h1>
<p class="descripcion-pagina">Llena el siguiente formulario para crear una cuenta</p>

<?php include_once __DIR__ . "/../templates/alertas.php"; ?>

<form class="formulario" method="POST" action="/crear-cuenta">

    <div class="campo">
        <label for="nombre">Nombre</label>
        <input 
            type="text"
            id="nombre"
            name="nombre"
            placeholder="Tu Nombre"
            value="<?php echo sanitize($usuario->nombre); ?>"
        />
    </div>

    <div class="campo">
        <label for="apellido">Apellido</label>
        <input 
            type="text"
            id="apellido"
            name="apellido"
            placeholder="Tu Apellido"
            value="<?php echo sanitize($usuario->apellido); ?>"
        />
    </div>
    
    <div class="campo">
        <label for="telefono">Teléfono</label>
        <input 
            type="tel"
            id="telefono"
            name="telefono"
            placeholder="Tu Teléfono"
            value="<?php echo sanitize($usuario->telefono); ?>"
        />
    </div>

    <div class="campo">
        <label for="email">Email</label>
        <input 
            type="email"
            id="email"
            name="email"
            placeholder="Tu Email"
            value="<?php echo sanitize($usuario->email); ?>"
        />
    </div>

    <div class="campo">
        <label for="password">Password</label>
        <input 
            type="password"
            id="password"
            name="password"
            placeholder="Debe contener al menos 6 caracteres"
        />
    </div>

    <div class="campo">
        <label for="coPassword">Vuelve a escribir el password</label>
        <input 
            type="password"
            id="coPassword"
            name="coPassword"
            placeholder="Confirma tu password"
        />
    </div>

    <input type="submit" class="boton" value="Crear">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href='/olvide'>¿Olvidaste tu contraseña?</a>
</div>