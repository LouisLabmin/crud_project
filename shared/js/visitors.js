// shared/js/visitors.js
// This JavaScript file is responsible for logging visitor information asynchronously on every page load. 
// It sends a POST request to a PHP script that records the visitor's IP address and other relevant data in the database. 
// This allows the site owner to track visits and analyze traffic patterns without impacting the user experience, as the logging happens in the background
//  without any page reloads or interruptions.


console.log("visitors.js LOADED");

// Trigger visitor logging asynchronously
fetch("/shared/log_visit.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    }
})
.then(response => response.text())
.then(result => {
    console.log("Visit logged:", result);
})
.catch(error => {
    console.error("Visit log failed:", error);
});
