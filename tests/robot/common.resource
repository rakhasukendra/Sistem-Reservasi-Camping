*** Settings ***
Library     SeleniumLibrary    timeout=15s
Library     OperatingSystem
Library     String
Library     dbhelper.py
Resource    akun.resource

*** Variables ***
${DB}       ${EXECDIR}${/}database${/}database.sqlite
@{KATA_ERROR}    Server Error    Whoops    Not Found    Forbidden    Page Expired    Illuminate

*** Keywords ***
Pastikan Akun Terisi
    [Arguments]    ${email}    ${password}
    Should Not Start With    ${email}    GANTI    msg=Isi akun dulu di tests/robot/akun.resource
    Should Not Start With    ${password}    GANTI    msg=Isi akun dulu di tests/robot/akun.resource

Login Sebagai
    [Arguments]    ${email}    ${password}
    Open Browser    ${BASE_URL}/masuk    ${BROWSER}    options=add_argument("--incognito")
    Maximize Browser Window
    Input Text    name=email    ${email}
    Input Text    name=password    ${password}
    Click Element    css=form[action$="/masuk"] [type="submit"]
    Wait Until Location Does Not Contain    /masuk    message=Login gagal untuk ${email}. Cek akun.resource

Halaman Harus Normal
    [Arguments]    ${path}
    Go To    ${BASE_URL}${path}
    ${url}=    Get Location
    Should Not Contain    ${url}    /masuk    msg=Dialihkan ke login saat membuka ${path}
    ${txt}=    Get Text    css=body
    FOR    ${k}    IN    @{KATA_ERROR}
        Should Not Contain    ${txt}    ${k}    msg=Halaman ${path} menampilkan '${k}'
    END
    Capture Page Screenshot

Akses Harus Ditolak
    [Arguments]    ${path}
    Go To    ${BASE_URL}${path}
    ${url}=    Get Location
    ${txt}=    Get Text    css=body
    Capture Page Screenshot
    ${ditolak}=    Halaman Ditolak    ${path}    ${url}    ${txt}
    Should Be True    ${ditolak}    msg=${path} seharusnya ditolak, tapi halaman terbuka (URL: ${url})

Isi Form Otomatis
    [Arguments]    ${selector}    ${email}=user@demo.test
    ${js}=    Get File    ${EXECDIR}${/}tests${/}robot${/}fill_form.js
    ${js}=    Replace String    ${js}    __SELECTOR__    ${selector}
    ${js}=    Replace String    ${js}    __EMAIL__    ${email}
    ${hasil}=    Execute Javascript    ${js}
    Log    Hasil pengisi form: ${hasil}    console=True
    Should Not Be Equal    ${hasil}    FORM_TIDAK_ADA

Klik Kirim Form
    [Arguments]    ${selector}
    Execute Javascript    const f=document.querySelector('${selector}'); const b=f.querySelector('button:not([type="button"]),input[type="submit"]'); if(b){b.click();}else{f.requestSubmit();}
    Sleep    2s
    Wait For Condition    return document.readyState == 'complete'
    Capture Page Screenshot