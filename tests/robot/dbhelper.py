import hashlib
import sqlite3

TEKNIS = {"sessions", "cache", "cache_locks", "jobs", "job_batches",
          "failed_jobs", "migrations", "password_reset_tokens", "sqlite_sequence"}


def snapshot_database(path):
    """Sidik jari seluruh isi tabel aplikasi (tabel teknis diabaikan)."""
    con = sqlite3.connect(path)
    try:
        tabel = [r[0] for r in con.execute(
            "select name from sqlite_master where type='table' order by name")]
        h = hashlib.md5()
        for t in tabel:
            if t in TEKNIS:
                continue
            rows = con.execute(f'select * from "{t}" order by 1').fetchall()
            h.update(t.encode())
            h.update(repr(rows).encode())
        return h.hexdigest()
    finally:
        con.close()

        

def halaman_ditolak(path, url, txt):
    """True jika pengguna dialihkan dari path itu, atau halaman memuat penanda penolakan."""
    kata = ["forbidden", "not found", "unauthorized"]
    return (path not in url) or any(k in txt.lower() for k in kata)