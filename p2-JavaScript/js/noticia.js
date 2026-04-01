const comentarios = [
    {
        autor: "María López",
        fecha: "10/03/2026 18:20",
        texto: "Gracias por avisar. En la zona de la Plaza seguimos con poca presión desde esta tarde."
    },
    {
        autor: "Antonio Ruiz",
        fecha: "10/03/2026 19:05",
        texto: "Sería útil indicar si habrá un punto de suministro alternativo para personas mayores."
    },
    {
        autor: "Lucía Fernández",
        fecha: "10/03/2026 20:11",
        texto: "En mi calle ya han colocado señalización. Ojalá se resuelva pronto para evitar más molestias."
    }
];

const localidadesCercanas = [
    "Iznalloz",
    "Deifontes",
    "Peligros",
    "Albolote",
    "Alfacar",
    "Víznar",
    "Güevéjar",
    "Pulianas",
    "Cogollos Vega",
    "Colomera"
];

const panelComentarios = document.getElementById("panel-comentarios");
const triggerComentarios = document.getElementById("trigger-comentarios");
const botonCerrarPanel = document.getElementById("cerrar-panel");
const botonMostrarFormulario = document.getElementById("mostrar-formulario");
const contenedorFormulario = document.getElementById("contenedor-formulario");
const botonCerrarFormulario = document.getElementById("cerrar-formulario");
const botonCancelarFormulario = document.getElementById("cancelar-formulario");
const contadorComentarios = document.getElementById("contador-comentarios");
const listaComentarios = document.getElementById("lista-comentarios");
const formularioComentario = document.getElementById("form-comentario");
const campoNombre = document.getElementById("nombre-comentario");
const campoEmail = document.getElementById("email-comentario");
const campoTexto = document.getElementById("texto-comentario");
const mensajePanel = document.getElementById("mensaje-panel");
const dialogoAlerta = document.getElementById("dialogo-alerta");
const dialogoTitulo = document.getElementById("dialogo-titulo");
const dialogoMensaje = document.getElementById("dialogo-mensaje");

const patronEmail = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const patronLocalidades = new RegExp(
    `(^|[^\\p{L}])(${localidadesCercanas.map(escaparExpresionRegular).join("|")})(?=[^\\p{L}]|$)`,
    "giu"
);

function escaparExpresionRegular(texto) {
    return texto.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

function abrirPanelComentarios() {
    panelComentarios.classList.add("abierto");
}

function cerrarPanelComentarios() {
    panelComentarios.classList.remove("abierto");
    ocultarFormularioComentario();
}

function mostrarFormularioComentario() {
    contenedorFormulario.classList.remove("oculto");
    limpiarMensajePanel();
    campoNombre.focus();
}

function ocultarFormularioComentario() {
    contenedorFormulario.classList.add("oculto");
    limpiarMensajePanel();
    formularioComentario.reset();
}

function abrirDialogo(titulo, mensaje) {
    dialogoTitulo.textContent = titulo;
    dialogoMensaje.textContent = mensaje;

    if (!dialogoAlerta.open) {
        dialogoAlerta.showModal();
    }
}

function mostrarMensajePanel(texto, tipo) {
    mensajePanel.textContent = texto;
    mensajePanel.classList.remove("mensaje_panel_error", "mensaje_panel_ok");

    if (tipo === "error") {
        mensajePanel.classList.add("mensaje_panel_error");
    } else if (tipo === "ok") {
        mensajePanel.classList.add("mensaje_panel_ok");
    }
}

function limpiarMensajePanel() {
    mensajePanel.textContent = "";
    mensajePanel.classList.remove("mensaje_panel_error", "mensaje_panel_ok");
}

function formatearFechaActual() {
    const ahora = new Date();
    return ahora.toLocaleString("es-ES", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });
}

function crearElementoComentario(comentario) {
    const articulo = document.createElement("article");
    articulo.className = "comentario";

    const cabecera = document.createElement("div");
    cabecera.className = "comentario_cabecera";

    const autor = document.createElement("strong");
    autor.textContent = comentario.autor;

    const fecha = document.createElement("span");
    fecha.className = "comentario_fecha";
    fecha.textContent = comentario.fecha;

    const texto = document.createElement("p");
    texto.className = "comentario_texto";
    texto.textContent = comentario.texto;

    cabecera.appendChild(autor);
    cabecera.appendChild(fecha);
    articulo.appendChild(cabecera);
    articulo.appendChild(texto);

    return articulo;
}

function renderizarComentarios() {
    contadorComentarios.textContent = comentarios.length;
    listaComentarios.innerHTML = "";

    comentarios.forEach((comentario) => {
        listaComentarios.appendChild(crearElementoComentario(comentario));
    });
}

function convertirLocalidadesAMayusculas(texto) {
    return texto.replace(patronLocalidades, (coincidencia, prefijo, localidad) => {
        return `${prefijo}${localidad.toUpperCase()}`;
    });
}

function gestionarEscrituraComentario(evento) {
    const posicionInicial = evento.target.selectionStart;
    const posicionFinal = evento.target.selectionEnd;
    const textoActual = evento.target.value;
    const textoTransformado = convertirLocalidadesAMayusculas(textoActual);

    if (textoActual !== textoTransformado) {
        evento.target.value = textoTransformado;
        evento.target.setSelectionRange(posicionInicial, posicionFinal);
    }
}

function enviarComentario(evento) {
    evento.preventDefault();

    const nombre = campoNombre.value.trim();
    const email = campoEmail.value.trim();
    const texto = campoTexto.value.trim();

    if (!nombre || !email || !texto) {
        const mensaje = "Debes rellenar nombre, e-mail y comentario antes de enviar.";
        mostrarMensajePanel(mensaje, "error");
        abrirDialogo("Campos obligatorios", mensaje);
        return;
    }

    if (!patronEmail.test(email)) {
        const mensaje = "El e-mail no tiene un formato válido. Revisa el campo antes de continuar.";
        mostrarMensajePanel(mensaje, "error");
        abrirDialogo("E-mail incorrecto", mensaje);
        return;
    }

    const nuevoComentario = {
        autor: nombre,
        fecha: formatearFechaActual(),
        texto: texto
    };

    comentarios.unshift(nuevoComentario);
    renderizarComentarios();
    mostrarMensajePanel("Comentario añadido correctamente.", "ok");
    abrirDialogo("Comentario enviado", "Tu comentario se ha añadido al principio de la lista.");
    formularioComentario.reset();
    ocultarFormularioComentario();
}

triggerComentarios.addEventListener("mouseenter", abrirPanelComentarios);
triggerComentarios.addEventListener("focus", abrirPanelComentarios);
panelComentarios.addEventListener("mouseenter", abrirPanelComentarios);
botonCerrarPanel.addEventListener("click", cerrarPanelComentarios);
botonMostrarFormulario.addEventListener("click", mostrarFormularioComentario);
botonCerrarFormulario.addEventListener("click", ocultarFormularioComentario);
botonCancelarFormulario.addEventListener("click", ocultarFormularioComentario);
formularioComentario.addEventListener("submit", enviarComentario);
campoTexto.addEventListener("input", gestionarEscrituraComentario);

document.addEventListener("keydown", (evento) => {
    if (evento.key === "Escape" && panelComentarios.classList.contains("abierto")) {
        cerrarPanelComentarios();
    }
});

renderizarComentarios();
