(function(){
  const root=document.querySelector('[data-spike]'); if(!root) return;

  // stepper + gate step 1, scroll to section top
  let secs=[...root.querySelectorAll('section[data-step]')], tabs=[...root.querySelectorAll('.spike-step')];
  const nav=n=>{
    secs.forEach(s=>s.hidden=(+s.dataset.step!==n));
    tabs.forEach(t=>t.classList.toggle('is-active',+t.dataset.tab===n));
    const el=secs.find(s=>+s.dataset.step===n); if(el){
      const ab=document.getElementById('wpadminbar'); const off=(ab?ab.offsetHeight:0)+12;
      const top=el.getBoundingClientRect().top+scrollY-off; scrollTo({top,behavior:'smooth'});
    }
  };
  const validate=s=>{
    let bad=null; s.querySelectorAll('input[required],select[required],textarea[required]').forEach(el=>{
      el.classList.remove('spike-invalid'); if(!el.checkValidity()&&!bad){ bad=el; el.classList.add('spike-invalid'); }
    });
    if(bad){ bad.scrollIntoView({behavior:'smooth',block:'center'}); setTimeout(()=>bad.focus({preventScroll:true}),150); bad.reportValidity?.(); return false; }
    return true;
  };
  root.addEventListener('click',e=>{
    const n=e.target.closest('[data-next]'), p=e.target.closest('[data-prev]'); if(!n&&!p) return;
    const active=secs.find(s=>!s.hidden)||secs[0], idx=secs.indexOf(active);
    if(n){ if(idx===0&&!validate(active)) return e.preventDefault(); nav(Math.min(3,idx+2)); e.preventDefault(); }
    if(p){ nav(Math.max(1,idx)); e.preventDefault(); }
  }); nav(1);

  // Make checkbox "chips" iOS-proof: wrap text and use NBSPs
  root.querySelectorAll('.checks label').forEach(l=>{
    [...l.childNodes].forEach(n=>{
      if(n.nodeType===3 && n.nodeValue.trim().length){
        const s=document.createElement('span');
        s.className='lbl';
        s.textContent = n.nodeValue.replace(/ /g, '\u00A0'); // spaces -> NBSP
        l.replaceChild(s, n);
      }
    });
  });


  // phone mask
  const phone=root.querySelector('#primary_phone'), fmt=v=>v.replace(/\D/g,'').slice(0,10).replace(/(\d{3})(\d)/,'$1-$2').replace(/(\d{3}-\d{3})(\d)/,'$1-$2');
  if(phone) phone.addEventListener('input',()=>phone.value=fmt(phone.value));
  root.addEventListener('input',e=>{ if(e.target.matches('.spike-invalid')) e.target.classList.remove('spike-invalid'); });

  // checkbox label fix for iPhone: force &nbsp; inside labels so chips never split
  root.querySelectorAll('.checks label').forEach(l=>{
    l.childNodes.forEach(n=>{ if(n.nodeType===3) n.nodeValue=n.nodeValue.replace(/ /g,'\u00A0'); });
  });

  // pain diagram (img + canvas)
  const img=root.querySelector('#spike-diagram'), layer=root.querySelector('#spike-layer'), pointsEl=root.querySelector('#spike_points'); let pts=[];
  function resizeLayer(){ if(!img||!layer) return; const w=img.clientWidth,h=img.clientHeight; if(!w||!h) return; layer.width=w; layer.height=h; draw(); }
  function drawX(c,x,y){ c.beginPath(); c.strokeStyle='red'; c.lineWidth=3; c.moveTo(x-8,y-8); c.lineTo(x+8,y+8); c.moveTo(x-8,y+8); c.lineTo(x+8,y-8); c.stroke(); c.beginPath(); c.arc(x,y,10,0,Math.PI*2); c.stroke(); }
  function draw(){ const c=layer.getContext('2d'); c.clearRect(0,0,layer.width,layer.height); pts.forEach(p=>drawX(c,p.x*layer.width,p.y*layer.height)); if(pointsEl) pointsEl.value=JSON.stringify(pts); }
  function addPt(e){ const r=layer.getBoundingClientRect(); const x=(e.clientX-r.left)/r.width,y=(e.clientY-r.top)/r.height; if(pts.length>=(window.SPIKE?.maxPts||10)) return; pts.push({x:+x.toFixed(4),y:+y.toFixed(4)}); draw(); }
  if(img&&layer){ (img.complete?resizeLayer:img.addEventListener.bind(img))('load',resizeLayer); addEventListener('resize',resizeLayer); layer.addEventListener('click',addPt); root.addEventListener('click',e=>{ if(e.target.matches('[data-clear]')){ e.preventDefault(); pts=[]; draw(); }}); }

  // signature pad
  const sig=root.querySelector('#spike_sig'), out=root.querySelector('#spike_sig_data'), clr=root.querySelector('#spike_sig_clear');
  if(sig){ const ctx=sig.getContext('2d'); let d=false,lx=0,ly=0; const dpr=devicePixelRatio||1;
    const rz=()=>{ const w=sig.clientWidth,h=(sig.clientHeight||180); sig.width=Math.max(320,w)*dpr; sig.height=h*dpr; ctx.setTransform(1,0,0,1,0,0); ctx.scale(dpr,dpr); ctx.lineWidth=2; ctx.lineCap='round'; ctx.lineJoin='round'; };
    const pos=e=>{ const r=sig.getBoundingClientRect(),t=e.touches?e.touches[0]:e; return {x:t.clientX-r.left,y:t.clientY-r.top}; };
    const start=e=>{ e.preventDefault(); d=true; ({x:lx,y:ly}=pos(e)); };
    const move=e=>{ if(!d) return; e.preventDefault(); const p=pos(e); ctx.beginPath(); ctx.moveTo(lx,ly); ctx.lineTo(p.x,p.y); ctx.stroke(); lx=p.x; ly=p.y; };
    const end=()=>{ d=false; out.value=sig.toDataURL('image/png'); };
    rz(); addEventListener('resize',rz); sig.addEventListener('mousedown',start); addEventListener('mousemove',move); addEventListener('mouseup',end);
    sig.addEventListener('touchstart',start,{passive:false}); sig.addEventListener('touchmove',move,{passive:false}); sig.addEventListener('touchend',end);
    if(clr) clr.addEventListener('click',e=>{e.preventDefault(); ctx.clearRect(0,0,sig.width,sig.height); out.value='';});
  }

  // default sign date
  (function(){ const el=root.querySelector('#sign_date'); if(el&&!el.value) el.value=new Date().toISOString().slice(0,10); })();

  // reCAPTCHA v3
  const form=root.querySelector('form'), siteKey=(window.SPIKE&&SPIKE.siteKey)||'';
  form.addEventListener('submit',function(e){
    if(!siteKey) return; e.preventDefault(); const go=()=>form.submit();
    if(window.grecaptcha){ grecaptcha.ready(()=>grecaptcha.execute(siteKey,{action:'submit'}).then(t=>{ document.getElementById('spike_token').value=t; go(); })); }
    else{ const s=document.createElement('script'); s.src='https://www.google.com/recaptcha/api.js?render='+encodeURIComponent(siteKey);
      s.onload=()=>grecaptcha.ready(()=>grecaptcha.execute(siteKey,{action:'submit'}).then(t=>{ document.getElementById('spike_token').value=t; go(); })); document.head.appendChild(s); }
  });
})();
