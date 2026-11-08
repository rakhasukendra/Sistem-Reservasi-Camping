const form = document.querySelector('__SELECTOR__');
if (!form) { return 'FORM_TIDAK_ADA'; }
const fire = (el) => { el.dispatchEvent(new Event('input', {bubbles: true})); el.dispatchEvent(new Event('change', {bubbles: true})); };
const hari = new Date(Date.now() + 7 * 86400000).toISOString().slice(0, 10);
let terisi = 0;
form.querySelectorAll('input, select, textarea').forEach((el) => {
  if (el.disabled || ['hidden', 'submit', 'button', 'file'].includes(el.type)) { return; }
  if (el.type === 'checkbox' || el.type === 'radio') {
    if (el.required) { el.checked = true; fire(el); terisi++; }
    return;
  }
  if (!el.required || el.value) { return; }
  if (el.tagName === 'SELECT') {
    const o = Array.from(el.options).find((x) => x.value);
    if (o) { el.value = o.value; }
  } else if (el.type === 'date') { el.value = hari; }
  else if (el.type === 'tel') { el.value = '081234567890'; }
  else if (el.type === 'email') { el.value = '__EMAIL__'; }
  else if (el.type === 'number') { el.value = '1'; }
  else if (el.type === 'password') { el.value = 'Uji#Robot2026'; }
  else { el.value = 'Uji Robot'; }
  fire(el);
  terisi++;
});
return 'terisi=' + terisi;