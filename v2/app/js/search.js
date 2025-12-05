// Live search + department/lab flow
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.querySelector('input[name="q"]');
  
  if (searchInput) {
    let timeout;
    searchInput.addEventListener('input', (e) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        if (e.target.value.length > 1) {
          window.location.href = `?q=${encodeURIComponent(e.target.value)}`;
        }
      }, 300);
    });
  }
});
