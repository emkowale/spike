(function(){
  const root=document.querySelector('[data-spike]'); if(!root) return;

  // Stepper (unchanged)
  let step=1,max=3,secs=[...root.querySelectorAll('section[data-step]')],tabs=[...root.querySelectorAll('.spike-step')];
  const nav=n=>{ step=n; secs.forEach(s=>s.hidden=(+s.dataset.step!==n)); tabs.forEach(t=>t.classList.toggle('is-active',+t.dataset.tab===n)); };
  root.addEventListener('click',e=>{ if(e.target.matches('[data-next]')) nav(Math.min(max,step+1)); if(e.target.matches('[data-prev]')) nav(Math.max(1,step-1)); });
  nav(1);

  // Phone mask
  const phone=root.querySelector('#primary_phone');
  const fmt=v=>v.replace(/\D/g,'').slice(0,10).replace(/(\d{3})(\d)/,'$1-$2').replace(/(\d{3}-\d{3})(\d)/,'$1-$2');
  if(phone) phone.addEventListener('input',()=>phone.value=fmt(phone.value));

  // -----------------------------
  // Pain diagram: image + canvas
  // -----------------------------
  const img = root.querySelector('#spike-diagram');
  const layer = root.querySelector('#spike-layer');
  const pointsEl = root.querySelector('#spike_points');
  let pts = []; // store as % so it redraws responsively

  function resizeLayer(){
    if(!img || !layer) return;
    const w = img.clientWidth, h = img.clientHeight;
    if(!w || !h) return;
    layer.width = w; layer.height = h;
    drawMarks();
  }

  function drawX(ctx,x,y){
    ctx.beginPath(); ctx.strokeStyle='red'; ctx.lineWidth=3;
    ctx.moveTo(x-8,y-8); ctx.lineTo(x+8,y+8);
    ctx.moveTo(x-8,y+8); ctx.lineTo(x+8,y-8);
    ctx.stroke();
    ctx.beginPath(); ctx.arc(x,y,10,0,Math.PI*2); ctx.stroke();
  }

  function drawMarks(){
    if(!layer) return;
    const ctx = layer.getContext('2d');
    ctx.clearRect(0,0,layer.width,layer.height);
    pts.forEach(p => drawX(ctx, p.x*layer.width, p.y*layer.height));
    if(pointsEl) pointsEl.value = JSON.stringify(pts);
  }

  function addPoint(e){
    if(!layer) return;
    const r = layer.getBoundingClientRect();
    const x = (e.clientX - r.left) / r.width;
    const y = (e.clientY - r.top) / r.height;
    if(pts.length >= (window.SPIKE?.maxPts || 10)) return;
    pts.push({x:+x.toFixed(4), y:+y.toFixed(4)});
    drawMarks();
  }

  if(img && layer){
    if(img.complete) resizeLayer(); else img.addEventListener('load', resizeLayer);
    window.addEventListener('resize', resizeLayer);
    layer.addEventListener('click', addPoint);
    root.addEventListener('click', e=>{
      if(e.target.matches('[data-clear]')){ e.preventDefault(); pts=[]; drawMarks(); }
    });
  }

  // -----------------------------
  // Signature pad (responsive)
  // -----------------------------
  const sig=root.querySelector('#spike_sig'), out=root.querySelector('#spike_sig_data'), clr=root.querySelector('#spike_sig_clear');
  if(sig){
    const ctx=sig.getContext('2d'); let d=false,lx=0,ly=0; const dpr=window.devicePixelRatio||1;
    const resize=()=>{ const w=sig.clientWidth, h=(sig.clientHeight||180); sig.width=Math.max(320,w)*dpr; sig.height=h*dpr; ctx.setTransform(1,0,0,1,0,0); ctx.scale(dpr,dpr); ctx.lineWidth=2; ctx.lineCap='round'; ctx.lineJoin='round'; };
    const pos=e=>{ const r=sig.getBoundingClientRect(),t=e.touches?e.touches[0]:e; return {x:t.clientX-r.left,y:t.clientY-r.top}; };
    const start=e=>{ e.preventDefault(); d=true; const p=pos(e); lx=p.x; ly=p.y; };
    const move=e=>{ if(!d) return; e.preventDefault(); const p=pos(e); ctx.beginPath(); ctx.moveTo(lx,ly); ctx.lineTo(p.x,p.y); ctx.stroke(); lx=p.x; ly=p.y; };
    const end =()=>{ d=false; out.value=sig.toDataURL('image/png'); };
    resize(); addEventListener('resize',resize);
    sig.addEventListener('mousedown',start); addEventListener('mousemove',move); addEventListener('mouseup',end);
    sig.addEventListener('touchstart',start,{passive:false}); sig.addEventListener('touchmove',move,{passive:false}); sig.addEventListener('touchend',end);
    if(clr) clr.addEventListener('click',e=>{e.preventDefault(); ctx.clearRect(0,0,sig.width,sig.height); out.value='';});
  }

  // Default date to today
  (function(){ const el=document.querySelector('#sign_date'); if(el && !el.value){ el.value=new Date().toISOString().slice(0,10); }})();

  // reCAPTCHA v3
  const form=root.querySelector('form'), siteKey=(window.SPIKE&&SPIKE.siteKey)||'';
  form.addEventListener('submit',function(e){
    if(!siteKey) return; e.preventDefault();
    const go=()=>form.submit();
    if(window.grecaptcha){ grecaptcha.ready(()=>grecaptcha.execute(siteKey,{action:'submit'}).then(t=>{ document.getElementById('spike_token').value=t; go(); })); }
    else{ const s=document.createElement('script'); s.src='https://www.google.com/recaptcha/api.js?render='+encodeURIComponent(siteKey); s.onload=()=>grecaptcha.ready(()=>grecaptcha.execute(siteKey,{action:'submit'}).then(t=>{ document.getElementById('spike_token').value=t; go(); })); document.head.appendChild(s); }
  });
})();
