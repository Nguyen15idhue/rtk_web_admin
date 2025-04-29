(function(window){
    function ensureJsonResponse(response){
        const ct = response.headers.get('content-type') || '';
        return response.text().then(text => {
            if (!response.ok) {
                let msg = `HTTP ${response.status}: ${response.statusText}`;
                try { const err = JSON.parse(text); if (err.message) msg += ' – ' + err.message; }
                catch{}
                throw new Error(msg);
            }
            if (!ct.includes('application/json')) {
                throw new Error('Expected JSON, got: ' + text);
            }
            return JSON.parse(text);
        });
    }

    function getJson(url){
        return fetch(url, { credentials: 'same-origin' })
               .then(ensureJsonResponse);
    }

    function postJson(url, data){
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(ensureJsonResponse);
    }

    function postForm(url, formData){
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
        .then(ensureJsonResponse);
    }

    window.api = { getJson, postJson, postForm };
})(window);
