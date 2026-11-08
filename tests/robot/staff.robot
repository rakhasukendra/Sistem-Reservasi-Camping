*** Settings ***
Resource            common.resource
Test Teardown       Close All Browsers

*** Test Cases ***
Staff Dapat Membuka Halaman Kerja
    Pastikan Akun Terisi    ${STAFF_EMAIL}    ${STAFF_PASSWORD}
    Login Sebagai    ${STAFF_EMAIL}    ${STAFF_PASSWORD}
    FOR    ${p}    IN    /staff    /staff/sewa    /staff/stok    /staff/artikel    /staff/dashboard
        Halaman Harus Normal    ${p}
    END

Staff Dapat Membuka Form Edit Alat
    Pastikan Akun Terisi    ${STAFF_EMAIL}    ${STAFF_PASSWORD}
    Login Sebagai    ${STAFF_EMAIL}    ${STAFF_PASSWORD}
    Halaman Harus Normal    /staff/stok
    ${link}=    Set Variable    css=a[href*="/staff/alat/"][href$="/edit"]
    ${ada}=    Run Keyword And Return Status    Page Should Contain Element    ${link}
    IF    not ${ada}
        Fail    Tidak ada link edit alat di /staff/stok
    END
    ${href}=    Get Element Attribute    ${link}    href
    Go To    ${href}
    Wait Until Page Contains Element    css=form[action*="/staff/alat/"]
    Capture Page Screenshot

Staff Dapat Memutuskan Reservasi
    Pastikan Akun Terisi    ${STAFF_EMAIL}    ${STAFF_PASSWORD}
    Login Sebagai    ${STAFF_EMAIL}    ${STAFF_PASSWORD}
    Go To    ${BASE_URL}/staff/sewa
    ${form}=    Set Variable    form[action*="/keputusan"]
    ${ada}=    Run Keyword And Return Status    Page Should Contain Element    css=${form}
    IF    not ${ada}
        Fail    Tidak ada reservasi yang menunggu keputusan di /staff/sewa. Jalankan booking.robot dulu.
    END
    ${sebelum}=    Snapshot Database    ${DB}
    Isi Form Otomatis    ${form}
    Klik Kirim Form    ${form}
    ${sesudah}=    Snapshot Database    ${DB}
    Should Not Be Equal    ${sebelum}    ${sesudah}    msg=Data di database tidak berubah setelah keputusan dikirim

Staff Tidak Boleh Membuka Halaman Admin
    Pastikan Akun Terisi    ${STAFF_EMAIL}    ${STAFF_PASSWORD}
    Login Sebagai    ${STAFF_EMAIL}    ${STAFF_PASSWORD}
    Akses Harus Ditolak    /admin/penilaian-staff

Pelanggan Tidak Boleh Membuka Halaman Staff Dan Admin
    Login Sebagai    ${CUSTOMER_EMAIL}    ${CUSTOMER_PASSWORD}
    Akses Harus Ditolak    /staff/sewa
    Akses Harus Ditolak    /staff/stok
    Akses Harus Ditolak    /admin/penilaian-staff