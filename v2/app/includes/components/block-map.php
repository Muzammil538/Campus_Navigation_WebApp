<div id="blockMap" class="relative w-full aspect-[4/3] bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl border-2 border-slate-200 overflow-hidden p-3 shadow-inner">
  <div class="grid grid-cols-4 grid-rows-5 gap-1.5 h-full relative">
    <!-- Gate (top center) -->
    <div data-block="gate" class="row-start-1 col-start-3 bg-gradient-to-br from-slate-400 to-slate-500 rounded-lg flex items-center justify-center text-[9px] font-bold text-white shadow-lg border-2 border-white/50">MAIN GATE</div>
    
    <!-- Paths -->
    <div data-block="path1" class="row-start-2 col-start-2 row-span-1 col-span-2 bg-gradient-to-r from-blue-400/30 to-emerald-400/30 rounded-full flex items-center justify-center h-1 mx-auto my-1"></div>
    
    <!-- Departments -->
    <div data-block="cse" class="row-start-3 col-start-1 bg-gradient-to-br from-blue-400 to-blue-500 rounded-lg flex items-center justify-center text-[9px] font-bold text-white shadow-lg border-2 border-white/50">CSE</div>
    <div data-block="ece" class="row-start-3 col-start-3 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-lg flex items-center justify-center text-[9px] font-bold text-white shadow-lg border-2 border-white/50">ECE</div>
    
    <!-- Labs -->
    <div data-block="lab-cse101" class="row-start-4 col-start-1 bg-gradient-to-br from-sky-400 to-sky-500 rounded-lg flex items-center justify-center text-[9px] font-bold text-white shadow-lg border-2 border-white/50">CSE-101</div>
    <div data-block="lab-ece201" class="row-start-4 col-start-3 bg-gradient-to-br from-orange-400 to-orange-500 rounded-lg flex items-center justify-center text-[9px] font-bold text-white shadow-lg border-2 border-white/50">ECE-201</div>
    
    <!-- Legend -->
    <div class="row-start-5 col-span-4 flex items-center justify-center space-x-4 text-[10px] text-slate-500 font-medium">
      <span class="flex items-center gap-1"><div class="w-3 h-3 bg-blue-400 rounded-full"></div>Route</span>
      <span class="flex items-center gap-1"><div class="w-3 h-3 bg-slate-500 rounded-full"></div>You are here</span>
    </div>
  </div>
</div>
