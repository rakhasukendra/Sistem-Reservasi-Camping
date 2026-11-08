import http from 'k6/http';
import { check, sleep, group } from 'k6';

const BASE       = __ENV.BASE_URL || 'http://localhost:8000';
const EMAIL      = __ENV.EMAIL;
const PASS       = __ENV.PASSWORD;
const SMOKE      = __ENV.MODE === 'smoke';
const LOGIN_PATH = '/masuk';

export const options = SMOKE
  ? { vus: 1, iterations: 3, noCookiesReset: true }
  : {
      noCookiesReset: true,   // simpan sesi login antar iterasi
      stages: [
        { duration: '30s', target: 5 },
        { duration: '1m',  target: 5 },
        { duration: '20s', target: 0 },
      ],
      thresholds: {
        http_req_failed:   ['rate<0.05'],
        http_req_duration: ['p(95)<2000'],
        checks:            ['rate>0.95'],
      },
    };

function getToken(res) {
  const doc = res.html();
  let t = doc.find('input[name="_token"]').first().attr('value');
  if (!t) t = doc.find('meta[name="csrf-token"]').first().attr('content');
  return t;
}

function doLogin() {
  const page  = http.get(`${BASE}${LOGIN_PATH}`);
  const token = getToken(page);
  const res   = http.post(`${BASE}${LOGIN_PATH}`, {
    _token: token, email: EMAIL, password: PASS,
  });
  return { page, token, res };
}

export function setup() {
  if (!EMAIL || !PASS) throw new Error('EMAIL dan PASSWORD belum diisi.');
  const { page, token, res } = doLogin();
  if (page.status !== 200) throw new Error(`Halaman ${LOGIN_PATH} tidak bisa dibuka (status ${page.status}).`);
  if (!token) throw new Error('Token CSRF tidak ditemukan.');
  if (res.url.endsWith(LOGIN_PATH)) throw new Error(`Login gagal (status ${res.status}).`);
}

let loggedIn = false;   // nilainya terpisah untuk tiap pengguna virtual

export default function () {
  if (!loggedIn) {
    group('Login (sekali per pengguna)', () => {
      const { res } = doLogin();
      loggedIn = res.status === 200 && !res.url.endsWith(LOGIN_PATH);
      check(res, {
        'login berhasil': () => loggedIn,
        'tidak kena 419 atau 429': (r) => r.status !== 419 && r.status !== 429,
      });
    });
    if (!loggedIn) { sleep(10); return; }   // kalau ditolak, tunggu dulu
  }

  group('Beranda', () => {
    const r = http.get(`${BASE}/`);
    check(r, { 'beranda 200': (x) => x.status === 200 });
  });

  group('Profil (butuh login)', () => {
    const r = http.get(`${BASE}/profil`);
    check(r, { 'profil 200 dan masih login': (x) => x.status === 200 && !x.url.endsWith(LOGIN_PATH) });
  });

  sleep(1);
}