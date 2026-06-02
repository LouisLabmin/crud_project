// shared/js/visitors.js
// Logs visitor and updates live visitor count via AJAX

console.log("visitors.js LOADED ✅");

// Step 1: log visit
fetch(window.APP_CONFIG.base + "/shared/ajax/log_visit.php", {
    method: "POST"
})
.then(res => res.json())

// Step 2: validate logging
.then(res => {
    if (res.status !== 'success') {
        throw new Error('Visit logging failed');
    }

    // Step 3: fetch updated count
    return fetch(window.APP_CONFIG.base + "/shared/ajax/get_visit_count.php");
})

// Step 4: parse count
.then(res => res.json())

// Step 5: update UI
.then(data => {
    if (data.status === 'success') {
        const el = document.getElementById('visitor_count');
        if (el) {
            el.textContent = data.total;
        }
    }
})

// Step 6: error handling
.catch(err => {
    console.warn("Visitor tracking error:", err);
});