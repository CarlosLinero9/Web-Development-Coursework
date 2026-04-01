# Práctica 2 de JavaScript - guía rápida

## Qué pide la práctica

### Parte A
- Añadir en `noticia.html` un panel de comentarios oculto a la derecha.
- El panel debe abrirse al acercar el ratón al borde derecho.
- Debe mostrar 3 comentarios predefinidos.
- Debe existir un botón **Nuevo comentario**.
- Al pulsarlo aparece un formulario con:
  - nombre
  - e-mail
  - texto
  - botón enviar
- Al enviar correctamente, el comentario nuevo se añade **al principio** de la lista.
- El formulario desaparece después de enviar.
- El panel y el formulario deben poder cerrarse.

### Parte B
- Comprobar que no haya campos vacíos.
- Validar el e-mail con una expresión regular.
- Mostrar el aviso en un diálogo modal.
- Además se deja un mensaje visible dentro del panel.

### Parte C
- Mientras el usuario escribe en el textarea, si aparece una localidad cercana, se convierte automáticamente en mayúsculas.
- En esta solución se usan: Iznalloz, Deifontes, Peligros, Albolote, Alfacar, Víznar, Güevéjar, Pulianas, Cogollos Vega y Colomera.

## Archivos modificados
- `noticia.html`: añade el panel, el formulario, el diálogo modal y enlaza el script.
- `css/base.css`: estilos del panel, formulario, comentarios y diálogo.
- `js/noticia.js`: lógica completa en JavaScript.

## Cómo está organizado el JS

### 1. Datos iniciales
Al principio del archivo hay un array llamado `comentarios` con 3 comentarios precargados.

### 2. Selección del DOM
Se guardan en constantes los elementos importantes con `document.getElementById(...)`.

### 3. Funciones principales
- `abrirPanelComentarios()`: abre el panel.
- `cerrarPanelComentarios()`: cierra el panel y el formulario.
- `mostrarFormularioComentario()`: enseña el formulario.
- `ocultarFormularioComentario()`: lo esconde y limpia el formulario.
- `renderizarComentarios()`: recorre el array y pinta todos los comentarios en el DOM.
- `crearElementoComentario()`: crea cada comentario con `document.createElement(...)`.
- `enviarComentario()`: valida el formulario y mete el nuevo comentario al principio con `unshift(...)`.
- `convertirLocalidadesAMayusculas()`: detecta las localidades con una expresión regular y las pasa a mayúsculas.
- `gestionarEscrituraComentario()`: lanza esa conversión en el evento `input` del textarea.

### 4. Eventos usados
- `mouseenter` sobre la franja invisible de la derecha para abrir el panel.
- `click` en botones para abrir/cerrar panel y formulario.
- `submit` del formulario para validar y añadir el comentario.
- `input` del textarea para pasar localidades a mayúsculas mientras se escribe.
- `keydown` con Escape para cerrar el panel.

## Cómo defenderla en clase
Lo importante es saber explicar esto:
1. Los comentarios iniciales están en un array de objetos.
2. El DOM se modifica con `createElement`, `appendChild` e `innerHTML = ""` para volver a pintar la lista.
3. La validación de e-mail se hace con una expresión regular.
4. El nuevo comentario se mete al principio del array con `unshift`.
5. El textarea escucha el evento `input` para detectar localidades mientras escribes.
6. El texto del comentario se mete con `textContent`, que es más seguro que usar `innerHTML`.

## Entrega
Abre `principal.html` o directamente `noticia.html` en el navegador.
