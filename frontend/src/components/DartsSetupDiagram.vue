<template>
  <div class="setup-container bg-dark q-pa-xl rounded-borders shadow-2 relative-position overflow-hidden">
    <div class="text-subtitle2 text-grey-5 q-mb-lg">Laukuma specifikācija</div>
    
    <div class="diagram-wrapper relative-position" style="height: 250px;">
      <div class="wall absolute-right bg-grey-9" style="width: 10px; height: 100%; right: 0;"></div>
      <div class="board absolute" style="right: 5px; top: 20%; width: 50px; height: 50px; border-radius: 50%; border: 4px solid #191919; background: repeating-conic-gradient(#fcd116 0 18deg, #191919 0 36deg);">
        <div class="bullseye absolute-center bg-red rounded-borders" style="width: 6px; height: 6px;"></div>
      </div>

      <div class="line-h absolute" style="bottom: 10%; right: 10px; left: 20%; height: 2px; border-top: 2px dashed #ffcc00;">
         <div class="absolute-center bg-dark q-px-sm text-yellow text-weight-bold">237 cm</div>
      </div>
      
      <div class="line-v absolute" style="top: 25%; right: 60px; height: 65%; border-right: 2px dashed #ffcc00;">
         <div class="absolute-center bg-dark q-px-sm text-yellow text-weight-bold rotate-90" style="right: -25px">173 cm</div>
      </div>

      <div class="floor absolute-bottom full-width bg-grey-10" style="height: 10%;"></div>
      <div class="oche absolute" style="bottom: 10%; left: 20%; width: 10px; height: 20px; background: #ffcc00;"></div>
    </div>
  </div>
</template>

<style scoped>
.setup-container { border: 1px solid rgba(255,255,255,0.1); }
.rotate-90 { transform: rotate(90deg); }
</style>