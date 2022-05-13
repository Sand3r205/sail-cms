$(document).ready(function() {
    $('#login').on('click', () => {
        var username = prompt("Username");
        var password = prompt("Password");

        fetch('/cms/api/login.php', {
            method: 'POST',
            body: JSON.stringify({ username, password })
        }).then(response => {
            return response.json();
        }).then(data => {
            if (data) {
                document.cookie = "token=" + data;
                location.reload();
            } else {
                alert('Wrong username and/or password');
            }
        })
    });

    $('#logout').on('click', () => {
        fetch('/cms/api/logout.php', {
            method: 'POST',
            body: JSON.stringify({ token: document.cookie.split('=')[1] })
        }).then(response => {
            location.reload();
        })
    })
});