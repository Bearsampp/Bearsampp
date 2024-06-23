/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

async function getSummaryStatus() {
    const url = AJAX_URL;
    const proc = 'summary';
    const senddata = new URLSearchParams();
    senddata.append(`proc`, proc);
    const options = {
        method: 'POST',
        body: senddata
    }
    let response = await fetch(url, options);
    if (!response.ok) {
        console.log('Error receiving from ajax.php');
    } else {
        let myajaxresponse = await response.text();
        let data;
        try {
            data = JSON.parse(myajaxresponse);
        } catch (error) {
            console.error('Failed to parse response:', error);
        }
        let q = document.querySelector('.summary-binapache');
        let ql = q.querySelector('.loader');
        ql.remove();
        q.insertAdjacentHTML('beforeend', data.binapache);

        q = document.querySelector('.summary-binfilezilla');
        ql = q.querySelector('.loader');
        ql.remove();
        q.insertAdjacentHTML('beforeend', data.binfilezilla);

        q = document.querySelector('.summary-binxlight');
        ql = q.querySelector('.loader');
        ql.remove();
        q.insertAdjacentHTML('beforeend', data.binxlight);

        q = document.querySelector('.summary-binmailhog');
        ql = q.querySelector('.loader');
        ql.remove();
        q.insertAdjacentHTML('beforeend', data.binmailhog);

        q = document.querySelector('.summary-binmariadb');
        ql = q.querySelector('.loader');
        ql.remove();
        q.insertAdjacentHTML('beforeend', data.binmariadb);

        q = document.querySelector('.summary-binmysql');
        ql = q.querySelector('.loader');
        ql.remove();
        q.insertAdjacentHTML('beforeend', data.binmysql);

        q = document.querySelector('.summary-binpostgresql');
        q.insertAdjacentHTML('beforeend', data.binpostgresql);
        ql = q.querySelector('.loader');
        ql.remove();

        q = document.querySelector('.summary-binmemcached');
        ql = q.querySelector('.loader');
        ql.remove();
        q.insertAdjacentHTML('beforeend', data.binmemcached);

        q = document.querySelector('.summary-binnodejs');
        ql = q.querySelector('.loader');
        ql.remove();
        q.insertAdjacentHTML('beforeend', data.binnodejs);

        q = document.querySelector('.summary-binphp');
        ql = q.querySelector('.loader');
        ql.remove();
        q.insertAdjacentHTML('beforeend', data.binphp);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    if (document.querySelector('.summary').className === 'row summary') {
        getSummaryStatus();
    }
})
