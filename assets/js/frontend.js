(function(){
  const root=document.querySelector('[data-spike]'); if(!root) return;

  // Stepper
  let step=1,max=3,secs=[...root.querySelectorAll('section[data-step]')],tabs=[...root.querySelectorAll('.spike-step')];
  const nav=n=>{step=n; secs.forEach(s=>s.hidden=(+s.dataset.step!==n)); tabs.forEach(t=>t.classList.toggle('is-active',+t.dataset.tab===n));};
  root.addEventListener('click',e=>{ if(e.target.matches('[data-next]'))nav(Math.min(max,step+1)); if(e.target.matches('[data-prev]'))nav(Math.max(1,step-1));}); nav(1);

  // Phone mask
  const phone=root.querySelector('#primary_phone'),fmt=v=>v.replace(/\D/g,'').slice(0,10).replace(/(\d{3})(\d)/,'$1-$2').replace(/(\d{3}-\d{3})(\d)/,'$1-$2');
  if(phone) phone.addEventListener('input',()=>phone.value=fmt(phone.value));

  // Pain diagram
  const svg=root.querySelector('#spike-figure'),ptsEl=root.querySelector('#spike_points'); let side='front',pts=[];
  const FRONT='M150,40c-22,0-30,22-30,42v80c0,25-20,39-20,64v80c0,15 15,25 50,25s50-10 50-25v-80c0-25-20-39-20-64v-80c0-20-8-42-30-42z';
  const BACK ='M150,40c-22,0-30,22-30,42v80c0,30-30,45-30,70v90c0,12 18,22 60,22s60-10 60-22v-90c0-25-30-40-30-70v-80c0-20-8-42-30-42z';
  const body=()=>`<path d="${side==='front'?FRONT:BACK}" fill="#fff" stroke="#0ea5e9" stroke-width="2"/>`;
  const draw=()=>{ svg.innerHTML=body()+pts.filter(p=>p.side===side).map(p=>`<g><circle cx="${p.x}" cy="${p.y}" r="10" fill="none" stroke="red" stroke-width="3"/><line x1="${p.x-6}" y1="${p.y-6}" x2="${p.x+6}" y2="${p.y+6}" stroke="red" stroke-width="3"/><line x1="${p.x-6}" y1="${p.y+6}" x2="${p.x+6}" y2="${p.y-6}" stroke="red" stroke-width="3"/></g>`).join(''); ptsEl.value=JSON.stringify(pts); };
  svg.setAttribute('viewBox','0 0 300 700'); draw();
  svg.addEventListener('click',e=>{ const pt=svg.createSVGPoint(); pt.x=e.clientX; pt.y=e.clientY; const p=pt.matrixTransform(svg.getScreenCTM().inverse()); if(pts.length>=(window.SPIKE?.maxPts||10)) return; pts.push({side,x:Math.round(p.x),y:Math.round(p.y)}); draw(); });
  root.addEventListener('click',e=>{ if(e.target.matches('[data-side]')){ root.querySelectorAll('[data-side]').forEach(b=>b.classList.remove('is-active')); e.target.classList.add('is-active'); side=e.target.dataset.side; draw(); } if(e.target.matches('[data-clear]')){ pts=pts.filter(p=>p.side!==side); draw(); } });

  // Signature pad
  const sig=root.querySelector('#spike_sig'),out=root.querySelector('#spike_sig_data'),clr=root.querySelector('#spike_sig_clear');
  if(sig){ const ctx=sig.getContext('2d'); let d=false,lx=0,ly=0,dpr=window.devicePixelRatio||1;
    const resize=()=>{ const w=sig.clientWidth,h=sig.clientHeight; sig.width=w*dpr; sig.height=h*dpr; ctx.scale(dpr,dpr); ctx.lineWidth=2; ctx.lineCap='round'; ctx.lineJoin='round'; };
    const pos=e=>{ const r=sig.getBoundingClientRect(),t=e.touches?e.touches[0]:e; return {x:t.clientX-r.left,y:t.clientY-r.top}; };
    const start=e=>{ d=true; const p=pos(e); lx=p.x; ly=p.y; }; const move=e=>{ if(!d)return; const p=pos(e); ctx.beginPath(); ctx.moveTo(lx,ly); ctx.lineTo(p.x,p.y); ctx.stroke(); lx=p.x; ly=p.y; }; const end=()=>{ d=false; out.value=sig.toDataURL('image/png'); };
    resize(); addEventListener('resize',resize); sig.addEventListener('mousedown',start); addEventListener('mousemove',move); addEventListener('mouseup',end);
    sig.addEventListener('touchstart',start,{passive:true}); sig.addEventListener('touchmove',move,{passive:true}); sig.addEventListener('touchend',end);
    if(clr) clr.addEventListener('click',e=>{e.preventDefault(); ctx.clearRect(0,0,sig.width,sig.height); out.value='';});
  }

  // reCAPTCHA v3
  const form=root.querySelector('form'),siteKey=(window.SPIKE&&SPIKE.siteKey)||'';
  form.addEventListener('submit',function(e){
    if(!siteKey) return; e.preventDefault();
    const go=()=>form.submit();
    if(window.grecaptcha){ grecaptcha.ready(()=>grecaptcha.execute(siteKey,{action:'submit'}).then(t=>{ document.getElementById('spike_token').value=t; go(); })); }
    else{ const s=document.createElement('script'); s.src='https://www.google.com/recaptcha/api.js?render='+encodeURIComponent(siteKey); s.onload=()=>grecaptcha.ready(()=>grecaptcha.execute(siteKey,{action:'submit'}).then(t=>{ document.getElementById('spike_token').value=t; go(); })); document.head.appendChild(s); }
  });
})();
