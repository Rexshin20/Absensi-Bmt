(function () {
  'use strict';

  function firstField(modal) {
    return modal.querySelector(
        'input:not([type=hidden]):not([type=checkbox]):not([type=date]):not([type=search]), select, textarea'
    );
  }

  function open(modal) {
    modal.classList.add('is-open');
    document.body.classList.add('modal-open');

    var field = firstField(modal);
    if (field) field.focus();

    modal.dispatchEvent(new CustomEvent('modal:opened'));
  }

  function close(modal) {
    modal.classList.remove('is-open');

    if (!document.querySelector('.modal-root.is-open')) {
      document.body.classList.remove('modal-open');
    }

    if (window.location.search) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-modal-open]').forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        var modal = document.getElementById(trigger.getAttribute('data-modal-open'));
        if (modal) open(modal);
      });
    });

    document.querySelectorAll('.modal-root').forEach(function (modal) {
      modal.querySelectorAll('[data-modal-close]').forEach(function (button) {
        button.addEventListener('click', function () {
          close(modal);
        });
      });

      if (modal.dataset.open === '1') open(modal);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape') return;

      var modal = document.querySelector('.modal-root.is-open');
      if (modal) close(modal);
    });
  });
})();
