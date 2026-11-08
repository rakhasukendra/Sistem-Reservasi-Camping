*** Settings ***
Library    SeleniumLibrary    timeout=15s
Library    OperatingSystem
Library    String
Library    DateTime
Resource   akun.resource

*** Test Cases ***
Pelanggan Dapat Mengajukan Reservasi
    ${sebelum}=    Hitung Reservasi Di Database
    Buka Browser
    Login Sebagai Pelanggan
    Tambah Barang Pertama Ke Keranjang
    Buka Keranjang
    Buka Form Reservasi
    Isi Dan Kirim Form Reservasi    ${sebelum}
    Reservasi Harus Tersimpan    ${sebelum}
    Sleep    3s
    [Teardown]    Close All Browsers

*** Keywords ***
Buka Browser
    Open Browser    ${BASE_URL}/masuk    ${BROWSER}    options=add_argument("--incognito")
    Maximize Browser Window

Login Sebagai Pelanggan
    Go To    ${BASE_URL}/masuk
    Input Text    name=email    ${CUSTOMER_EMAIL}
    Input Text    name=password    ${CUSTOMER_PASSWORD}
    Click Element    css=form[action$="/masuk"] [type="submit"]
    Wait Until Location Does Not Contain    /masuk

Tambah Barang Pertama Ke Keranjang
    Go To    ${BASE_URL}/
    Wait Until Page Contains Element    css=form[action*="/keranjang/tambah/"]
    Scroll Element Into View    css=form[action*="/keranjang/tambah/"] [type="submit"]
    Click Element    css=form[action*="/keranjang/tambah/"] [type="submit"]
    Wait For Condition    return document.readyState == 'complete'

Buka Keranjang
    Go To    ${BASE_URL}/keranjang
    Page Should Contain Element    css=form[action*="/keranjang/hapus/"]
    Capture Page Screenshot    keranjang.png

Buka Form Reservasi
    Go To    ${BASE_URL}/reservasi/buat
    Wait Until Page Contains Element    css=form[action$="/reservasi"]

Isi Dan Kirim Form Reservasi
    [Arguments]    ${sebelum}
    ${geser}=      Evaluate    7 + 3 * ${sebelum}
    ${geser2}=     Evaluate    ${geser} + 2
    ${mulai}=      Get Current Date    increment=${geser} days    result_format=%Y-%m-%d
    ${selesai}=    Get Current Date    increment=${geser2} days    result_format=%Y-%m-%d
    ${js}=    Get File    ${EXECDIR}${/}tests${/}robot${/}fill_booking.js
    ${js}=    Replace String    ${js}    __MULAI__    ${mulai}
    ${js}=    Replace String    ${js}    __SELESAI__    ${selesai}
    ${hasil}=    Execute Javascript    ${js}
    Log    Hasil pengisi form: ${hasil}    console=True
    Should Not Be Equal    ${hasil}    FORM_TIDAK_ADA
    Capture Page Screenshot    form-terisi.png
    Click Element    css=form[action$="/reservasi"] [type="submit"]
    Wait Until Location Does Not Contain    /reservasi/buat
    Capture Page Screenshot    setelah-submit.png

Hitung Reservasi Di Database
    ${db}=    Set Variable    ${EXECDIR}${/}database${/}database.sqlite
    File Should Exist    ${db}
    ${n}=    Evaluate    sqlite3.connect($db).execute("select count(*) from bookings").fetchone()[0]    modules=sqlite3
    RETURN    ${n}

Reservasi Harus Tersimpan
    [Arguments]    ${sebelum}
    ${sesudah}=    Hitung Reservasi Di Database
    ${harapan}=    Evaluate    ${sebelum} + 1
    Should Be Equal As Integers    ${sesudah}    ${harapan}
    ...    msg=Jumlah reservasi tidak bertambah (sebelum=${sebelum}, sesudah=${sesudah})