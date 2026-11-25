// Confirmation avant suppression (compatible avec la CSP : pas de JS inline)
document.querySelectorAll('form[data-confirm]').forEach(function (form) {
  form.addEventListener('submit', function (ev) {
    if (!window.confirm(form.dataset.confirm)) { ev.preventDefault(); }
  });
});
