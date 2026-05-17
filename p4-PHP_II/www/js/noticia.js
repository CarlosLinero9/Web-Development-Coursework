const panel = document.getElementById('panel');
const trigger = document.getElementById('trigger');
const botonCerrar = document.getElementById('cerrar');
const botonNuevo = document.getElementById('nuevo');
const formulario = document.getElementById('formulario');
const campoTexto = document.getElementById('texto');
const botonCancelar = document.getElementById('cancelar');
const modalError = document.getElementById('modal-error');
const modalTitulo = document.getElementById('modal-titulo');
const modalMensaje = document.getElementById('modal-mensaje');
const cerrarModal = document.getElementById('cerrar-modal');
const fondoModal = document.querySelector('.modal-fondo');

if (panel && trigger) {
  let localidades = [];

  if (campoTexto) {
    try {
      const dataLocalidades = campoTexto.dataset.localidades || '[]';
      localidades = JSON.parse(dataLocalidades);
    } catch (error) {
      localidades = [];
    }
  }

  function mostrarModal(titulo, mensaje) {
    if (!modalError || !modalTitulo || !modalMensaje) {
      return;
    }
    modalTitulo.textContent = titulo;
    modalMensaje.textContent = mensaje;
    modalError.classList.remove('modal-oculto');
  }

  function ocultarModal() {
    if (modalError) {
      modalError.classList.add('modal-oculto');
    }
  }

  function abrirPanel() {
    panel.classList.add('abierto');
  }

  function cerrarPanel() {
    panel.classList.remove('abierto');
  }

  function abrirFormulario() {
    if (formulario && botonNuevo) {
      formulario.style.display = 'block';
      botonNuevo.style.display = 'none';
    }
  }

  function cerrarFormulario(limpiar = false) {
    if (formulario && botonNuevo) {
      formulario.style.display = 'none';
      botonNuevo.style.display = 'block';
    }

    if (limpiar && campoTexto) {
      campoTexto.value = '';
    }
  }

  if (cerrarModal) cerrarModal.addEventListener('click', ocultarModal);
  if (fondoModal) fondoModal.addEventListener('click', ocultarModal);

  trigger.addEventListener('mouseenter', abrirPanel);
  trigger.addEventListener('click', abrirPanel);
  panel.addEventListener('mouseenter', abrirPanel);
  if (botonCerrar) botonCerrar.addEventListener('click', cerrarPanel);

  if (botonNuevo) {
    botonNuevo.addEventListener('click', function () {
      abrirPanel();
      abrirFormulario();
    });
  }

  if (botonCancelar) {
    botonCancelar.addEventListener('click', function () {
      cerrarFormulario(true);
    });
  }

  document.addEventListener('keydown', function (evento) {
    if (evento.key === 'Escape') {
      cerrarPanel();
      ocultarModal();
      cerrarFormulario(false);
    }
  });

  if (campoTexto) {
    campoTexto.addEventListener('input', function () {
      let texto = campoTexto.value;

      localidades.forEach(function (localidad) {
        const regExp = new RegExp('\\b' + localidad.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b', 'giu');
        texto = texto.replace(regExp, localidad.toUpperCase());
      });

      campoTexto.value = texto;
    });
  }

  if (formulario && campoTexto) {
    formulario.addEventListener('submit', function (evento) {
      const texto = campoTexto.value.trim();

      if (!texto) {
        evento.preventDefault();
        abrirPanel();
        abrirFormulario();
        mostrarModal('Comentario vacío', 'Debes escribir un comentario antes de enviarlo.');
      }
    });
  }

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
