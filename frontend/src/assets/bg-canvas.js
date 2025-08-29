//  background canvas animation
(function(){
  if (typeof window === 'undefined') return;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  function createCanvas(){
    const c = document.createElement('canvas');
    c.id = 'bg-anim-canvas';
    c.style.position = 'fixed';
    c.style.left = '0';
    c.style.top = '0';
    c.style.width = '100%';
    c.style.height = '100%';
    c.style.zIndex = '0';
    c.style.pointerEvents = 'none'; 
 
  c.style.opacity = '0.92';
    document.body.appendChild(c);
    return c;
  }

  const canvas = createCanvas();
  const ctx = canvas.getContext('2d');

  function resize(){
    const dpr = window.devicePixelRatio || 1;
    canvas.width = Math.max(800, window.innerWidth) * dpr;
    canvas.height = Math.max(600, window.innerHeight) * dpr;
    canvas.style.width = window.innerWidth + 'px';
    canvas.style.height = window.innerHeight + 'px';
    ctx.setTransform(dpr,0,0,dpr,0,0);
  }

  let t=0;
  function draw(){
    t+=0.08; // much faster so motion is clearly visible
    const w = canvas.width / (window.devicePixelRatio||1);
    const h = canvas.height / (window.devicePixelRatio||1);

    // clear with subtle gradient
    const g = ctx.createLinearGradient(0,0,w, h);
    g.addColorStop(0, 'rgba(243,246,249,1)');
    g.addColorStop(1, 'rgba(233,238,245,1)');
    ctx.fillStyle = g;
    ctx.fillRect(0,0,w,h);

    // draw blobs
  // much stronger, more colorful blobs
  drawBlob(w*0.12, h*0.18, 400, t, 'rgba(17,94,160,0.32)');
  drawBlob(w*0.85, h*0.82, 460, t*0.8 + 1.2, 'rgba(3,58,86,0.28)');
  drawBlob(w*0.65, h*0.25, 340, t*1.3 + 2.7, 'rgba(12,48,78,0.20)');
  // additional accent blobs
  drawBlob(w*0.28, h*0.72, 260, t*1.05 + 3.1, 'rgba(6,110,170,0.18)');
  drawBlob(w*0.5, h*0.5, 240, t*0.6 + 0.9, 'rgba(30,130,200,0.14)');

  // particle layer for texture
  drawParticles(w, h, t);

  // subtle tint overlay to emphasize color (multiply blend)
  ctx.save();
  ctx.globalCompositeOperation = 'multiply';
  ctx.fillStyle = 'rgba(9,48,88,0.06)';
  ctx.fillRect(0,0,w,h);
  ctx.restore();
  }

  function drawBlob(cx, cy, radius, phase, color){
    const p = 0.6; // complexity
  ctx.save();
  ctx.fillStyle = color;
  ctx.beginPath();
    const steps = 64;
    for(let i=0;i<=steps;i++){
      const a = (i/steps) * Math.PI * 2;
      const r = radius * (1 + 0.12 * Math.sin(a*3 + phase*2) + 0.06 * Math.cos(a*5 - phase));
      const x = cx + Math.cos(a) * r;
      const y = cy + Math.sin(a) * r;
      if (i===0) ctx.moveTo(x,y); else ctx.lineTo(x,y);
    }
  ctx.closePath();
    // much less blur so shapes are clearer and visible
    ctx.filter = 'blur(6px)';
    // stronger additive blending
    ctx.globalCompositeOperation = 'lighter';
  ctx.fill();
    ctx.restore();
  }

  // small particle layer - subtle circles moving slowly
  function drawParticles(w,h,phase){
    const count = Math.min(120, Math.round((w*h)/80000));
    ctx.save();
    ctx.globalCompositeOperation = 'lighter';
    for(let i=0;i<count;i++){
      const ix = (i*47) % w;
      const iy = ((i*83)+ (Math.sin(phase*0.5 + i)*30)) % h;
      const r = 1.8 + (Math.sin(phase + i*0.13) + 1) * 2.2;
      ctx.beginPath();
      ctx.fillStyle = 'rgba(255,255,255,0.06)';
      ctx.arc(ix, iy, r, 0, Math.PI*2);
      ctx.closePath();
      ctx.fill();
    }
    ctx.restore();
  }

  let rafId;
  function loop(){
    draw();
    rafId = requestAnimationFrame(loop);
  }

  window.addEventListener('resize', function(){
    resize();
  });

  resize();
  rafId = requestAnimationFrame(loop);

  // expose stop for debugging
  window.__bgCanvas = { stop: ()=>{ cancelAnimationFrame(rafId); if (canvas && canvas.parentNode) canvas.parentNode.removeChild(canvas);} };

})();
