const form = document.querySelector('form[action$="/reservasi"]');
if (!form) { return 'FORM_TIDAK_ADA'; }
const fire = (el) => { el.dispatchEvent(new Event('input', {bubbles: true})); el.dispatchEvent(new Event('change', {bubbles: true})); };
const dates = form.querySelectorAll('input[type="date"]');
if (dates.length > 0) { dates[0].value = '__MULAI__'; fire(dates[0]); }
if (dates.length > 1) { dates[1].value = '__SELESAI__'; fire(dates[1]); }
form.querySelectorAll('[required]').forEach((el) => {
  if (el.type === 'checkbox') { el.checked = true; fire(el); return; }
  if (el.type === 'date' || el.value) { return; }
  if (el.tagName === 'SELECT') {
    const o = Array.from(el.options).find((x) => x.value);
    if (o) { el.value = o.value; }
  } else if (el.type === 'tel') { el.value = '081234567890'; }
  else if (el.type === 'email') { el.value = 'user@demo.test'; }
  else if (el.type === 'number') { el.value = '1'; }
  else { el.value = 'Uji Robot'; }
  fire(el);
});
return 'jumlah_input_tanggal=' + dates.length;