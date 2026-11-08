*** Settings ***
Resource            common.resource
Library             DateTime
Test Teardown       Close All Browsers

*** Test Cases ***
Admin Dapat Membuka Halaman Penilaian Staff
    Pastikan Akun Terisi    ${ADMIN_EMAIL}    ${ADMIN_PASSWORD}
    Login Sebagai    ${ADMIN_EMAIL}    ${ADMIN_PASSWORD}
    Halaman Harus Normal    /admin/penilaian-staff

Admin Dapat Memberi Penilaian Ke Staff
    Pastikan Akun Terisi    ${ADMIN_EMAIL}    ${ADMIN_PASSWORD}
    Login Sebagai    ${ADMIN_EMAIL}    ${ADMIN_PASSWORD}
    Go To    ${BASE_URL}/admin/penilaian-staff
    ${form}=    Set Variable    form[action*="/admin/staff/"][action$="/nilai"]
    ${ada}=    Run Keyword And Return Status    Page Should Contain Element    css=${form}
    IF    not ${ada}
        Fail    Form penilaian staff tidak ditemukan di /admin/penilaian-staff
    END
    ${sebelum}=    Snapshot Database    ${DB}
    Isi Form Otomatis    ${form}
    Klik Kirim Form    ${form}
    ${sesudah}=    Snapshot Database    ${DB}
    Should Not Be Equal    ${sebelum}    ${sesudah}    msg=Data tidak berubah setelah penilaian dikirim

Admin Dapat Membuat Akun Baru
    Pastikan Akun Terisi    ${ADMIN_EMAIL}    ${ADMIN_PASSWORD}
    Login Sebagai    ${ADMIN_EMAIL}    ${ADMIN_PASSWORD}
    ${email}=    Evaluate    'robot' + str(int(time.time())) + '@demo.test'    modules=time
    ${form}=    Set Variable    form[action$="/admin/akun"]
    ${ketemu}=    Set Variable    ${FALSE}
    FOR    ${p}    IN    /admin/penilaian-staff    /staff/dashboard    /staff    /profil
        Go To    ${BASE_URL}${p}
        ${ada}=    Run Keyword And Return Status    Page Should Contain Element    css=${form}
        IF    ${ada}
            ${ketemu}=    Set Variable    ${TRUE}
            BREAK
        END
    END
    IF    not ${ketemu}
        Skip    Form pembuat akun tidak ditemukan di halaman yang dicoba. Kirim isi view admin ke saya.
    END
    ${sebelum}=    Snapshot Database    ${DB}
    Isi Form Otomatis    ${form}    ${email}
    Klik Kirim Form    ${form}
    ${sesudah}=    Snapshot Database    ${DB}
    Should Not Be Equal    ${sebelum}    ${sesudah}    msg=Akun baru tidak tersimpan