// Anti-tabnabbing for all external links
window.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('a[target="_blank"]').forEach(function(link) {
    link.setAttribute('rel', 'noopener noreferrer');
  });
});
