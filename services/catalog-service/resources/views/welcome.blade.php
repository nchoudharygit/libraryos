<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LibraryOS — Admin Console</title>
<style>
  :root{
    --bg: #EFEAD9;
    --bg-alt: #F5F1E4;
    --bg-alt2: #FAF7EE;
    --paper: #FFFDF7;
    --paper-dim: #F3EEDC;
    --paper-line: #E1D6B8;
    --ink: #2A2417;
    --ink-soft: #6E6650;
    --brass: #B8935B;
    --brass-dark: #8C6C3E;
    --brass-light: #7A5E36;
    --success: #3F6B4F;
    --success-bg: #E4EDE3;
    --error: #97392A;
    --error-bg: #F2DFD8;
    --pending: #A5761F;
    --rule: rgba(140,108,62,0.28);
    --header-ink: #2A2417;
    --serif: Georgia, "Iowan Old Style", "Palatino Linotype", "Book Antiqua", serif;
    --mono: ui-monospace, "SFMono-Regular", "JetBrains Mono", Menlo, Consolas, monospace;
    --sans: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{margin:0;padding:0;}
  body{
    background: radial-gradient(ellipse at top, var(--bg-alt2) 0%, var(--bg) 60%);
    color: var(--header-ink);
    font-family: var(--sans);
    min-height: 100vh;
    padding: 26px 20px 60px;
  }
  .wrap{ max-width: 1180px; margin: 0 auto; }

  header{
    display:flex; align-items:flex-end; justify-content:space-between; gap:20px;
    flex-wrap:wrap; margin-bottom: 20px; border-bottom: 1px solid var(--rule); padding-bottom: 16px;
  }
  .brand-mark{ display:flex; align-items:center; gap:14px; }
  .stamp-logo{
    width:44px; height:44px; border:2px solid var(--brass); border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-family: var(--serif); font-weight:700; font-size:17px; color:var(--brass-light);
    flex-shrink:0; transform:rotate(-6deg);
  }
  h1{ font-family: var(--serif); font-size: 24px; font-weight:700; margin:0; color: var(--header-ink); }
  .subtitle{
    font-size: 11.5px; color: var(--brass-dark); margin-top:3px; letter-spacing:0.4px;
    font-family: var(--mono); text-transform: uppercase;
  }

  /* settings strip */
  .settings-strip{
    display:flex; gap:14px; flex-wrap:wrap; margin-bottom:18px;
    background: var(--paper); border:1px solid var(--rule); border-radius:5px; padding:10px 14px;
    box-shadow: 0 2px 10px rgba(140,108,62,0.08);
  }
  .settings-strip .su{ display:flex; align-items:center; gap:8px; flex:1; min-width:220px; }
  .settings-strip label{
    font-family: var(--mono); font-size:10.5px; color: var(--brass-dark); text-transform:uppercase;
    letter-spacing:0.4px; white-space:nowrap;
  }
  .settings-strip input{
    flex:1; background: var(--bg-alt2); border:1px solid var(--paper-line); color: var(--ink);
    font-family: var(--mono); font-size:11.5px; padding:6px 8px; border-radius:3px;
  }
  .settings-strip input:focus{ outline:none; border-color:var(--brass); }
  .health-dot{ width:7px; height:7px; border-radius:50%; background:#555; flex-shrink:0; }
  .health-dot.ok{ background: var(--success); box-shadow:0 0 5px var(--success); }
  .health-dot.bad{ background: var(--error); box-shadow:0 0 5px var(--error); }

  /* tabs */
  .tabs{ display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; }
  .tab{
    font-family: var(--serif); font-weight:700; font-size:14px; padding:10px 20px;
    border-radius: 4px 4px 0 0; cursor:pointer; color: var(--brass-dark);
    background: var(--bg-alt2); border:1px solid var(--rule); border-bottom:none;
    transition: all .12s ease;
  }
  .tab:hover{ color: var(--ink); }
  .tab.active{ background: var(--paper); color: var(--ink); border-color: var(--paper); box-shadow: 0 -2px 8px rgba(140,108,62,0.08); }

  .card{
    background: var(--paper); color: var(--ink); border-radius: 0 5px 5px 5px;
    padding: 20px 22px; box-shadow: 0 6px 24px rgba(140,108,62,0.14); border: 1px solid var(--paper-line);
  }
  .card + .card{ margin-top: 18px; border-radius:5px; }

  .toolbar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; gap:10px; flex-wrap:wrap; }
  .toolbar h2{ font-family: var(--serif); font-size:17px; margin:0; }
  .toolbar-actions{ display:flex; gap:8px; }
  .btn{
    font-family: var(--mono); font-size:12px; padding:8px 14px; border-radius:3px; cursor:pointer;
    border:1px solid var(--paper-line); background: var(--paper-dim); color: var(--ink-soft);
    transition: all .12s ease;
  }
  .btn:hover{ border-color: var(--brass-dark); color: var(--ink); }
  .btn-primary{ background: var(--ink); color: var(--paper); border-color: var(--ink); }
  .btn-primary:hover{ opacity:0.9; }
  .btn-danger{ color: var(--error); border-color: var(--error-bg); }
  .btn-danger:hover{ background: var(--error-bg); border-color: var(--error); }
  .btn-small{ padding:5px 10px; font-size:11px; }

  table{ width:100%; border-collapse: collapse; font-family: var(--mono); font-size:12.5px; }
  th{
    text-align:left; padding:8px 10px; border-bottom: 2px solid var(--brass-dark);
    color: var(--ink-soft); font-size:10.5px; text-transform:uppercase; letter-spacing:0.4px;
  }
  td{ padding:9px 10px; border-bottom: 1px solid var(--paper-line); vertical-align:middle; }
  tr:hover td{ background: rgba(184,147,91,0.08); }
  .row-actions{ display:flex; gap:6px; justify-content:flex-end; }
  .badge{ display:inline-block; padding:2px 8px; border-radius:10px; font-size:10.5px; }
  .badge.yes{ background: var(--success-bg); color: var(--success); }
  .badge.no{ background: var(--error-bg); color: var(--error); }
  .empty-note{ font-family: var(--mono); font-size:12.5px; color: var(--ink-soft); font-style:italic; padding: 10px 0; }

  /* form */
  .form-panel{ margin-top:18px; padding-top:16px; border-top: 1px dashed var(--paper-line); }
  .form-title{ font-family: var(--serif); font-weight:700; font-size:15px; margin-bottom:12px; }
  .field-grid{ display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px; }
  @media (max-width:640px){ .field-grid{ grid-template-columns:1fr; } }
  .field-grid .full{ grid-column: 1 / -1; }
  .field label{
    display:block; font-family: var(--mono); font-size:10.5px; text-transform:uppercase;
    letter-spacing:0.4px; color: var(--ink-soft); margin-bottom:5px;
  }
  .field label .req{ color: var(--error); }
  .field input[type=text], .field input[type=number], .field input[type=date], .field select, .field textarea{
    width:100%; padding:8px 10px; border:1px solid var(--paper-line); border-radius:3px;
    background:#fff; font-family: var(--mono); font-size:12.5px; color:var(--ink);
  }
  .field textarea{ min-height:70px; resize:vertical; }
  .field input:focus, .field select:focus, .field textarea:focus{ outline:none; border-color:var(--brass-dark); }
  .field-checkbox{ display:flex; align-items:center; gap:8px; padding-top:22px; }
  .form-buttons{ display:flex; gap:10px; }

  /* response ledger */
  .stamp{
    display:inline-flex; align-items:center; gap:8px; font-family: var(--serif); font-weight:700;
    font-size:13px; padding: 5px 12px; border-radius: 4px; border: 2px solid currentColor;
    transform: rotate(-2deg); letter-spacing:0.4px;
  }
  .stamp.ok{ color: var(--success); background: var(--success-bg); }
  .stamp.warn{ color: var(--pending); background: #F5EBD6; }
  .stamp.err{ color: var(--error); background: var(--error-bg); }
  .resp-meta{ font-family: var(--mono); font-size:11px; color: var(--ink-soft); margin-top:8px; }
  pre.resp-body{
    margin-top: 12px; background: #FBF9F3; border:1px solid var(--paper-line); border-radius:4px;
    padding:12px; font-family: var(--mono); font-size:12px; line-height:1.5; overflow-x:auto;
    max-height:260px; color:var(--ink);
  }
  .json-key{ color: var(--brass-dark); } .json-str{ color:#3F6B4F; } .json-num{ color:#2A5A8C; } .json-bool{ color:#97392A; }
</style>
</head>
<body>
<div class="wrap">

  <header>
    <div class="brand-mark">
      <div class="stamp-logo">L</div>
      <div>
        <h1>LibraryOS Admin Console</h1>
        <div class="subtitle">books · authors · members · transactions — full CRUD practice</div>
      </div>
    </div>
  </header>

  <div class="settings-strip" id="settingsStrip"></div>

  <div class="tabs" id="tabs"></div>

  <div class="card">
    <div class="toolbar">
      <h2 id="toolbarTitle"></h2>
      <div class="toolbar-actions">
        <button class="btn" id="refreshBtn">Refresh</button>
        <button class="btn btn-primary" id="addBtn">+ Add New</button>
      </div>
    </div>
    <div id="tableWrap"></div>
    <div id="formWrap"></div>
  </div>

  <div class="card">
    <div class="toolbar"><h2>Response Ledger</h2></div>
    <div id="responseArea"><div class="empty-note">No action yet — add, edit, or delete a record to see the raw API response here.</div></div>
  </div>

</div>

<script>
const entities = {
  books: {
    label: 'Books', service: 'catalog', endpoint: 'books',
    columns: [['id','ID'],['title','Title'],['author_id','Author ID'],['isbn','ISBN'],['genre','Genre'],['total_copies','Copies']],
    fields: [
      { name:'title', label:'Title', type:'text', required:true, full:true },
      { name:'author_id', label:'Author', type:'authorSelect', required:true },
      { name:'isbn', label:'ISBN', type:'text', required:true },
      { name:'genre', label:'Genre', type:'text' },
      { name:'total_copies', label:'Total Copies', type:'number', default:1 },
      { name:'published_date', label:'Published Date', type:'date' },
      { name:'description', label:'Description', type:'textarea', full:true },
    ]
  },
  authors: {
    label:'Authors', service:'catalog', endpoint:'authors',
    columns: [['id','ID'],['name','Name'],['email','Email'],['nationality','Nationality']],
    fields: [
      { name:'name', label:'Name', type:'text', required:true },
      { name:'email', label:'Email', type:'text' },
      { name:'nationality', label:'Nationality', type:'text' },
      { name:'bio', label:'Bio', type:'textarea', full:true },
    ]
  },
  members: {
    label:'Members', service:'consumer', endpoint:'members',
    columns: [['id','ID'],['name','Name'],['email','Email'],['membership_id','Membership ID'],['is_active','Active']],
    fields: [
      { name:'name', label:'Name', type:'text', required:true },
      { name:'email', label:'Email', type:'text', required:true },
      { name:'membership_id', label:'Membership ID', type:'text', required:true },
      { name:'join_date', label:'Join Date', type:'date', required:true },
      { name:'is_active', label:'Active', type:'checkbox', default:true },
    ]
  },
  transactions: {
    label:'Transactions', service:'transaction', endpoint:'transactions',
    columns: [['id','ID'],['book_id','Book ID'],['consumer_id','Member ID'],['issue_date','Issued'],['due_date','Due'],['return_date','Returned'],['status','Status']],
    fields: [
      { name:'book_id', label:'Book', type:'bookSelect', required:true },
      { name:'consumer_id', label:'Member', type:'memberSelect', required:true },
      { name:'issue_date', label:'Issue Date', type:'date', required:true },
      { name:'due_date', label:'Due Date', type:'date', required:true },
      { name:'return_date', label:'Return Date', type:'date' },
      { name:'status', label:'Status', type:'text' },
    ]
  }
};

let state = {
  activeEntity: 'books',
  baseUrls: {
    catalog: 'http://localhost:8001',
    consumer: 'http://localhost:8002',
    transaction: 'http://localhost:8003',
  },
  health: { catalog: null, consumer: null, transaction: null },
  data: { books: [], authors: [], members: [], transactions: [] },
  loaded: { books:false, authors:false, members:false, transactions:false },
  formMode: null, // 'create' | 'edit' | null
  editingId: null,
};

const settingsStrip = document.getElementById('settingsStrip');
const tabsEl = document.getElementById('tabs');
const toolbarTitle = document.getElementById('toolbarTitle');
const tableWrap = document.getElementById('tableWrap');
const formWrap = document.getElementById('formWrap');
const responseArea = document.getElementById('responseArea');
const addBtn = document.getElementById('addBtn');
const refreshBtn = document.getElementById('refreshBtn');

function apiBase(entityKey){ return state.baseUrls[entities[entityKey].service]; }

function renderSettingsStrip(){
  settingsStrip.innerHTML = Object.keys(state.baseUrls).map(svc => {
    const dot = state.health[svc] === true ? 'ok' : (state.health[svc] === false ? 'bad' : '');
    return `
      <div class="su">
        <span class="health-dot ${dot}"></span>
        <label>${svc}</label>
        <input type="text" data-svc="${svc}" value="${state.baseUrls[svc]}" />
      </div>`;
  }).join('');
  settingsStrip.querySelectorAll('input[data-svc]').forEach(inp => {
    inp.addEventListener('change', e => { state.baseUrls[e.target.dataset.svc] = e.target.value; });
  });
}

function renderTabs(){
  tabsEl.innerHTML = Object.keys(entities).map(key => `
    <div class="tab ${state.activeEntity === key ? 'active' : ''}" data-entity="${key}">${entities[key].label}</div>
  `).join('');
  tabsEl.querySelectorAll('.tab').forEach(t => {
    t.addEventListener('click', () => {
      state.activeEntity = t.dataset.entity;
      state.formMode = null;
      renderAll();
      ensureLoaded(state.activeEntity);
    });
  });
}

async function ensureLoaded(entityKey, force=false){
  if (state.loaded[entityKey] && !force) return;
  await fetchList(entityKey);
}

async function fetchList(entityKey){
  const ent = entities[entityKey];
  const url = `${apiBase(entityKey)}/api/v1/${ent.endpoint}`;
  tableWrap.innerHTML = `<div class="empty-note">Loading ${ent.label.toLowerCase()}…</div>`;
  try {
    const res = await fetch(url, { headers: { Accept: 'application/json' } });
    const json = await res.json();
    state.data[entityKey] = Array.isArray(json) ? json : (json.data || []);
    state.loaded[entityKey] = true;
    state.health[ent.service] = res.ok;
  } catch (e) {
    state.data[entityKey] = [];
    state.health[ent.service] = false;
    tableWrap.innerHTML = `<div class="empty-note">Could not reach ${ent.service} service at ${apiBase(entityKey)} — is it running? (${e.message})</div>`;
    renderSettingsStrip();
    return;
  }
  renderSettingsStrip();
  renderTable();
}

function renderTable(){
  const key = state.activeEntity;
  const ent = entities[key];
  const rows = state.data[key];
  toolbarTitle.textContent = ent.label;

  if (!rows || rows.length === 0) {
    tableWrap.innerHTML = `<div class="empty-note">No ${ent.label.toLowerCase()} yet — click "+ Add New" to create one.</div>`;
    return;
  }

  const thead = ent.columns.map(c => `<th>${c[1]}</th>`).join('') + '<th></th>';
  const tbody = rows.map(r => {
    const tds = ent.columns.map(c => {
      let v = r[c[0]];
      if (typeof v === 'boolean') {
        v = `<span class="badge ${v ? 'yes' : 'no'}">${v ? 'yes' : 'no'}</span>`;
      } else if (v === null || v === undefined || v === '') {
        v = '<span style="color:#B3AA8F">—</span>';
      }
      return `<td>${v}</td>`;
    }).join('');
    return `
      <tr>
        ${tds}
        <td>
          <div class="row-actions">
            <button class="btn btn-small" data-edit="${r.id}">Edit</button>
            <button class="btn btn-small btn-danger" data-del="${r.id}">Delete</button>
          </div>
        </td>
      </tr>`;
  }).join('');

  tableWrap.innerHTML = `<table><thead><tr>${thead}</tr></thead><tbody>${tbody}</tbody></table>`;

  tableWrap.querySelectorAll('[data-edit]').forEach(b => {
    b.addEventListener('click', () => openForm('edit', parseInt(b.dataset.edit)));
  });
  tableWrap.querySelectorAll('[data-del]').forEach(b => {
    b.addEventListener('click', () => deleteRecord(parseInt(b.dataset.del)));
  });
}

async function openForm(mode, id=null){
  state.formMode = mode;
  state.editingId = id;
  const key = state.activeEntity;
  const ent = entities[key];

  // preload reference data for select-type fields
  const needsAuthors = ent.fields.some(f => f.type === 'authorSelect');
  const needsBooks = ent.fields.some(f => f.type === 'bookSelect');
  const needsMembers = ent.fields.some(f => f.type === 'memberSelect');
  if (needsAuthors) await ensureLoaded('authors');
  if (needsBooks) await ensureLoaded('books');
  if (needsMembers) await ensureLoaded('members');

  const record = mode === 'edit' ? state.data[key].find(r => r.id === id) : {};
  renderForm(record || {});
}

function renderForm(record){
  const key = state.activeEntity;
  const ent = entities[key];
  const isEdit = state.formMode === 'edit';

  const fieldsHtml = ent.fields.map(f => {
    const val = record[f.name] !== undefined && record[f.name] !== null ? record[f.name] : (f.default !== undefined ? f.default : '');
    const reqMark = f.required ? '<span class="req">*</span>' : '';
    let inputHtml = '';

    if (f.type === 'textarea') {
      inputHtml = `<textarea data-field="${f.name}">${val}</textarea>`;
    } else if (f.type === 'checkbox') {
      inputHtml = `<div class="field-checkbox"><input type="checkbox" data-field="${f.name}" ${val ? 'checked' : ''} style="width:16px;height:16px;" /><span style="font-family:var(--mono);font-size:12px;">${val ? 'Active' : 'Inactive'}</span></div>`;
    } else if (f.type === 'authorSelect' || f.type === 'bookSelect' || f.type === 'memberSelect') {
      const srcKey = f.type === 'authorSelect' ? 'authors' : (f.type === 'bookSelect' ? 'books' : 'members');
      const opts = (state.data[srcKey] || []).map(o => {
        const labelField = srcKey === 'authors' ? o.name : (srcKey === 'books' ? o.title : o.name);
        return `<option value="${o.id}" ${String(val) === String(o.id) ? 'selected' : ''}>${o.id} — ${labelField}</option>`;
      }).join('');
      inputHtml = `<select data-field="${f.name}"><option value="">— select —</option>${opts}</select>`;
    } else {
      inputHtml = `<input type="${f.type}" data-field="${f.name}" value="${val}" />`;
    }

    return `<div class="field ${f.full ? 'full' : ''}"><label>${f.label} ${reqMark}</label>${inputHtml}</div>`;
  }).join('');

  formWrap.innerHTML = `
    <div class="form-panel">
      <div class="form-title">${isEdit ? `Edit ${ent.label.slice(0,-1)} #${record.id}` : `New ${ent.label.slice(0,-1)}`}</div>
      <div class="field-grid">${fieldsHtml}</div>
      <div class="form-buttons">
        <button class="btn btn-primary" id="saveFormBtn">${isEdit ? 'Save Changes' : 'Create'}</button>
        <button class="btn" id="cancelFormBtn">Cancel</button>
      </div>
    </div>
  `;

  document.getElementById('cancelFormBtn').addEventListener('click', () => {
    state.formMode = null;
    formWrap.innerHTML = '';
  });
  document.getElementById('saveFormBtn').addEventListener('click', submitForm);
}

function collectFormValues(){
  const key = state.activeEntity;
  const ent = entities[key];
  const values = {};
  ent.fields.forEach(f => {
    const el = formWrap.querySelector(`[data-field="${f.name}"]`);
    if (!el) return;
    let v;
    if (f.type === 'checkbox') v = el.checked;
    else v = el.value;

    if (v === '' || v === null) {
      if (f.required) values.__missing = values.__missing || [], values.__missing.push(f.label);
      return; // skip empty optional fields
    }
    if (f.type === 'number' || f.type === 'authorSelect' || f.type === 'bookSelect' || f.type === 'memberSelect') {
      v = Number(v);
    }
    values[f.name] = v;
  });
  return values;
}

async function submitForm(){
  const key = state.activeEntity;
  const ent = entities[key];
  const values = collectFormValues();

  if (values.__missing) {
    renderResponse({ error: true, message: `Please fill required fields: ${values.__missing.join(', ')}` }, 422, '', 'VALIDATION', 0);
    return;
  }
  delete values.__missing;

  const isEdit = state.formMode === 'edit';
  const base = apiBase(key);
  const url = isEdit ? `${base}/api/v1/${ent.endpoint}/${state.editingId}` : `${base}/api/v1/${ent.endpoint}`;
  const method = isEdit ? 'PUT' : 'POST';

  const started = performance.now();
  try {
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(values),
    });
    const elapsed = Math.round(performance.now() - started);
    const text = await res.text();
    let data; try { data = text ? JSON.parse(text) : null; } catch(e) { data = text; }
    renderResponse(data, res.status, url, method, elapsed);

    if (res.ok) {
      state.formMode = null;
      formWrap.innerHTML = '';
      await fetchList(key);
    }
  } catch (e) {
    renderResponse({ error: true, message: 'Request failed: ' + e.message }, 0, url, method, 0);
  }
}

async function deleteRecord(id){
  const key = state.activeEntity;
  const ent = entities[key];
  if (!confirm(`Delete ${ent.label.slice(0,-1).toLowerCase()} #${id}? This cannot be undone from here.`)) return;

  const url = `${apiBase(key)}/api/v1/${ent.endpoint}/${id}`;
  const started = performance.now();
  try {
    const res = await fetch(url, { method: 'DELETE', headers: { Accept: 'application/json' } });
    const elapsed = Math.round(performance.now() - started);
    const text = await res.text();
    let data; try { data = text ? JSON.parse(text) : null; } catch(e) { data = text; }
    renderResponse(data, res.status, url, 'DELETE', elapsed);
    if (res.ok) await fetchList(key);
  } catch (e) {
    renderResponse({ error: true, message: 'Request failed: ' + e.message }, 0, url, 'DELETE', 0);
  }
}

function syntaxHighlight(json){
  const esc = String(json).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  return esc.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(\.\d+)?([eE][+-]?\d+)?)/g,
    match => {
      let cls = 'json-num';
      if (/^"/.test(match)) cls = /:$/.test(match) ? 'json-key' : 'json-str';
      else if (/true|false/.test(match)) cls = 'json-bool';
      return `<span class="${cls}">${match}</span>`;
    });
}

function renderResponse(data, status, url, method, elapsed){
  let stampClass = 'err', stampText = 'FAILED';
  if (status >= 200 && status < 300) { stampClass = 'ok'; stampText = status + ' OK'; }
  else if (status >= 400 && status < 500) { stampClass = 'warn'; stampText = status + ' CLIENT ERROR'; }
  else if (status >= 500) { stampClass = 'err'; stampText = status + ' SERVER ERROR'; }
  else if (status === 0) { stampClass = 'err'; stampText = 'NO RESPONSE'; }

  const bodyStr = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
  responseArea.innerHTML = `
    <span class="stamp ${stampClass}">${stampText}</span>
    <div class="resp-meta"><b>${method}</b> ${url} &nbsp;·&nbsp; ${elapsed}ms</div>
    <pre class="resp-body">${syntaxHighlight(bodyStr || '(empty response)')}</pre>
  `;
}

addBtn.addEventListener('click', () => openForm('create'));
refreshBtn.addEventListener('click', () => fetchList(state.activeEntity, true));

function renderAll(){
  renderSettingsStrip();
  renderTabs();
  toolbarTitle.textContent = entities[state.activeEntity].label;
  formWrap.innerHTML = '';
  if (state.loaded[state.activeEntity]) renderTable();
  else tableWrap.innerHTML = `<div class="empty-note">Loading…</div>`;
}

renderAll();
ensureLoaded(state.activeEntity);
</script>
</body>
</html>