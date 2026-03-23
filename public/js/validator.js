
window.validateUniqueAndSuggest = function({
    input,
    table,
    column,
    fieldKey,
    csrfToken,
    feedbackClass,
    endpoint,
    minLength = 2,
    strict = false,
    validity,
    updateSubmitState
}) {
    let feedback = input.parentElement.querySelector(`.${feedbackClass}`);
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = feedbackClass;
        input.parentElement.appendChild(feedback);
    }

    let timer;
    input.addEventListener('input', () => {
        const value = input.value.trim();
        clearTimeout(timer);
        feedback.textContent = '';
        input.classList.remove('valid', 'invalid');
        validity[fieldKey] = false;
        updateSubmitState();
        if (value.length < minLength) return;

        timer = setTimeout(() => {
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ table, column, query: value })
            })
            .then(res => res.ok ? res.json() : Promise.reject(res.status))
            .then(data => {
                let invalid = false;
                if (data.results && data.results.length > 0) {
                    if (strict) {
                        invalid = data.results.some(r => r.toLowerCase().includes(value.toLowerCase()));
                    } else {
                        invalid = data.results.includes(value);
                    }
                }

                if (!invalid) {
                    input.classList.add('valid');
                    feedback.textContent = 'Available';
                    feedback.style.color = 'green';
                    validity[fieldKey] = true;
                } else {
                    input.classList.add('invalid');
                    feedback.innerHTML = data.results.map(n => `<div>${n}</div>`).join('');
                    feedback.style.color = 'red';
                    validity[fieldKey] = false;
                }

                updateSubmitState();
            })
            .catch(() => {
                feedback.textContent = '';
                input.classList.remove('valid', 'invalid');
                validity[fieldKey] = false;
                updateSubmitState();
            });
        }, 250);
    });
}
