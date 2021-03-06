(function () {

    var form = document.querySelector('form');

    var url = 'https://s4kxk6t0y4.execute-api.us-east-1.amazonaws.com/prod/send_mail';

    var get = function (field) {
        var element = document.querySelector('[name=' + field + ']');
        if (element) {
            return element.value;
        } else {
            return '';
        }
    };

    if (form) {
        form.addEventListener('submit', function (event) {

            event.preventDefault();

            var r = new XMLHttpRequest();
            r.open("POST", url, true);
            r.onreadystatechange = function () {
                if (r.readyState != 4) return;
                if (r.status === 200) {
                    window.location = 'bedankt-voor-uw-vraag.html';
                } else {
                    alert('Er is iets mis gegaan bij het versturen van uw bericht. Probeer het later nogmaals.');
                }
            };
            r.send(JSON.stringify({
                name_student: get('name_student'),
                name: get('name'),
                email: get('email'),
                subject: get('subject'),
                year: get('year'),
                message: get('message'),
                level: get('level'),
                phone: get('phone')
            }));
        });
    }
})();