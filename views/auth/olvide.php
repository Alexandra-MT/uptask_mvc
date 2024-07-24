<div class="contenedor olvide">
    
<?php include_once __DIR__.'/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recupera tu Acceso Uptask</p>

        <?php include_once __DIR__.'/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/olvide">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Tu Email">
            </div>
            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>
        <div class="acciones">
            <a href="/">¿Tienes una cuenta? Iniciar Sesión</a>
            <a href="/crear">¿Aún no tienes una cuenta? Crea una</a>
        </div>
    </div>
</div>