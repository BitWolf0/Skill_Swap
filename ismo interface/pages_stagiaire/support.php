<?php
$pageTitle = 'ISMO-SkillSwap — Support';
$currentPage = 'info';
$basePath = '..';
include __DIR__ . '/../backend/includes/header.php';
?>

<main class="content-area legal-page" id="main-content">
  <section class="content-main" style="max-width:800px;margin:0 auto;">
    <div class="legal-card support-layout" style="display:flex;flex-direction:column;gap:20px;">
      <h1>Support technique</h1>
      <p>Un probleme ? Creer un ticket et notre equipe vous repondra sous 24h.</p>
      <div class="support-tabs" role="tablist">
        <button class="support-tab active" data-panel="list" role="tab" aria-selected="true">Mes tickets</button>
        <button class="support-tab" data-panel="create" role="tab" aria-selected="false">Nouveau ticket</button>
      </div>
      <div class="support-panel active" id="panel-list" role="tabpanel">
        <div class="ticket-list" id="ticket-list">
          <div class="ticket-empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <p>Aucun ticket pour le moment.</p>
          </div>
        </div>
      </div>
      <div class="support-panel" id="panel-create" role="tabpanel">
        <form class="ticket-form" id="ticket-form">
          <div class="form-group">
            <label for="ticket-subject">Sujet</label>
            <input type="text" id="ticket-subject" placeholder="Ex: Probleme de connexion" required />
          </div>
          <div class="form-group">
            <label for="ticket-category">Categorie</label>
            <select id="ticket-category">
              <option value="account">Compte</option>
              <option value="technical">Probleme technique</option>
              <option value="billing">Facturation</option>
              <option value="report">Signaler</option>
              <option value="other">Autre</option>
            </select>
          </div>
          <div class="form-group">
            <label for="ticket-description">Description</label>
            <textarea id="ticket-description" placeholder="Decrivez votre probleme en details..." required></textarea>
          </div>
          <button type="submit" class="btn-sm btn-primary">Envoyer</button>
        </form>
      </div>
    </div>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
<script>
'use strict';
const STORAGE_KEY = 'ismo_support_tickets';
document.querySelectorAll('.support-tab').forEach(btn=>{btn.addEventListener('click',()=>{document.querySelectorAll('.support-tab').forEach(b=>{b.classList.remove('active');b.setAttribute('aria-selected','false')});document.querySelectorAll('.support-panel').forEach(p=>p.classList.remove('active'));btn.classList.add('active');btn.setAttribute('aria-selected','true');document.getElementById('panel-'+btn.dataset.panel).classList.add('active')})});
function toast(msg){const el=document.getElementById('toast-container');const t=document.createElement('div');t.className='toast toast-success';t.setAttribute('role','status');t.innerHTML='<span>'+msg+'</span>';el.appendChild(t);setTimeout(()=>{t.classList.add('toast-exit');t.addEventListener('animationend',()=>t.remove(),{once:true})},3000)}
function getTickets(){try{return JSON.parse(localStorage.getItem(STORAGE_KEY))||[]}catch{return[]}}
function saveTickets(t){localStorage.setItem(STORAGE_KEY,JSON.stringify(t))}
function renderTickets(){const list=document.getElementById('ticket-list');const tickets=getTickets();if(!tickets.length){list.innerHTML='<div class="ticket-empty"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><p>Aucun ticket pour le moment.</p></div>';return}
list.innerHTML=tickets.map(t=>renderTicket(t)).join('')}
function renderTicket(t){const statusLabels={open:'Ouvert',in_progress:'En cours',waiting:'En attente',resolved:'Resolu',closed:'Ferme'};const catLabels={account:'Compte',technical:'Technique',billing:'Facturation',report:'Signalement',other:'Autre'};const repliesHtml=(t.replies||[]).map(r=>'<div class="ticket-reply'+(r.staff?' staff':'')+'"><div class="ticket-reply-avatar">'+(r.staff?'S':'U')+'</div><div class="ticket-reply-body"><div class="ticket-reply-header"><span class="ticket-reply-author">'+(r.staff?'Support ISMO':'Vous')+'</span><span class="ticket-reply-time">'+new Date(r.date).toLocaleDateString('fr-FR')+'</span></div><div class="ticket-reply-text">'+r.text+'</div></div></div>').join('');return'<div class="ticket-card" data-id="'+t.id+'"><div class="ticket-card-header"><span class="ticket-card-subject">'+t.subject+'</span><div class="ticket-card-meta"><span class="ticket-category">'+(catLabels[t.category]||t.category)+'</span><span class="ticket-status '+t.status+'">'+(statusLabels[t.status]||t.status)+'</span><span class="ticket-date">'+new Date(t.date).toLocaleDateString('fr-FR')+'</span></div></div><div class="ticket-card-expand"><div class="ticket-description">'+t.description+'</div>'+repliesHtml+'</div></div>'}
document.getElementById('ticket-form').addEventListener('submit',e=>{e.preventDefault();const subject=document.getElementById('ticket-subject').value.trim();const category=document.getElementById('ticket-category').value;const description=document.getElementById('ticket-description').value.trim();if(!subject||!description)return;const tickets=getTickets();tickets.unshift({id:Date.now(),subject,category,description,status:'open',date:new Date().toISOString(),replies:[]});saveTickets(tickets);renderTickets();document.getElementById('ticket-subject').value='';document.getElementById('ticket-description').value='';document.querySelector('[data-panel="list"]').click();toast('Ticket envoye ! Notre equipe vous repondra sous 24h.')});
document.getElementById('ticket-list').addEventListener('click',e=>{const card=e.target.closest('.ticket-card');if(card)card.classList.toggle('expanded')});
document.addEventListener('DOMContentLoaded',()=>renderTickets());
</script>
</body>
</html>
