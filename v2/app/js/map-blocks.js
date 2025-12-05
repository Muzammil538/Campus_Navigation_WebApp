// app/js/map-blocks.js - Dynamic route highlighting
document.addEventListener('DOMContentLoaded', () => {
  const blockMap = document.getElementById('blockMap');
  if (!blockMap) return;

  // Demo data: block positions and types
  const campusLayout = {
    gate: { row: 0, col: 0, color: 'slate-500', label: 'Gate' },
    cse: { row: 1, col: 0, color: 'blue-100', label: 'CSE', deptId: 1 },
    ece: { row: 1, col: 1, color: 'emerald-100', label: 'ECE', deptId: 2 },
    library: { row: 1, col: 2, color: 'orange-100', label: 'Lib' },
    lab1: { row: 2, col: 1, color: 'sky-100', label: 'Lab A', labId: 1 },
    admin: { row: 3, col: 0, color: 'lime-100', label: 'Admin' },
    hostel: { row: 3, col: 2, color: 'rose-100', label: 'Hostel' }
  };

  // Parse URL params for route highlighting
  const urlParams = new URLSearchParams(window.location.search);
  const deptId = urlParams.get('dept_id');
  const labId = urlParams.get('lab_id');

  if (deptId || labId) {
    highlightRoute(deptId, labId);
  }

  function highlightRoute(deptId, labId) {
    // Highlight gate → department → lab
    setTimeout(() => {
      // Gate (start)
      highlightBlock('gate', 'ring-2 ring-blue-400 bg-blue-200');
      
      if (deptId) {
        // Find and highlight department
        Object.entries(campusLayout).forEach(([key, block]) => {
          if (block.deptId == deptId) {
            setTimeout(() => highlightBlock(key, 'ring-2 ring-emerald-400 bg-emerald-200 scale-110'), 500);
          }
        });
      }
      
      if (labId) {
        // Highlight lab (destination)
        Object.entries(campusLayout).forEach(([key, block]) => {
          if (block.labId == labId) {
            setTimeout(() => highlightBlock(key, 'ring-2 ring-orange-400 bg-orange-200 scale-110 shadow-lg'), 1000);
          }
        });
      }
    }, 300);
  }

  function highlightBlock(blockKey, classes) {
    const blocks = blockMap.querySelectorAll('[data-block]');
    blocks.forEach(block => {
      if (block.dataset.block === blockKey) {
        block.className = block.className.replace(/bg-\w+-\d+|ring-\w+|scale-\d+|shadow-\w+/g, '') + ' ' + classes;
        block.style.animation = 'pulse 1s infinite';
      }
    });
  }
});
