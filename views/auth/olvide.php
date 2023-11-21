<div class="contenedor olvide">

    <?php include_once __DIR__ . '/../templates/nombre-sitio.php' ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Restablece tu password</p>

        <?php include_once __DIR__ . '/../templates/alertas.php' ?>

        <form action="/olvide" method="POST" class="formulario">
            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="email"
                    id="emial"
                    placeholder="Tu Email"
                    name="email"

                />
            </div>
            <input type="submit" class="boton" value="Mandar Email">
        </form>
        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Crear Una</a>
            <a href="/">¿Ya tienes una Cuenta? Iniciar Sesión</a>
        </div>
    </div><!--.contenedor-sm-->
</div>