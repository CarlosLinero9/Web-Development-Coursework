const panel = document.getElementById('panel');
const trigger = document.getElementById('trigger');
const botonCerrar = document.getElementById('cerrar');
const botonNuevo = document.getElementById('nuevo');
const formulario = document.getElementById('formulario');
const campoNombre = document.getElementById('nombre');
const campoEmail = document.getElementById('email');
const campoTexto = document.getElementById('texto');
const botonCancelar = document.getElementById('cancelar');
const modalError = document.getElementById('modal-error');
const modalTitulo = document.getElementById('modal-titulo');
const modalMensaje = document.getElementById('modal-mensaje');
const cerrarModal = document.getElementById('cerrar-modal');
const fondoModal = document.querySelector('.modal-fondo');

if (panel && trigger && formulario && campoTexto) {
  let localidades = [];

  try {
    const dataLocalidades = campoTexto.dataset.localidades || '[]';
    localidades = JSON.parse(dataLocalidades);
  } catch (error) {
    localidades = [];
  }

  function mostrarModal(titulo, mensaje) {
    modalTitulo.textContent = titulo;
    modalMensaje.textContent = mensaje;
    modalError.classList.remove('modal-oculto');
  }

  function ocultarModal() {
    modalError.classList.add('modal-oculto');
  }

  function abrirPanel() {
    panel.classList.add('abierto');
  }

  function cerrarPanel() {
    panel.classList.remove('abierto');
  }

  function abrirFormulario() {
    formulario.style.display = 'block';
    botonNuevo.style.display = 'none';
  }

  function cerrarFormulario(limpiar = false) {
    formulario.style.display = 'none';
    botonNuevo.style.display = 'block';

    if (limpiar) {
      campoNombre.value = '';
      campoEmail.value = '';
      campoTexto.value = '';
    }
  }

  cerrarModal.addEventListener('click', ocultarModal);
  fondoModal.addEventListener('click', ocultarModal);

  trigger.addEventListener('mouseenter', abrirPanel);
  trigger.addEventListener('click', abrirPanel);
  panel.addEventListener('mouseenter', abrirPanel);
  botonCerrar.addEventListener('click', cerrarPanel);

  botonNuevo.addEventListener('click', function () {
    abrirPanel();
    abrirFormulario();
  });

  botonCancelar.addEventListener('click', function () {
    cerrarFormulario(true);
  });

  document.addEventListener('keydown', function (evento) {
    if (evento.key === 'Escape') {
      cerrarPanel();
      ocultarModal();
      cerrarFormulario(false);
    }
  });

  campoTexto.addEventListener('input', function () {
    let texto = campoTexto.value;

    localidades.forEach(function (localidad) {
      const regExp = new RegExp('\\b' + localidad.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b', 'giu');
      texto = texto.replace(regExp, localidad.toUpperCase());
    });

    campoTexto.value = texto;
  });

  formulario.addEventListener('submit', function (evento) {
    const nombre = campoNombre.value.trim();
    const email = campoEmail.value.trim();
    const texto = campoTexto.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i;

    if (!nombre || !email || !texto) {
      evento.preventDefault();
      abrirPanel();
      abrirFormulario();
      mostrarModal('Campos obligatorios', 'Debes rellenar nombre, e-mail y comentario antes de enviarlo.');
      return;
    }

    if (!emailRegex.test(email)) {
      evento.preventDefault();
      abrirPanel();
      abrirFormulario();
      mostrarModal('E-mail no válido', 'Introduce un e-mail correcto antes de enviar el comentario.');
    }
  });

  if (panel.dataset.openPanel === '1') {
    abrirPanel();
  }

  if (panel.dataset.openForm === '1') {
    abrirFormulario();
  }

  if (panel.dataset.modalTitle && panel.dataset.modalMessage) {
    mostrarModal(panel.dataset.modalTitle, panel.dataset.modalMessage);
  }
}
