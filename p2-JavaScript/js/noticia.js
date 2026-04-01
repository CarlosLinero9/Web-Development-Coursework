let comentarios = [
  {
    nombre: "María López García",
    texto: "Gracias por el aviso. En la zona de la Plaza llevamos toda la tarde con muy poca presión y ya empezaba a ser preocupante.",
    fecha: "10/03/2026 18:20"
  },
  {
    nombre: "Antonio Ruiz Martínez",
    texto: "Sería importante saber si se va a habilitar algún punto de suministro alternativo, sobre todo para personas mayores que no pueden desplazarse fácilmente.",
    fecha: "10/03/2026 19:05"
  },
  {
    nombre: "Lucía Fernández Sánchez",
    texto: "En mi calle ya han colocado señalización y hay operarios trabajando. Ojalá lo solucionen pronto porque está afectando a bastantes vecinos.",
    fecha: "10/03/2026 20:11"
  }
];

let pueblos = ["Iznalloz", "Deifontes", "Peligros", "Albolote", "Alfacar", "Víznar", "Pulianas", "Colomera"];

let panel = document.getElementById("panel");
let trigger = document.getElementById("trigger");
let lista = document.getElementById("lista");
let contador = document.getElementById("contador");
let botonCerrar = document.getElementById("cerrar");
let botonNuevo = document.getElementById("nuevo");
let formulario = document.getElementById("formulario");
let campoNombre = document.getElementById("nombre");
let campoEmail = document.getElementById("email");
let campoTexto = document.getElementById("texto");
let botonEnviar = document.getElementById("enviar");
let botonCancelar = document.getElementById("cancelar");

let modalError = document.getElementById("modal-error");
let modalTitulo = document.getElementById("modal-titulo");
let modalMensaje = document.getElementById("modal-mensaje");
let cerrarModal = document.getElementById("cerrar-modal");
let fondoModal = document.querySelector(".modal-fondo");

function mostrarModal(titulo, mensaje) {
  modalTitulo.textContent = titulo;
  modalMensaje.textContent = mensaje;
  modalError.classList.remove("modal-oculto");
}

function ocultarModal() {
  modalError.classList.add("modal-oculto");
}

cerrarModal.addEventListener("click", ocultarModal);
fondoModal.addEventListener("click", ocultarModal);

trigger.addEventListener("mouseenter", function () {
  panel.classList.add("abierto");
});

panel.addEventListener("mouseenter", function () {
  panel.classList.add("abierto");
});

botonCerrar.addEventListener("click", function () {
  panel.classList.remove("abierto");
});

document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    panel.classList.remove("abierto");
    ocultarModal();
    formulario.style.display = "none";
  }
});

function pintar() {
  lista.innerHTML = "";

  comentarios.forEach(function (comentario) {
    let div = document.createElement("div");

    let cabecera = document.createElement("div");
    cabecera.className = "comentario-cabecera";

    let nombre = document.createElement("strong");
    nombre.textContent = comentario.nombre;

    let fecha = document.createElement("strong");
    fecha.textContent = comentario.fecha;

    let texto = document.createElement("p");
    texto.className = "comentario-texto";
    texto.textContent = comentario.texto;

    cabecera.appendChild(nombre);
    cabecera.appendChild(fecha);

    div.appendChild(cabecera);
    div.appendChild(texto);

    lista.appendChild(div);
  });

  contador.textContent = comentarios.length;
}

pintar();

botonNuevo.addEventListener("click", function () {
  formulario.style.display = "block";
});

botonCancelar.addEventListener("click", function () {
  formulario.style.display = "none";
  campoNombre.value = "";
  campoEmail.value = "";
  campoTexto.value = "";
});

campoTexto.addEventListener("input", function () {
  let texto = campoTexto.value;

  pueblos.forEach(function (pueblo) {
    let reg = new RegExp(pueblo, "gi");
    texto = texto.replace(reg, pueblo.toUpperCase());
  });

  campoTexto.value = texto;
});

botonEnviar.addEventListener("click", function () {
  let nombre = campoNombre.value.trim();
  let email = campoEmail.value.trim();
  let texto = campoTexto.value.trim();

  if (!nombre || !email || !texto) {
    mostrarModal("Campos obligatorios", "Debes rellenar nombre, e-mail y comentario antes de enviarlo.");
    return;
  }

  let regex = /^[^@]+@[^@]+\.[a-z]{2,}$/i;
  if (!regex.test(email)) {
    mostrarModal("E-mail no válido", "Introduce un e-mail correcto antes de enviar el comentario.");
    return;
  }

  let fecha = new Date().toLocaleString("es-ES");

  comentarios.unshift({
    nombre: nombre,
    texto: texto,
    fecha: fecha
  });

  pintar();

  campoNombre.value = "";
  campoEmail.value = "";
  campoTexto.value = "";
  formulario.style.display = "none";
});