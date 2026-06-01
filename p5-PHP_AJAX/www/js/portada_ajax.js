document.addEventListener('DOMContentLoaded', function () {
    const buscador = document.getElementById('buscador-portada');
    const resultados = document.getElementById('resultados-portada');

    if (!buscador || !resultados) {
        return;
    }

    async function buscarNoticias() {
        const texto = buscador.value.trim();

        if (texto.length < 2) {
            resultados.innerHTML = '';
            resultados.classList.remove('visible');
            return;
        }

        try {
            const respuesta = await fetch('ajax_buscar_portada.php?q=' + encodeURIComponent(texto));
            const datos = await respuesta.json();

            resultados.innerHTML = '';

            if (!datos.ok || datos.noticias.length === 0) {
                resultados.innerHTML = '<p>No hay resultados.</p>';
                resultados.classList.add('visible');
                return;
            }

            datos.noticias.forEach(function (noticia) {
                const enlace = document.createElement('a');
                enlace.href = 'noticia.php?id=' + noticia.id;
                enlace.textContent = noticia.titulo;
                resultados.appendChild(enlace);
            });

            resultados.classList.add('visible');
        } catch (error) {
            resultados.innerHTML = '<p>Error al buscar.</p>';
            resultados.classList.add('visible');
        }
    }

    buscador.addEventListener('input', buscarNoticias);
});
