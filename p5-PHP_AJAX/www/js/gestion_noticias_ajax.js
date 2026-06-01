document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.getElementById('buscador-gestion-noticias');
    const campoBusqueda = document.getElementById('busqueda-gestion');
    const cuerpoTabla = document.getElementById('tabla-noticias-body');
    const mensaje = document.getElementById('mensaje-ajax-gestion');

    if (!formulario || !campoBusqueda || !cuerpoTabla) {
        return;
    }

    function escapar(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }

    function fechaEsp(fecha) {
        const partes = fecha.split('-');
        if (partes.length !== 3) {
            return fecha;
        }
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }

    function pintarTabla(noticias) {
        cuerpoTabla.innerHTML = '';

        if (noticias.length === 0) {
            cuerpoTabla.innerHTML = '<tr><td colspan="6">No hay noticias.</td></tr>';
            return;
        }

        noticias.forEach(function (noticia) {
            const fila = document.createElement('tr');
            fila.innerHTML =
                '<td><a href="noticia.php?id=' + noticia.id + '">' + escapar(noticia.titulo) + '</a></td>' +
                '<td>' + fechaEsp(noticia.fecha_publicacion) + '</td>' +
                '<td>' + escapar(noticia.tipo) + '</td>' +
                '<td>' + escapar(noticia.lugar) + '</td>' +
                '<td><input class="check-publicado" type="checkbox" data-id="' + noticia.id + '" ' + (noticia.publicado ? 'checked' : '') + ' /></td>' +
                '<td>' +
                    '<a href="editar_noticia.php?id=' + noticia.id + '">Editar</a> ' +
                    '<form method="post" action="borrar_noticia.php" onsubmit="return confirm(\'¿Borrar noticia?\');">' +
                        '<input type="hidden" name="id" value="' + noticia.id + '" />' +
                        '<button type="submit">Borrar</button>' +
                    '</form>' +
                '</td>';
            cuerpoTabla.appendChild(fila);
        });
    }

    async function buscarNoticias(event) {
        if (event) {
            event.preventDefault();
        }

        const q = campoBusqueda.value.trim();

        try {
            const respuesta = await fetch('ajax_buscar_gestion_noticias.php?q=' + encodeURIComponent(q));
            const datos = await respuesta.json();

            if (datos.ok) {
                pintarTabla(datos.noticias);
            }
        } catch (error) {
            if (mensaje) {
                mensaje.textContent = 'Error al buscar noticias.';
            }
        }
    }

    async function cambiarPublicado(checkbox) {
        const datos = new FormData();
        datos.append('id', checkbox.dataset.id);
        datos.append('publicado', checkbox.checked ? '1' : '0');

        try {
            const respuesta = await fetch('ajax_cambiar_publicado.php', {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();

            if (!resultado.ok) {
                checkbox.checked = !checkbox.checked;
                if (mensaje) {
                    mensaje.textContent = resultado.error || 'No se ha podido cambiar el estado.';
                }
                return;
            }

            if (mensaje) {
                mensaje.textContent = checkbox.checked ? 'Noticia publicada.' : 'Noticia ocultada.';
            }
        } catch (error) {
            checkbox.checked = !checkbox.checked;
            if (mensaje) {
                mensaje.textContent = 'Error al cambiar el estado.';
            }
        }
    }

    formulario.addEventListener('submit', buscarNoticias);
    campoBusqueda.addEventListener('input', buscarNoticias);

    cuerpoTabla.addEventListener('change', function (event) {
        if (event.target.classList.contains('check-publicado')) {
            cambiarPublicado(event.target);
        }
    });
});
