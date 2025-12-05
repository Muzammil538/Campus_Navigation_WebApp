// Global app utilities
document.addEventListener('DOMContentLoaded', () => {
  // Back button handler
  document.querySelectorAll('.back-btn, [onclick*="history.back"]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      history.back();
    });
  });

  // Toggle handlers
  document.querySelectorAll('input[type="checkbox"]').forEach(toggle => {
    toggle.addEventListener('change', function() {
      this.parentNode.classList.toggle('bg-brandSoft', this.checked);
    });
  });
});
